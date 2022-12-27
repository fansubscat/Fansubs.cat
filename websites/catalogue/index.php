<?php
require_once("db.inc.php");
require_once("parsedown.inc.php");

$header_tab=(!empty($_GET['page']) ? $_GET['page'] : '');
$header_page_title='';

switch ($header_tab) {
	case $config['filmsoneshots_slug_internal']:
		$header_page_title=$config['filmsoneshots'];
		$header_url=$config['filmsoneshots_slug'].'/';
		break;
	case $config['serialized_slug_internal']:
		$header_page_title=$config['serialized'];
		$header_url=$config['serialized_slug'].'/';
		break;
	case 'search':
		$header_page_title='Resultats de la cerca';
		$header_url='cerca/'.(!empty($_GET['query']) ? urlencode(urlencode($_GET['query'])) : '');
		break;
	default:
		$header_page_title='';
		$header_url='';
		break;
}

$header_social = array(
	'title' => (!empty($header_page_title) ? $header_page_title.' | ' : '').$config['site_title'],
	'url' => $config['base_url'].'/'.$header_url,
	'description' => $config['site_description'],
	'image' => $config['preview_image']
);

require_once('header.inc.php');

if (!empty($site_message) || !empty($is_fools_day)){
?>
				<div data-nosnippet class="section">
					<div class="site-message"><?php echo !empty($is_fools_day) ? 'Estem millorant el disseny de la pàgina. De moment hi hem afegit Comic Sans, que li donarà un toc més modern. <a href="'.$static_url.'/various/innocents.png" target="_blank" style="color: black">Més informació</a>' : $site_message; ?></div>
				</div>
<?php
}

if (is_robot()){
	if ($config['items_type']=='liveaction') {
		$number=25;
	} else {
		$number=50;
	}
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt FROM series s WHERE s.type='${config['items_type']}' AND EXISTS (SELECT id FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
?>
				<div class="section">
					<div class="site-message"><?php printf($config['site_robot_message'], $totalnumber); ?></div>
				</div>
<?php
}

$max_items=24;

$cookie_viewed_files = get_cookie_viewed_files_ids();

$base_query="SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT f.type ORDER BY f.type SEPARATOR '|') fansub_type, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0 AND ls.id NOT IN (".(count($cookie_viewed_files)>0 ? implode(',',$cookie_viewed_files) : '0').")) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";
$query_portion_limit_to_non_hidden = "(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0";

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot()) ? " AND v.status<>5 AND v.status<>4" : "").(!empty($_COOKIE['show_missing']) ? "" : " AND v.is_missing_episodes=0").((empty($_COOKIE['show_hentai']) && !is_robot()) ? " AND (s.rating<>'XXX' OR s.rating IS NULL)" : "").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

switch ($header_tab){
	case $config['filmsoneshots_slug_internal']:
		$sections=array($config['section_moviesoneshots']);
		$descriptions=array($config['section_moviesoneshots_desc']);
		$queries=array(
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND s.subtype='${config['filmsoneshots_db']}'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		break;
	case $config['serialized_slug_internal']:
		$sections=array($config['section_serialized']);
		$descriptions=array($config['section_serialized_desc']);
		$queries=array(
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND s.subtype='${config['serialized_db']}'$cookie_extra_conditions GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		break;
	case 'search':
		$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
		if (!empty($query)){
			query("INSERT INTO search_history (query,day) VALUES ('".escape($_GET['query'])."','".date('Y-m-d')."')");
		}
		$sections=array($config['section_search_results']);
		$descriptions=array(sprintf($config['section_search_results_desc'], $query, "%s"));
		$spaced_query=$query;
		$query = str_replace(" ", "%", $query);
		$queries=array(
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.keywords OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name='$spaced_query')) GROUP BY s.id ORDER BY s.name ASC");
		$specific_version=array(FALSE);
		$type=array('static');
		break;
	default:
		$result = query("SELECT b.series_id, IFNULL(MAX(b.total_views),0) max_views FROM (SELECT a.series_id, SUM(a.views) total_views FROM (SELECT SUM(vi.views) views, f.version_id, s.id series_id, f.episode_id FROM views vi LEFT JOIN file f ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE (SELECT COUNT(*) FROM version v WHERE s.type='${config['items_type']}' AND v.series_id=s.id AND v.is_hidden=0)>0 AND f.episode_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, b.series_id DESC");
		$in_clause='0';
		while ($row = mysqli_fetch_assoc($result)){
			$in_clause.=','.$row['series_id'];
		}
		mysqli_free_result($result);
		$sections=array($config['section_advent'], $config['section_featured'], $config['section_last_updated'], $config['section_last_completed'], $config['section_random'], $config['section_popular'], $config['section_more_recent'], $config['section_best_rated']);
		$descriptions=array($config['section_advent_desc'], $config['section_featured_desc'], $config['section_last_updated_desc'], $config['section_last_completed_desc'], $config['section_random_desc'], $config['section_popular_desc'], $config['section_more_recent_desc'], $config['section_best_rated_desc']);
		$recommendations_subquery = "SELECT version_id FROM recommendation r LEFT JOIN version v ON r.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='${config['items_type']}'";
		if ($is_fools_day) {
			$sections[1]=$config['section_fools'];
			$descriptions[1]=$config['section_fools_desc'];
			//Worst rated completed or semi completed animes
			$fools_day_result = query("SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status IN (1,3) AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 ORDER BY sr.score ASC LIMIT 10");
			$fools_day_items = array();
			while ($rowfd = mysqli_fetch_assoc($fools_day_result)) {
				$fools_day_items[] = $rowfd['id'];
			}
			$recommendations_subquery = implode(',',$fools_day_items);
		} else if (date('m-d')=='04-23') { // Sant Jordi
			$sections[1]=$config['section_sant_jordi'];
			$descriptions[1]=$config['section_sant_jordi_desc'];
			//Best rated completed and featurable animes of genres Romance, Boys Love and Girls Love
			$sant_jordi_result = query("SELECT DISTINCT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status=1 AND vr.is_featurable=1 AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 AND sr.id IN (SELECT rsg.series_id FROM rel_series_genre rsg WHERE rsg.genre_id IN (7,23,38)) ORDER BY sr.score DESC LIMIT 10");
			$sant_jordi_items = array();
			while ($rowsj = mysqli_fetch_assoc($sant_jordi_result)) {
				$sant_jordi_items[] = $rowsj['id'];
			}
			$recommendations_subquery = implode(',',$sant_jordi_items);
		} else if (date('m-d')=='10-31' || date('m-d')=='11-01') { // Tots Sants
			$sections[1]=$config['section_tots_sants'];
			$descriptions[1]=$config['section_tots_sants_desc'];
			//Best rated completed and featurable animes of genre Horror
			$tots_sants_result = query("SELECT DISTINCT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status=1 AND vr.is_featurable=1 AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 AND sr.id IN (SELECT rsg.series_id FROM rel_series_genre rsg WHERE rsg.genre_id IN (21)) ORDER BY sr.score DESC LIMIT 10");
			$tots_sants_items = array();
			while ($rowsts = mysqli_fetch_assoc($tots_sants_result)) {
				$tots_sants_items[] = $rowsts['id'];
			}
			$recommendations_subquery = implode(',',$tots_sants_items);
		}
		$queries=array(
			NULL,
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND v.id IN ($recommendations_subquery)$cookie_extra_conditions GROUP BY v.id ORDER BY RAND()",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY v.id ORDER BY last_updated DESC LIMIT $max_items",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND completed_date IS NOT NULL AND 1$cookie_extra_conditions GROUP BY v.id ORDER BY completed_date DESC LIMIT $max_items",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY RAND() LIMIT $max_items",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND s.id IN ($in_clause)$cookie_extra_conditions GROUP BY s.id ORDER BY FIELD(s.id,$in_clause) LIMIT $max_items",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.publish_date DESC LIMIT $max_items",
			$base_query . " WHERE s.type='${config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
		$specific_version=array(FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE);
		$type=array('advent','recommendations', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel');
		break;
}

for ($i=0;$i<count($sections);$i++){
	if ($type[$i]=='advent') {
		if (strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 11:59:59')<=0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<h3 class="section-subtitle"><?php echo $descriptions[$i]; ?></h3>
					<div class="section-content fake-carousel">
						<a class="advent" href="<?php echo $advent_url; ?>" target="_blank"><img src="<?php echo $static_url; ?>/images/advent/header_<?php echo date('Y'); ?>.jpg" alt="Calendari d'advent dels fansubs en català" /></a>
					</div>
				</div>
<?php
		}
		continue;
	}
	$result = query($queries[$i]);
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<h3 class="section-subtitle"><?php echo sprintf($descriptions[$i], mysqli_num_rows($result)); ?></h3>
<?php
	if (mysqli_num_rows($result)==0){
		if ($sections[$i]=='Resultats de la cerca'){
?>
					<div class="section-content fake-carousel"><div><span class="fa fa-search empty-icon"></span><br><br>No s'ha trobat cap element. Prova una altra cerca o explora el catàleg a les pestanyes superiors.</div></div>
<?php
		} else {
?>
					<div class="section-content fake-carousel"><div><span class="fa fa-ban empty-icon"></span><br><br>No s'ha trobat cap element. Prova de reduir el filtre a les opcions de la part superior dreta.</div></div>
<?php
		}
	} else {
		if ($type[$i]=='static') {
			$genres = array();
			while ($row = mysqli_fetch_assoc($result)) {
				if (!empty($row['genres'])) {
					$dbgenres = explode(',',$row['genres']);
					foreach ($dbgenres as $dbgenre) {
						if (!empty($genres[$dbgenre])) {
							$genres[$dbgenre]=$genres[$dbgenre]+1;
						} else {
							$genres[$dbgenre]=1;
						}
					}
				}
			}
			mysqli_data_seek($result, 0);

?>
					<div class="section-content genres-carousel">
						<div>
							<div class="select-genre select-genre-selected" data-genre-id="-1">
								<a>Mostra-ho tot</a>
							</div>
						</div>
<?php
			arsort($genres);
			$resultg = query("SELECT g.id, g.name FROM genre g WHERE g.id IN (0".implode(',',array_keys($genres)).") ORDER BY FIELD(g.id,0".implode(',',array_keys($genres)).")");
			while ($rowg = mysqli_fetch_assoc($resultg)) {
?>
						<div>
							<div class="select-genre" data-genre-id="<?php echo $rowg['id']; ?>">
								<a><?php echo $rowg['name'].' ('.$genres[$rowg['id']].')'; ?></a>
							</div>
						</div>
<?php
			}
			mysqli_free_result($resultg);
?>
					</div>
<?php
		}
?>
					<div class="section-content<?php echo $type[$i]=='carousel' ? ' carousel' : ($type[$i]=='recommendations' ? ' recommendations' : ' flex wrappable catalog'); ?>">
<?php
		while ($row = mysqli_fetch_assoc($result)){
			if (!empty($row['genres']) && $type[$i]=='static') {
				$genres = ' genre-'.implode(' genre-', explode(',',$row['genres']));
			} else {
				$genres = "";
			}
?>
						<div class="status-<?php echo get_status($row['best_status']); ?><?php echo $genres; ?>">
<?php
			if ($type[$i]=='recommendations') {
				print_featured_item($row, $specific_version[$i]);
			} else {
				print_carousel_item($row, $specific_version[$i]);
			}
?>
						</div>
<?php
		}
?>
					</div>
<?php
	}
	mysqli_free_result($result);
?>
				</div>
<?php
	//Search case: add manga
	if ($header_tab=='search') {
		$result = query("SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND v.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR '|') fansub_name, GROUP_CONCAT(DISTINCT f.type ORDER BY f.type SEPARATOR '|') fansub_type, GROUP_CONCAT(DISTINCT sg.genre_id) genres, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type<>'${config['items_type']}' AND s.type<>'liveaction' AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.keywords OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name='$spaced_query')) GROUP BY s.id ORDER BY s.name ASC");
		if (mysqli_num_rows($result)>0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $config['section_search_other_results']; ?></h2>
					<h3 class="section-subtitle"><?php echo mysqli_num_rows($result)==1 ? sprintf($config['section_search_other_results_desc_s'], mysqli_num_rows($result)) : sprintf($config['section_search_other_results_desc_p'], mysqli_num_rows($result)); ?></h3>
					<div class="section-content flex wrappable catalog">
<?php
			while ($row = mysqli_fetch_assoc($result)){
				if (!empty($row['genres'])) {
					$genres = ' genre-'.implode(' genre-', explode(',',$row['genres']));
				} else {
					$genres = "";
				}
?>
						<div class="status-<?php echo get_status($row['best_status']); ?><?php echo $genres; ?>">
<?php
				print_carousel_item($row, FALSE, FALSE);
?>
						</div>
<?php
			}
		}
?>
					</div>
				</div>
<?php
		mysqli_free_result($result);
	}
}

require_once('footer.inc.php');
?>

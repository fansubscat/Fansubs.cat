<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

if (!empty($_GET['hentai'])) {
	$hentai_subquery=" AND s.rating='XXX'";
} else {
	$hentai_subquery=" AND (s.rating IS NULL OR s.rating<>'XXX')";
}

if (!empty($site_message) || !empty($is_fools_day)){
?>
				<div data-nosnippet class="section">
					<div class="site-message"><?php echo !empty($is_fools_day) ? 'Estem millorant el disseny de la pàgina. De moment hi hem afegit Comic Sans, que li donarà un toc més modern. <a href="'.$static_url.'/various/innocents.png" target="_blank" style="color: black">Més informació</a>.' : $site_message; ?></div>
				</div>
<?php
}

$max_items=24;

$cookie_viewed_files = get_cookie_viewed_files_ids();

$base_query="SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id) ORDER BY v.id,f.name SEPARATOR '|') fansub_info, GROUP_CONCAT(DISTINCT sg.genre_id) genres, GROUP_CONCAT(DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑') ORDER BY g.name SEPARATOR ' • ') genre_names, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0 AND ls.id NOT IN (".(count($cookie_viewed_files)>0 ? implode(',',$cookie_viewed_files) : '0').")) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id";
$query_portion_limit_to_non_hidden = "(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND v.is_hidden=0$hentai_subquery";

$cookie_fansub_ids = get_cookie_fansub_ids();

$cookie_extra_conditions = ((empty($_COOKIE['show_cancelled']) && !is_robot()) ? " AND v.status<>5 AND v.status<>4" : "").(!empty($_COOKIE['show_missing']) ? "" : " AND v.is_missing_episodes=0").(count($cookie_fansub_ids)>0 ? " AND v.id NOT IN (SELECT v2.id FROM version v2 LEFT JOIN rel_version_fansub vf2 ON v2.id=vf2.version_id WHERE vf2.fansub_id IN (".implode(',',$cookie_fansub_ids).") AND NOT EXISTS (SELECT vf3.version_id FROM rel_version_fansub vf3 WHERE vf3.version_id=vf2.version_id AND vf3.fansub_id NOT IN (".implode(',',$cookie_fansub_ids).")))" : '');

if (!empty($_GET['search'])) {
	$query = (!empty($_GET['query']) ? escape($_GET['query']) : "");
	$sections=array($cat_config['section_search_results']);
	$spaced_query=$query;
	$query = str_replace(" ", "%", $query);
	$queries=array(
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name='$spaced_query')) GROUP BY s.id ORDER BY s.name ASC");
	$specific_version=array(FALSE);
	$type=array('static');
} else {
	$result = query("SELECT b.series_id, IFNULL(MAX(b.total_views),0) max_views FROM (SELECT a.series_id, SUM(a.views) total_views FROM (SELECT SUM(vi.views) views, f.version_id, s.id series_id, f.episode_id FROM views vi LEFT JOIN file f ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE (SELECT COUNT(*) FROM version v WHERE s.type='${cat_config['items_type']}' AND v.series_id=s.id AND v.is_hidden=0)>0 AND f.episode_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, b.series_id DESC");
	$in_clause='0';
	while ($row = mysqli_fetch_assoc($result)){
		$in_clause.=','.$row['series_id'];
	}
	mysqli_free_result($result);
	$sections=array($cat_config['section_advent'], $cat_config['section_featured'], $cat_config['section_last_updated'], $cat_config['section_last_completed'], $cat_config['section_random'], $cat_config['section_popular'], $cat_config['section_more_recent'], $cat_config['section_best_rated']);
	$recommendations_subquery = "SELECT version_id FROM recommendation r LEFT JOIN version v ON r.version_id=v.id LEFT JOIN series s ON v.series_id=s.id WHERE s.type='${cat_config['items_type']}'";
	$special_day=NULL;
	if ($is_fools_day) {
		$special_day='fools';
		$sections[1]=$cat_config['section_fools'];
		//Worst rated completed or semi completed animes
		$fools_day_result = query("SELECT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${cat_config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status IN (1,3) AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 ORDER BY sr.score ASC LIMIT 10");
		$fools_day_items = array();
		while ($rowfd = mysqli_fetch_assoc($fools_day_result)) {
			$fools_day_items[] = $rowfd['id'];
		}
		$recommendations_subquery = implode(',',$fools_day_items);
	} else if (date('m-d')=='04-23') { // Sant Jordi
		$special_day='sant_jordi';
		$sections[1]=$cat_config['section_sant_jordi'];
		//Best rated completed and featurable animes of genres Romance, Boys Love and Girls Love
		$sant_jordi_result = query("SELECT DISTINCT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${cat_config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status=1 AND vr.is_featurable=1 AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 AND sr.id IN (SELECT rsg.series_id FROM rel_series_genre rsg WHERE rsg.genre_id IN (7,23,38)) ORDER BY sr.score DESC LIMIT 10");
		$sant_jordi_items = array();
		while ($rowsj = mysqli_fetch_assoc($sant_jordi_result)) {
			$sant_jordi_items[] = $rowsj['id'];
		}
		$recommendations_subquery = implode(',',$sant_jordi_items);
	} else if (date('m-d')=='10-31' || date('m-d')=='11-01') { // Tots Sants
		$special_day='tots_sants';
		$sections[1]=$cat_config['section_tots_sants'];
		//Best rated completed and featurable animes of genre Horror
		$tots_sants_result = query("SELECT DISTINCT vr.id FROM version vr LEFT JOIN series sr ON vr.series_id=sr.id WHERE sr.type='${cat_config['items_type']}' AND (sr.rating<>'XXX' OR sr.rating IS NULL) AND vr.status=1 AND vr.is_featurable=1 AND sr.score IS NOT NULL AND vr.is_missing_episodes=0 AND sr.id IN (SELECT rsg.series_id FROM rel_series_genre rsg WHERE rsg.genre_id IN (21)) ORDER BY sr.score DESC LIMIT 10");
		$tots_sants_items = array();
		while ($rowsts = mysqli_fetch_assoc($tots_sants_result)) {
			$tots_sants_items[] = $rowsts['id'];
		}
		$recommendations_subquery = implode(',',$tots_sants_items);
	}
	$queries=array(
		NULL,
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND v.id IN ($recommendations_subquery)$cookie_extra_conditions GROUP BY v.id ORDER BY RAND()",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY v.id ORDER BY last_updated DESC LIMIT $max_items",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND completed_date IS NOT NULL AND 1$cookie_extra_conditions GROUP BY v.id ORDER BY completed_date DESC LIMIT $max_items",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY RAND() LIMIT $max_items",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND s.id IN ($in_clause)$cookie_extra_conditions GROUP BY s.id ORDER BY FIELD(s.id,$in_clause) LIMIT $max_items",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.publish_date DESC LIMIT $max_items",
		$base_query . " WHERE s.type='${cat_config['items_type']}' AND $query_portion_limit_to_non_hidden AND 1$cookie_extra_conditions GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
	$specific_version=array(FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE);
	$type=array('advent','recommendations', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel');
}

for ($i=0;$i<count($sections);$i++){
	if ($type[$i]=='advent') {
		if (strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 11:59:59')<=0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<div class="section-content fake-carousel">
						<a class="advent" href="<?php echo $advent_url; ?>" target="_blank"><img src="<?php echo $static_url; ?>/images/advent/header_<?php echo date('Y'); ?>.jpg" alt="Calendari d'advent dels fansubs en català" /></a>
					</div>
				</div>
<?php
		}
		continue;
	}
	$result = query($queries[$i]);
	if (mysqli_num_rows($result)>0 || $type[$i]=='static'){
?>
				<div class="section">
<?php
		if ($type[$i]!='recommendations') {
?>
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
<?php
		}
		if (mysqli_num_rows($result)==0){
?>
					<div class="section-content fake-carousel"><div><i class="fa fa-search empty-icon"></i><br><br>No s'ha trobat cap element. Prova una altra cerca o explora el catàleg a les pestanyes superiors.</div></div>
<?php
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
			}
?>
					<div class="section-content<?php echo $type[$i]=='carousel' ? ' carousel' : ($type[$i]=='recommendations' ? ' recommendations theme-dark' : ' catalogue'); ?>">
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
					print_featured_item($row, $special_day, $specific_version[$i]);
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
	}
	mysqli_free_result($result);
?>
				</div>
<?php
	//Search case: add manga
	if (!empty($_GET['search']) && !empty($query)) {
		switch($cat_config['items_type']) {
			case 'liveaction':
				$other_searches = array('anime', 'manga');
				break;
			case 'manga':
				$other_searches = array('anime', 'liveaction');
				break;
			case 'anime':
			default:
				$other_searches = array('manga', 'liveaction');
				break;
			
		}

		foreach ($other_searches as $other_search_type) {
			$result = query("SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id) ORDER BY v.id,f.name SEPARATOR '|') fansub_info, GROUP_CONCAT(DISTINCT sg.genre_id) genres, GROUP_CONCAT(DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑') ORDER BY g.name SEPARATOR ' • ') genre_names, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE s.type='$other_search_type' AND $query_portion_limit_to_non_hidden AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.keywords OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%' OR s.id IN (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.name='$spaced_query'))$hentai_subquery GROUP BY s.id ORDER BY s.name ASC");
			if (mysqli_num_rows($result)>0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $cat_config['section_search_'.$other_search_type]; ?></h2>
					<div class="section-content catalogue">
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
			mysqli_free_result($result);
?>
					</div>
				</div>
<?php
		}
	}
}
if (!empty($_GET['search'])) {
	require_once("../common.fansubs.cat/footer_text.inc.php");
}
?>

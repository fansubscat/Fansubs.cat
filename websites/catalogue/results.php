<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

$is_hentai_site=!empty($_GET['hentai']);
$is_fools_day = (date('d')==28 && date('m')==12);

if (empty($is_searching)) {
	$is_searching=!empty($_GET['search']);
}

if ($is_hentai_site) {
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
if (!empty($user)) {
	$viewed_files_condition = " AND ls.id NOT IN (SELECT ufp.file_id FROM user_file_progress ufp WHERE ufp.user_id=".$user['id'].")";
} else {
	$cookie_viewed_files_ids = get_cookie_viewed_files_ids();
	if (count($cookie_viewed_files_ids)>0) {
		$viewed_files_condition.=" AND ls.id NOT IN (".implode(',',$cookie_viewed_files_ids).")";
	} else {
		$viewed_files_condition='';
	}
}

if (!empty($is_searching)) {
	$query = (isset($_GET['query']) ? escape($_GET['query']) : "");
	$query = str_replace(" ", "%", $query);
	$is_full_catalogue=($query!='');
	
	switch($cat_config['items_type']) {
		case 'liveaction':
			$searches = array('liveaction','anime', 'manga');
			break;
		case 'manga':
			$searches = array('manga','anime', 'liveaction');
			break;
		case 'anime':
		default:
			$searches = array('anime','manga', 'liveaction');
			break;
	}

	//Same as the base query for non-search but:
	//Without s.type (moved to the particular conditions so we can search for other types too)
	//With filter for s.name, s.alternate_names, s.studio, s.keywords, s.author
	$base_query="SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id) ORDER BY v.id,f.name SEPARATOR '|') fansub_info, GROUP_CONCAT(DISTINCT sg.genre_id) genres, GROUP_CONCAT(DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑') ORDER BY g.name SEPARATOR ' • ') genre_names, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0$viewed_files_condition) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND v.is_hidden=0 AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%')";
	if ($is_hentai_site) {
		$base_query.=" AND s.rating='XXX'";
	} else {
		$base_query.=" AND (s.rating IS NULL OR s.rating<>'XXX')";
	}
	if (isset($_POST['min_score']) && isset($_POST['max_score']) && is_numeric($_POST['min_score']) && is_numeric($_POST['max_score'])) {
		$base_query.=" AND (".($_POST['min_score']==0 ? "s.score IS NULL OR " : '')."(s.score>=".(intval($_POST['min_score'])/10)." AND s.score<=".(intval($_POST['max_score'])/10)."))";
	}
	if (isset($_POST['min_rating']) && isset($_POST['max_rating']) && is_numeric($_POST['min_rating']) && is_numeric($_POST['max_rating'])) {
		$rating_values=array();
		for ($i=intval($_POST['min_rating']); $i<=intval($_POST['max_rating']);$i++) {
			switch($i) {
				case 0:
					array_push($rating_values,"'TP'");
					break;
				case 1:
					array_push($rating_values,"'+7'");
					break;
				case 2:
					array_push($rating_values,"'+13'");
					break;
				case 3:
					array_push($rating_values,"'+16'");
					break;
				case 4:
				default:
					array_push($rating_values,"'+18'");
					break;
			}
		}
		$base_query.=" AND (s.rating IN (".implode(',',$rating_values)."))";
	}
	if (isset($_POST['min_year']) && isset($_POST['max_year']) && is_numeric($_POST['min_year']) && is_numeric($_POST['max_year'])) {
		$base_query.=" AND (".($_POST['min_year']==1950 ? "s.publish_date IS NULL OR " : '')."(YEAR(s.publish_date)>=".intval($_POST['min_year'])." AND YEAR(s.publish_date)<=".intval($_POST['max_year'])."))";
	}
	if (empty($_POST['show_blacklisted_fansubs'])) {
		if (!empty($user)) {
			$base_query.=" AND v.id NOT IN (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.fansub_id IN (SELECT ufbl.fansub_id FROM user_fansub_blacklist ufbl WHERE ufbl.user_id=".$user['id']."))";
		} else {
			$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
			if (count($cookie_blacklisted_fansub_ids)>0) {
				$base_query.=" AND v.id NOT IN (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.fansub_id IN (".implode(',',$cookie_blacklisted_fansub_ids)."))";
			}
		}
	}
	if (empty($_POST['show_lost_content'])) {
		$base_query.=' AND v.is_missing_episodes=0';
	}
	if (isset($_POST['demographics']) && is_array($_POST['demographics'])) {
		$demographics=array();
		$extra='';
		foreach ($_POST['demographics'] as $demographic) {
			if ($demographic==-1) {
				$extra=" OR (s.id IN (SELECT s.id FROM series s WHERE NOT EXISTS (SELECT sg.series_id FROM rel_series_genre sg LEFT JOIN genre g ON sg.genre_id=g.id WHERE g.type='demographics' AND sg.series_id=s.id)))";
			} else {
				array_push($demographics, intval($demographic));
			}
		}
		if (count($demographics)>0) {
			$base_query.=' AND (s.id IN (SELECT sg.series_id FROM rel_series_genre sg WHERE sg.genre_id IN('.implode(',',$demographics).'))'.$extra.')';
		} else if ($extra!=''){
			$base_query.=' AND (0'.$extra.')';
		}
	}
	if (isset($_POST['genres_include']) && is_array($_POST['genres_include'])) {
		foreach ($_POST['genres_include'] as $genre) {
			$base_query.=' AND s.id IN (SELECT sg.series_id FROM rel_series_genre sg WHERE sg.genre_id='.intval($genre).')';
		}
	}
	if (isset($_POST['genres_exclude']) && is_array($_POST['genres_exclude'])) {
		foreach ($_POST['genres_exclude'] as $genre) {
			$base_query.=' AND s.id NOT IN (SELECT sg.series_id FROM rel_series_genre sg WHERE sg.genre_id='.intval($genre).')';
		}
	}
	if (isset($_POST['status']) && is_array($_POST['status'])) {
		$status_include = array();
		foreach ($_POST['status'] as $status) {
			array_push($status_include, intval($status));
		}
		$base_query.=' AND v.status IN ('.implode(',',$status_include).')';
	}
	if (isset($_POST['type']) && $_POST['type']!='all') {
		$base_query.=" AND s.subtype='".escape($_POST['type'])."'";
	}
	$base_query_anime=$base_query;
	$base_query_manga=$base_query;
	$base_query_liveaction=$base_query;
	if (isset($_POST['min_duration']) && isset($_POST['max_duration']) && is_numeric($_POST['min_duration']) && is_numeric($_POST['max_duration'])) {
		if ($cat_config['items_type']!='manga') {
			$base_query_anime.=" AND s.id IN (SELECT DISTINCT s.id FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.type='anime' GROUP BY s.id HAVING AVG(f.length)>=".(intval($_POST['min_duration'])*60)." AND AVG(f.length)<=".(intval($_POST['max_duration']==120 ? 100000 : $_POST['max_duration'])*60).")";
			$base_query_manga.=" AND ".(($_POST['min_duration']==0 && $_POST['max_duration']==120) ? '1' : '0');
			$base_query_liveaction.=" AND s.id IN (SELECT DISTINCT s.id FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.type='liveaction' GROUP BY s.id HAVING AVG(f.length)>=".(intval($_POST['min_duration'])*60)." AND AVG(f.length)<=".(intval($_POST['max_duration']==120 ? 100000 : $_POST['max_duration'])*60).")";
		} else {
			$base_query_anime.=" AND ".(($_POST['min_duration']==1 && $_POST['max_duration']==100) ? '1' : '0');
			$base_query_manga.=" AND s.id IN (SELECT DISTINCT s.id FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN file f ON v.id=f.version_id WHERE s.type='manga' GROUP BY s.id HAVING AVG(f.length)>=".intval($_POST['min_duration'])." AND AVG(f.length)<=".intval($_POST['max_duration']==100 ? 100000 : $_POST['max_duration']).")";
			$base_query_liveaction.=" AND ".(($_POST['min_duration']==1 && $_POST['max_duration']==100) ? '1' : '0');
		}
	}

	$sections=array($cat_config['section_search_'.$searches[0]]);

	if ($is_full_catalogue) {
		array_push($sections, $cat_config['section_search_'.$searches[1]]);
		array_push($sections, $cat_config['section_search_'.$searches[2]]);
	}
	
	switch($cat_config['items_type']) {
		case 'liveaction':
			$queries=array(
				$base_query_liveaction . " AND s.type='${searches[0]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_anime . " AND s.type='${searches[1]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_manga . " AND s.type='${searches[2]}' GROUP BY s.id ORDER BY s.name ASC");
			break;
		case 'manga':
			$queries=array(
				$base_query_manga . " AND s.type='${searches[0]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_anime . " AND s.type='${searches[1]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_liveaction . " AND s.type='${searches[2]}' GROUP BY s.id ORDER BY s.name ASC");
			break;
		case 'anime':
		default:
			$queries=array(
				$base_query_anime . " AND s.type='${searches[0]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_manga . " AND s.type='${searches[1]}' GROUP BY s.id ORDER BY s.name ASC",
				$base_query_liveaction . " AND s.type='${searches[2]}' GROUP BY s.id ORDER BY s.name ASC");
			break;
	}
	$specific_version=array(FALSE, FALSE, FALSE);
	$type=array('static','search','search');
} else {
	$max_items=24;

	$base_query="SELECT s.*, (SELECT nv.id FROM version nv WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0 LIMIT 1) version_id, GROUP_CONCAT(DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id) ORDER BY v.id,f.name SEPARATOR '|') fansub_info, GROUP_CONCAT(DISTINCT sg.genre_id) genres, GROUP_CONCAT(DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑') ORDER BY g.name SEPARATOR ' • ') genre_names, MIN(v.status) best_status, MAX(v.files_updated) last_updated, (SELECT COUNT(d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions, s.number_of_episodes, (SELECT MAX(ls.created) FROM file ls LEFT JOIN version vs ON ls.version_id=vs.id WHERE vs.series_id=s.id AND vs.is_hidden=0$viewed_files_condition) last_file_created FROM series s LEFT JOIN version v ON s.id=v.series_id LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN rel_series_genre sg ON s.id=sg.series_id LEFT JOIN genre g ON sg.genre_id = g.id WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND v.is_hidden=0 AND s.type='${cat_config['items_type']}'";

	if (!empty($user)) {
		$base_query.=" AND v.id NOT IN (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.fansub_id IN (SELECT ufbl.fansub_id FROM user_fansub_blacklist ufbl WHERE ufbl.user_id=".$user['id']."))";
		if (empty($user['show_cancelled_projects'])) {
			$base_query.=' AND v.status<>5 AND v.status<>4';
		}
		if (empty($user['show_lost_projects'])) {
			$base_query.=' AND v.is_missing_episodes=0';
		}
	} else {
		$cookie_viewed_files_ids = get_cookie_viewed_files_ids();
		$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
		if (count($cookie_viewed_files_ids)>0) {
			$base_query.=" AND ls.id NOT IN (".implode(',',$cookie_viewed_files_ids).")";
		}
		if (count($cookie_blacklisted_fansub_ids)>0) {
			$base_query.=" AND v.id NOT IN (SELECT vf2.version_id FROM rel_version_fansub vf2 WHERE vf2.fansub_id IN (".implode(',',$cookie_blacklisted_fansub_ids)."))";
		}
		if (empty($_COOKIE['show_cancelled_projects']) && !is_robot()) {
			$base_query.=' AND v.status<>5 AND v.status<>4';
		}
		if (empty($_COOKIE['show_lost_projects']) || !is_robot()) {
			$base_query.=' AND v.is_missing_episodes=0';
		}
	}
	if ($is_hentai_site) {
		$base_query.=" AND s.rating='XXX'";
	} else {
		$base_query.=" AND (s.rating IS NULL OR s.rating<>'XXX')";
	}

	$sort_by_popularity_result = query("SELECT b.series_id, IFNULL(MAX(b.total_views),0) max_views FROM (SELECT a.series_id, SUM(a.views) total_views FROM (SELECT SUM(vi.views) views, f.version_id, s.id series_id, f.episode_id FROM views vi LEFT JOIN file f ON vi.file_id=f.id LEFT JOIN episode e ON f.episode_id=e.id LEFT JOIN series s ON e.series_id=s.id WHERE (SELECT COUNT(*) FROM version v WHERE s.type='${cat_config['items_type']}' AND v.series_id=s.id AND v.is_hidden=0)>0 AND f.episode_id IS NOT NULL AND vi.views>0 AND vi.day>='".date("Y-m-d",strtotime("-2 weeks"))."' GROUP BY f.version_id, f.episode_id) a GROUP BY a.episode_id) b GROUP BY b.series_id ORDER BY max_views DESC, b.series_id DESC");
	$sort_by_popularity_in_clause='0';
	while ($row = mysqli_fetch_assoc($sort_by_popularity_result)){
		$sort_by_popularity_in_clause.=','.$row['series_id'];
	}
	mysqli_free_result($sort_by_popularity_result);
	$sections=array('advent', 'featured', $cat_config['section_last_updated'], $cat_config['section_last_completed'], $cat_config['section_random'], $cat_config['section_popular'], $cat_config['section_more_recent'], $cat_config['section_best_rated']);
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
		$base_query . " AND v.id IN ($recommendations_subquery) GROUP BY v.id ORDER BY RAND()",
		$base_query . " GROUP BY v.id ORDER BY last_updated DESC LIMIT $max_items",
		$base_query . " AND completed_date IS NOT NULL GROUP BY v.id ORDER BY completed_date DESC LIMIT $max_items",
		$base_query . " GROUP BY s.id ORDER BY RAND() LIMIT $max_items",
		$base_query . " AND s.id IN ($sort_by_popularity_in_clause) GROUP BY s.id ORDER BY FIELD(s.id,$sort_by_popularity_in_clause) LIMIT $max_items",
		$base_query . " GROUP BY s.id ORDER BY s.publish_date DESC LIMIT $max_items",
		$base_query . " GROUP BY s.id ORDER BY s.score DESC LIMIT $max_items");
	$specific_version=array(FALSE, TRUE, TRUE, TRUE, FALSE, FALSE, FALSE, FALSE);
	$type=array('advent','recommendations', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel', 'carousel');
}
$results=array();
for ($i=0;$i<count($sections);$i++){
	if ($type[$i]=='advent') {
		if (strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 11:59:59')<=0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
					<div class="section-content">
						<a class="advent" href="<?php echo $advent_url; ?>" target="_blank"><img src="<?php echo $static_url; ?>/images/advent/header_<?php echo date('Y'); ?>.jpg" alt="Calendari d'advent dels fansubs en català" /></a>
					</div>
				</div>
<?php
		}
		array_push($results, NULL);
		continue;
	}
	array_push($results, query($queries[$i]));
}
for ($i=0;$i<count($results);$i++){
	$result=$results[$i];
	if ($type[$i]=='advent') {
		continue;
	}
	if (mysqli_num_rows($result)>0 || ($type[$i]=='static')){
?>
				<div class="section">
<?php
		if ($type[$i]!='recommendations') {
?>
					<h2 class="section-title-main"><?php echo $sections[$i]; ?></h2>
<?php
		}
		if (mysqli_num_rows($result)==0){ //Default search case ('static'), because other types are filtered out
			if ($is_full_catalogue && (mysqli_num_rows($results[$i+1])>0 || mysqli_num_rows($results[$i+2])>0)) {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No s’ha trobat cap <?php echo $cat_config['items_string_s']; ?> per a aquesta cerca, però hi ha altres continguts que hi coincideixen. Els tens a continuació.</div></div>
<?php
			} else {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No s'ha trobat cap contingut per a aquesta cerca. Prova de reduir la cerca o fes-ne una altra.</div></div>
<?php
			}
		} else {
?>
					<div class="section-content<?php echo $type[$i]=='carousel' ? ' carousel' : ($type[$i]=='recommendations' ? ' recommendations theme-dark' : ' catalogue'); ?>">
<?php
			while ($row = mysqli_fetch_assoc($result)){
?>
						<div class="status-<?php echo get_status($row['best_status']); ?>">
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
?>
				</div>
<?php
	}
	mysqli_free_result($result);
}
if (!empty($_GET['search'])) {
	require_once("../common.fansubs.cat/footer_text.inc.php");
}
?>

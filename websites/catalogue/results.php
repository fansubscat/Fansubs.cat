<?php
define('PAGE_STYLE_TYPE', 'catalogue');
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

validate_hentai_ajax();

define('IS_FOOLS_DAY', date('d')==28 && date('m')==12);
if (isset($_GET['search'])) {
	define('PAGE_IS_SEARCH', TRUE);
}

if (GLOBAL_MESSAGE!='' || IS_FOOLS_DAY){
?>
				<div data-nosnippet class="section">
					<div class="site-message"><?php echo IS_FOOLS_DAY ? 'Estem millorant el disseny de la pàgina. De moment hi hem afegit Comic Sans, que li donarà un toc més modern. <a href="'.STATIC_URL.'/various/innocents.png" target="_blank" style="color: black;">Més informació</a>.' : GLOBAL_MESSAGE; ?></div>
				</div>
<?php
}
if (!empty($user)) {
	$viewed_files_condition = " AND ls.id NOT IN (SELECT ufp.file_id
							FROM user_file_progress ufp
							WHERE ufp.user_id=${user['id']})";
} else {
	$cookie_viewed_files_ids = get_cookie_viewed_files_ids();
	if (count($cookie_viewed_files_ids)>0) {
		$viewed_files_condition.=" AND ls.id NOT IN (".implode(',',$cookie_viewed_files_ids).")";
	} else {
		$viewed_files_condition='';
	}
}

if (defined('PAGE_IS_SEARCH')) {
	$query = (isset($_GET['query']) ? escape(urldecode($_GET['query'])) : "");
	$query = str_replace(" ", "%", $query);
	$is_full_catalogue=($query!='');

	//Same as the base query for non-search but:
	//Without s.type (moved to the particular conditions so we can search for other types too)
	//With filter for s.name, s.alternate_names, s.studio, s.keywords, s.author
	$base_query="	SELECT s.*,
				(
					SELECT nv.id
					FROM version nv
					WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0
					LIMIT 1
				) version_id,
				GROUP_CONCAT(
					DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id)
					ORDER BY v.id, f.name
					SEPARATOR '|'
				) fansub_info,
				GROUP_CONCAT(
					DISTINCT sg.genre_id
				) genres,
				GROUP_CONCAT(
					DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑')
					ORDER BY g.name
					SEPARATOR ' • '
				) genre_names,
				MIN(v.status) best_status,
				MAX(v.files_updated) last_updated,
				(
					SELECT COUNT(d.id)
					FROM division d
					WHERE d.series_id=s.id AND d.number_of_episodes>0
				) divisions,
				s.number_of_episodes,
				(
					SELECT MAX(ls.created)
					FROM file ls
						LEFT JOIN version vs ON ls.version_id=vs.id
					WHERE vs.series_id=s.id AND vs.is_hidden=0$viewed_files_condition
				) last_file_created
			FROM series s
				LEFT JOIN version v ON s.id=v.series_id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE (
				SELECT COUNT(*)
				FROM version v
				WHERE v.series_id=s.id AND v.is_hidden=0
				)>0 AND v.is_hidden=0 AND (
					s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR s.studio LIKE '%$query%' OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%'
					)";
	if (SITE_IS_HENTAI) {
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
			$base_query.=" AND v.id NOT IN (
					SELECT vf2.version_id
					FROM rel_version_fansub vf2
					WHERE vf2.fansub_id IN (
						SELECT ufbl.fansub_id
						FROM user_fansub_blacklist ufbl
						WHERE ufbl.user_id=${user['id']}
						)
					)";
		} else {
			$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
			if (count($cookie_blacklisted_fansub_ids)>0) {
				$base_query.=" AND v.id NOT IN (
						SELECT vf2.version_id
						FROM rel_version_fansub vf2
						WHERE vf2.fansub_id IN (
							".implode(',',$cookie_blacklisted_fansub_ids)."
							)
						)";
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
				$extra=" OR (
						s.id IN (
							SELECT s.id
							FROM series s
							WHERE NOT EXISTS (
								SELECT sg.series_id
								FROM rel_series_genre sg
									LEFT JOIN genre g ON sg.genre_id=g.id
								WHERE g.type='demographics' AND sg.series_id=s.id
								)
							)
					)";
			} else {
				array_push($demographics, intval($demographic));
			}
		}
		if (count($demographics)>0) {
			$base_query.=' AND (
					s.id IN (
						SELECT sg.series_id
						FROM rel_series_genre sg
						WHERE sg.genre_id IN('.implode(',',$demographics).")
						)
					$extra)";
		} else if ($extra!=''){
			$base_query.=' AND (0'.$extra.')';
		}
	}
	if (isset($_POST['genres_include']) && is_array($_POST['genres_include'])) {
		foreach ($_POST['genres_include'] as $genre) {
			$base_query.=' AND s.id IN (
					SELECT sg.series_id
					FROM rel_series_genre sg
					WHERE sg.genre_id='.intval($genre)."
					)";
		}
	}
	if (isset($_POST['genres_exclude']) && is_array($_POST['genres_exclude'])) {
		foreach ($_POST['genres_exclude'] as $genre) {
			$base_query.=' AND s.id NOT IN (
					SELECT sg.series_id
					FROM rel_series_genre sg WHERE sg.genre_id='.intval($genre)."
					)";
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
		if (CATALOGUE_ITEM_TYPE!='manga') {
			$base_query_anime.=" AND s.id IN (
						SELECT DISTINCT s.id
						FROM series s
							LEFT JOIN version v ON s.id=v.series_id
							LEFT JOIN file f ON v.id=f.version_id
						WHERE s.type='anime'
						GROUP BY s.id HAVING AVG(f.length)>=".(intval($_POST['min_duration'])*60)." AND AVG(f.length)<=".(intval($_POST['max_duration']==120 ? 100000 : $_POST['max_duration'])*60)."
						)";
			$base_query_manga.=" AND ".(($_POST['min_duration']==0 && $_POST['max_duration']==120) ? '1' : '0');
			$base_query_liveaction.=" AND s.id IN (
						SELECT DISTINCT s.id
						FROM series s
							LEFT JOIN version v ON s.id=v.series_id
							LEFT JOIN file f ON v.id=f.version_id
						WHERE s.type='liveaction'
						GROUP BY s.id HAVING AVG(f.length)>=".(intval($_POST['min_duration'])*60)." AND AVG(f.length)<=".(intval($_POST['max_duration']==120 ? 100000 : $_POST['max_duration'])*60)."
						)";
		} else {
			$base_query_anime.=" AND ".(($_POST['min_duration']==1 && $_POST['max_duration']==100) ? '1' : '0');
			$base_query_manga.=" AND s.id IN (
						SELECT DISTINCT s.id
						FROM series s
							LEFT JOIN version v ON s.id=v.series_id
							LEFT JOIN file f ON v.id=f.version_id
						WHERE s.type='manga'
						GROUP BY s.id HAVING AVG(f.length)>=".intval($_POST['min_duration'])." AND AVG(f.length)<=".intval($_POST['max_duration']==100 ? 100000 : $_POST['max_duration'])."
						)";
			$base_query_liveaction.=" AND ".(($_POST['min_duration']==1 && $_POST['max_duration']==100) ? '1' : '0');
		}
	}

	$sections = array();
	
	switch(CATALOGUE_ITEM_TYPE) {
		case 'liveaction':
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
				'specific_version' => FALSE,
				'result' => query($base_query_liveaction . " AND s.type='liveaction' GROUP BY s.id ORDER BY s.name ASC"),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
					'specific_version' => FALSE,
					'result' => query($base_query_anime . " AND s.type='anime' GROUP BY s.id ORDER BY s.name ASC"),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
					'specific_version' => FALSE,
					'result' => query($base_query_manga . " AND s.type='manga' GROUP BY s.id ORDER BY s.name ASC"),
				));
			}
			break;
		case 'manga':
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
				'specific_version' => FALSE,
				'result' => query($base_query_manga . " AND s.type='manga' GROUP BY s.id ORDER BY s.name ASC"),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
					'specific_version' => FALSE,
					'result' => query($base_query_anime . " AND s.type='anime' GROUP BY s.id ORDER BY s.name ASC"),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
					'specific_version' => FALSE,
					'result' => query($base_query_liveaction . " AND s.type='liveaction' GROUP BY s.id ORDER BY s.name ASC"),
				));
			}
			break;
		case 'anime':
		default:
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
				'specific_version' => FALSE,
				'result' => query($base_query_anime . " AND s.type='anime' GROUP BY s.id ORDER BY s.name ASC"),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
					'specific_version' => FALSE,
					'result' => query($base_query_manga . " AND s.type='manga' GROUP BY s.id ORDER BY s.name ASC"),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
					'specific_version' => FALSE,
					'result' => query($base_query_liveaction . " AND s.type='liveaction' GROUP BY s.id ORDER BY s.name ASC"),
				));
			}
			break;
	}
} else {
	$max_items=24;

	$sections = array();

	$base_query="	SELECT s.*,
				(
					SELECT nv.id
					FROM version nv
					WHERE nv.files_updated=MAX(v.files_updated) AND nv.series_id=s.id AND nv.is_hidden=0
					LIMIT 1
				) version_id,
				GROUP_CONCAT(
					DISTINCT CONCAT(v.id,'___',f.name,'___',f.type,'___',f.id)
					ORDER BY v.id, f.name
					SEPARATOR '|'
				) fansub_info,
				GROUP_CONCAT(
					DISTINCT sg.genre_id
				) genres,
				GROUP_CONCAT(
					DISTINCT REPLACE(REPLACE(g.name,' ',' '),'-','‑')
					ORDER BY g.name
					SEPARATOR ' • '
				) genre_names,
				MIN(v.status) best_status,
				MAX(v.files_updated) last_updated,
				(
					SELECT COUNT(d.id)
					FROM division d
					WHERE d.series_id=s.id AND d.number_of_episodes>0
				) divisions,
				s.number_of_episodes,
				(
					SELECT MAX(ls.created)
					FROM file ls
						LEFT JOIN version vs ON ls.version_id=vs.id
					WHERE vs.series_id=s.id AND vs.is_hidden=0$viewed_files_condition
				) last_file_created
			FROM series s
				LEFT JOIN version v ON s.id=v.series_id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE (
				SELECT COUNT(*)
				FROM version v
				WHERE v.series_id=s.id AND v.is_hidden=0
				)>0 AND v.is_hidden=0 AND s.type='".CATALOGUE_ITEM_TYPE."'";

	if (!empty($user)) {
		$base_query.=" AND v.id NOT IN (
				SELECT vf2.version_id
				FROM rel_version_fansub vf2
				WHERE vf2.fansub_id IN (
					SELECT ufbl.fansub_id
					FROM user_fansub_blacklist ufbl
					WHERE ufbl.user_id=".$user['id']."
					)
				)";
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
			$base_query.=" AND v.id NOT IN (
							SELECT vf2.version_id
							FROM rel_version_fansub vf2
							WHERE vf2.fansub_id IN (".implode(',',$cookie_blacklisted_fansub_ids).")
							)";
		}
		if (empty($_COOKIE['show_cancelled_projects']) && !is_robot()) {
			$base_query.=' AND v.status<>5 AND v.status<>4';
		}
		if (empty($_COOKIE['show_lost_projects']) || !is_robot()) {
			$base_query.=' AND v.is_missing_episodes=0';
		}
	}
	if (SITE_IS_HENTAI) {
		$base_query.=" AND s.rating='XXX'";
	} else {
		$base_query.=" AND (s.rating IS NULL OR s.rating<>'XXX')";
	}

	$sort_by_popularity_result = query_most_popular_series_from_date(date("Y-m-d",strtotime("-2 weeks")));
	$sort_by_popularity_in_clause='0';
	while ($row = mysqli_fetch_assoc($sort_by_popularity_result)){
		$sort_by_popularity_in_clause.=','.$row['series_id'];
	}
	mysqli_free_result($sort_by_popularity_result);

	array_push($sections, array(
		'type' => 'advent',
		'title' => NULL,
		'specific_version' => FALSE,
		'result' => NULL,
	));

	$recommendations_subquery = "	SELECT version_id
					FROM recommendation r
						LEFT JOIN version v ON r.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
					WHERE s.type='".CATALOGUE_ITEM_TYPE."'";
	$special_day=NULL;
	if (IS_FOOLS_DAY) {
		$special_day='fools';
		//Worst rated completed or semi completed animes
		$fools_day_result = query_version_ids_for_fools_day();
		$fools_day_items = array();
		while ($rowfd = mysqli_fetch_assoc($fools_day_result)) {
			$fools_day_items[] = $rowfd['id'];
		}
		$recommendations_subquery = implode(',',$fools_day_items);
	} else if (date('m-d')=='04-23') { // Sant Jordi
		$special_day='sant_jordi';
		//Best rated completed and featurable animes of genres Romance, Boys Love and Girls Love
		$sant_jordi_result = query_version_ids_for_sant_jordi();
		$sant_jordi_items = array();
		while ($rowsj = mysqli_fetch_assoc($sant_jordi_result)) {
			$sant_jordi_items[] = $rowsj['id'];
		}
		$recommendations_subquery = implode(',',$sant_jordi_items);
	} else if (date('m-d')=='10-31' || date('m-d')=='11-01') { // Tots Sants
		$special_day='tots_sants';
		//Best rated completed and featurable animes of genre Horror
		$tots_sants_result = query_version_ids_for_tots_sants();
		$tots_sants_items = array();
		while ($rowsts = mysqli_fetch_assoc($tots_sants_result)) {
			$tots_sants_items[] = $rowsts['id'];
		}
		$recommendations_subquery = implode(',',$tots_sants_items);
	}

	array_push($sections, array(
		'type' => 'recommendations',
		'title' => $special_day,
		'specific_version' => TRUE,
		'result' => query("$base_query AND v.id IN ($recommendations_subquery)
			GROUP BY v.id
			ORDER BY RAND()"),
	));

	array_push($sections, array(
		'type' => 'chapters-carousel',
		'title' => '<i class="fa fa-fw fa-eye"></i> Continua mirant',
		'specific_version' => TRUE,
		'result' => query("SELECT
			e.number episode_number,
			et.title episode_title,
			s.name series_name,
			GROUP_CONCAT(DISTINCT CONCAT(fa.name,'___',fa.type,'___',fa.id) ORDER BY fa.name SEPARATOR '|') fansub_info,
			ufp.progress/f.length progress_percent
		FROM
			user_file_progress ufp
			LEFT JOIN file f ON ufp.file_id=f.id
			LEFT JOIN version v ON f.version_id=v.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id
			LEFT JOIN fansub fa ON vf.fansub_id=fa.id
			LEFT JOIN episode e ON f.episode_id=e.id
			LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
		WHERE ufp.user_id=${user['id']}
			AND s.type='".CATALOGUE_ITEM_TYPE."'
			AND ufp.is_seen=0
		GROUP BY f.id"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-clock-rotate-left"></i> Darreres actualitzacions',
		'specific_version' => TRUE,
		'result' => query("	$base_query
		GROUP BY v.id
		ORDER BY last_updated DESC
		LIMIT $max_items"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-check"></i> Finalitzats recentment',
		'specific_version' => TRUE,
		'result' => query("	$base_query AND completed_date IS NOT NULL
			GROUP BY v.id
			ORDER BY completed_date DESC
			LIMIT $max_items"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-dice"></i> A l’atzar',
		'specific_version' => FALSE,
		'result' => query("	$base_query
			GROUP BY s.id
			ORDER BY RAND()
			LIMIT $max_items"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-fire"></i> Més populars',
		'specific_version' => FALSE,
		'result' => query("	$base_query AND s.id IN ($sort_by_popularity_in_clause)
			GROUP BY s.id
			ORDER BY FIELD(s.id,$sort_by_popularity_in_clause)
			LIMIT $max_items"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-stopwatch"></i> Més actuals',
		'specific_version' => FALSE,
		'result' => query("	$base_query
			GROUP BY s.id
			ORDER BY s.publish_date DESC
			LIMIT $max_items"),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-heart"></i> Més ben valorats',
		'specific_version' => FALSE,
		'result' => query("	$base_query
			GROUP BY s.id
			ORDER BY s.score DESC
			LIMIT $max_items"),
	));
}

$i=0;
foreach($sections as $section){
	$result = $section['result'];
	if ($section['type']=='advent') {
		if (strcmp(date('m-d H:i:s'),'12-01 12:00:00')>=0 && strcmp(date('m-d H:i:s'),'12-25 11:59:59')<=0){
?>
				<div class="section">
					<h2 class="section-title-main"><?php echo $section['title']; ?></h2>
					<div class="section-content">
						<a class="advent" href="<?php echo ADVENT_URL; ?>" target="_blank"><img src="<?php echo STATIC_URL; ?>/images/advent/header_<?php echo date('Y'); ?>.jpg" alt="Calendari d'advent dels fansubs en català" /></a>
					</div>
				</div>
<?php
		}
		continue;
	} else if ($section['type']=='chapters-carousel' && empty($user)) {
		continue;
	} else if (mysqli_num_rows($result)>0 || ($section['type']=='static')){
?>
				<div class="section">
<?php
		if ($section['type']!='recommendations') {
?>
					<h2 class="section-title-main"><?php echo $section['title']; ?></h2>
<?php
		}
		if (mysqli_num_rows($result)==0){ //Default search case ('static'), because other types are filtered out
			if ($is_full_catalogue && (mysqli_num_rows($sections[$i+1]['result'])>0 || mysqli_num_rows($sections[$i+2]['result'])>0)) {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No s’ha trobat cap <?php echo CATALOGUE_ITEM_STRING_SINGULAR; ?> per a aquesta cerca, però hi ha altres continguts que hi coincideixen. Els tens a continuació.</div></div>
<?php
			} else {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br>No s'ha trobat cap contingut per a aquesta cerca. Prova de reduir la cerca o fes-ne una altra.</div></div>
<?php
			}
		} else {
?>
					<div class="section-content<?php echo ($section['type']=='carousel' || $section['type']=='chapters-carousel') ? ' carousel' : ($section['type']=='recommendations' ? ' recommendations theme-dark' : ' catalogue'); ?>">
<?php
			while ($row = mysqli_fetch_assoc($result)){
?>
						<div<?php echo isset($row['best_status']) ? ' class="status-'.get_status($row['best_status']).'"' : ''; ?>>
<?php
				if ($section['type']=='recommendations') {
					print_featured_item($row, $section['title'], $section['specific_version']);
				} else if ($section['type']=='chapters-carousel'){
					print_chapter_item($row);
				} else {
					print_carousel_item($row, $section['specific_version']);
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
	$i++;
}
if (!defined('PAGE_IS_SEARCH')) {
	require_once("../common.fansubs.cat/footer_text.inc.php");
}
?>

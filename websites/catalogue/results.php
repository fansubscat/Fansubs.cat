<?php
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'catalogue');
}
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

validate_hentai_ajax();

if (isset($_GET['search'])) {
	define('PAGE_IS_SEARCH', TRUE);
}

if (defined('PAGE_IS_SEARCH')) {
	$text = (isset($_GET['query']) ? $_GET['query'] : "");
	$is_full_catalogue=($text!='');
	$subtype='all';
	$min_score = 0;
	$max_score = 10;
	$ratings = array();
	$min_year = 1950;
	$max_year = date('Y');
	$min_duration=0;
	$max_duration=(CATALOGUE_ITEM_TYPE=='manga' ? 100 : 120);
	$length_type=(CATALOGUE_ITEM_TYPE=='manga' ? 'pages' : 'minutes');
	$show_blacklisted_fansubs = TRUE;
	$show_lost_content = TRUE;
	$show_no_demographics = TRUE;
	$demographics=array();
	$genres_include=array();
	$genres_exclude=array();
	$statuses=array();
	if (isset($_POST['min_score']) && isset($_POST['max_score']) && is_numeric($_POST['min_score']) && is_numeric($_POST['max_score'])) {
		$min_score = intval($_POST['min_score'])/10;
		$max_score = intval($_POST['max_score'])/10;
	}
	if (isset($_POST['min_rating']) && isset($_POST['max_rating']) && is_numeric($_POST['min_rating']) && is_numeric($_POST['max_rating'])) {
		for ($i=intval($_POST['min_rating']); $i<=intval($_POST['max_rating']);$i++) {
			switch($i) {
				case 0:
					array_push($ratings, "TP");
					break;
				case 1:
					array_push($ratings, "+7");
					break;
				case 2:
					array_push($ratings, "+13");
					break;
				case 3:
					array_push($ratings, "+16");
					break;
				case 4:
				default:
					array_push($ratings, "+18");
					break;
			}
		}
	}
	if (isset($_POST['min_year']) && isset($_POST['max_year']) && is_numeric($_POST['min_year']) && is_numeric($_POST['max_year'])) {
		$min_year = $_POST['min_year'];
		$max_year = $_POST['max_year'];
	}
	if (!empty($_POST['hide_blacklisted_fansubs'])) {
		$show_blacklisted_fansubs = FALSE;
	}
	if (!empty($_POST['hide_lost_content'])) {
		$show_lost_content = FALSE;
	}
	if (isset($_POST['demographics']) && is_array($_POST['demographics'])) {
		$show_no_demographics = FALSE;
		foreach ($_POST['demographics'] as $demographic) {
			if ($demographic==-1) {
				$show_no_demographics = TRUE;
			} else {
				array_push($demographics, intval($demographic));
			}
		}
	}
	if (isset($_POST['genres_include']) && is_array($_POST['genres_include'])) {
		foreach ($_POST['genres_include'] as $genre) {
			array_push($genres_include, intval($genre));
		}
	}
	if (isset($_POST['genres_exclude']) && is_array($_POST['genres_exclude'])) {
		foreach ($_POST['genres_exclude'] as $genre) {
			array_push($genres_exclude, intval($genre));
		}
	}
	if (isset($_POST['status']) && is_array($_POST['status'])) {
		foreach ($_POST['status'] as $status) {
			array_push($statuses, intval($status));
		}
	}
	if (isset($_POST['type']) && $_POST['type']!='all') {
		$subtype = $_POST['type'];
	}
	if (isset($_POST['min_duration']) && isset($_POST['max_duration']) && is_numeric($_POST['min_duration']) && is_numeric($_POST['max_duration'])) {
		$min_duration=$_POST['min_duration'];
		$max_duration=$_POST['max_duration'];
	}

	$sections = array();
	
	switch(CATALOGUE_ITEM_TYPE) {
		case 'liveaction':
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
				'specific_version' => FALSE,
				'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
		case 'manga':
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
				'specific_version' => FALSE,
				'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
		case 'anime':
		default:
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-display"></i> Resultats d’anime',
				'specific_version' => FALSE,
				'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> Resultats de manga',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> Resultats d’acció real',
					'specific_version' => FALSE,
					'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
	}
} else {
	$max_items=24;

	$sections = array();

	array_push($sections, array(
		'type' => 'advent',
		'title' => NULL,
		'specific_version' => FALSE,
		'result' => NULL,
	));

	$special_day = NULL;
	$force_recommended_ids_list = array();
	if (date('m-d')=='12-28') {
		$special_day='fools';
		$result = query_version_ids_for_fools_day(10);
	} else if (date('m-d')=='04-23') { // Sant Jordi
		$special_day='sant_jordi';
		$result = query_version_ids_for_sant_jordi(10);
	} else if (date('m-d')=='10-31' || date('m-d')=='11-01') { // Tots Sants
		$special_day='tots_sants';
		$result = query_version_ids_for_tots_sants(10);
	}
	if ($special_day!==NULL) {
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($force_recommended_ids_list, $row['id']);
		}
		mysqli_free_result($result);
	}

	array_push($sections, array(
		'type' => 'recommendations',
		'title' => $special_day,
		'specific_version' => TRUE,
		'result' => query_home_recommended_items($user, $force_recommended_ids_list, 10),
	));

	if (!empty($user)) {
		array_push($sections, array(
			'type' => 'chapters-carousel',
			'title' => '<i class="fa fa-fw fa-eye"></i> Continua '.(CATALOGUE_ITEM_TYPE=='manga' ? 'llegint' : 'mirant'),
			'specific_version' => TRUE,
			'result' => query_home_continue_watching_by_user_id($user['id']),
		));
	}

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-clock-rotate-left"></i> Darreres actualitzacions',
		'specific_version' => TRUE,
		'result' => query_home_last_updated($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-check"></i> Finalitzats recentment',
		'specific_version' => TRUE,
		'result' => query_home_last_finished($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-dice"></i> A l’atzar',
		'specific_version' => FALSE,
		'result' => query_home_random($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-fire"></i> Més populars',
		'specific_version' => FALSE,
		'result' => query_home_most_popular($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-stopwatch"></i> Més actuals',
		'specific_version' => FALSE,
		'result' => query_home_more_recent($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-heart"></i> Més ben valorats',
		'specific_version' => FALSE,
		'result' => query_home_best_rated($user, $max_items),
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

if (defined('PAGE_IS_SEARCH')) {
	require_once("../common.fansubs.cat/footer_text.inc.php");
}
?>

<?php
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'catalogue');
}
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

$special_day = get_special_day();

validate_hentai_ajax();

if (isset($_GET['search'])) {
	define('PAGE_IS_SEARCH', TRUE);
}

if (defined('PAGE_IS_SEARCH')) {
	$text = (isset($_GET['query']) ? $_GET['query'] : "");
	$is_full_catalogue=($text!='' && (!empty($_POST['full_catalogue']) || defined('ROBOT_INCLUDED')));
	$subtype='all';
	$min_score = 0;
	$max_score = 10;
	$ratings = array();
	$min_year = 1950;
	$max_year = date('Y');
	$min_duration=0;
	$max_duration=CATALOGUE_MAXIMUM_DURATION;
	$length_type=CATALOGUE_DURATION_SLIDER_FORMATTING;
	$fansub_slug = NULL;
	$show_blacklisted_fansubs = TRUE;
	$show_lost_content = FALSE;
	$show_no_demographics = FALSE;
	$content_types=array();
	$demographics=array();
	$origins=array();
	$genres_include=array();
	$genres_exclude=array();
	$statuses=array();
	if (isset($_POST['min_score']) && isset($_POST['max_score']) && is_numeric($_POST['min_score']) && is_numeric($_POST['max_score'])) {
		$min_score = intval($_POST['min_score'])/10;
		$max_score = intval($_POST['max_score'])/10;
	}
	if (isset($_POST['min_rating']) && isset($_POST['max_rating']) && is_numeric($_POST['min_rating']) && is_numeric($_POST['max_rating']) && !SITE_IS_HENTAI) {
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
	if (!empty($_POST['fansub'])) {
		if ($_POST['fansub']=='-1') {
			$show_blacklisted_fansubs = TRUE;
		} else {
			$show_blacklisted_fansubs = FALSE;
		}
	}
	if (!empty($_POST['fansub']) && $_POST['fansub']!='-1' && $_POST['fansub']!='-2') {
		$fansub_slug = $_POST['fansub'];
	}
	if (!empty($_POST['show_lost_content'])) {
		$show_lost_content = TRUE;
	}
	if (isset($_POST['content_types']) && is_array($_POST['content_types'])) {
		foreach ($_POST['content_types'] as $content_type) {
			array_push($content_types, escape($content_type));
		}
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
	if (isset($_POST['origins']) && is_array($_POST['origins'])) {
		foreach ($_POST['origins'] as $origin) {
			array_push($origins, escape($origin));
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
				'title' => '<i class="fa fa-fw fa-clapperboard"></i> '.lang('catalogue.search.results.liveaction'),
				'specific_version' => FALSE,
				'use_version_param' => TRUE,
				'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-tv"></i> '.lang('catalogue.search.results.anime'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> '.lang('catalogue.search.results.manga'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
		case 'manga':
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-book-open"></i> '.lang('catalogue.search.results.manga'),
				'specific_version' => FALSE,
				'use_version_param' => TRUE,
				'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-tv"></i> '.lang('catalogue.search.results.anime'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> '.lang('catalogue.search.results.liveaction'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
		case 'anime':
		default:
			array_push($sections, array(
				'type' => 'static',
				'title' => '<i class="fa fa-fw fa-tv"></i> '.lang('catalogue.search.results.anime'),
				'specific_version' => FALSE,
				'use_version_param' => TRUE,
				'result' => query_search_filter($user, $text, 'anime', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
			));
			if ($is_full_catalogue) {
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-book-open"></i> '.lang('catalogue.search.results.manga'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'manga', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
				array_push($sections, array(
					'type' => 'search',
					'title' => '<i class="fa fa-fw fa-clapperboard"></i> '.lang('catalogue.search.results.liveaction'),
					'specific_version' => FALSE,
					'use_version_param' => TRUE,
					'result' => query_search_filter($user, $text, 'liveaction', $subtype, $min_score, $max_score, $min_year, $max_year, $min_duration, $max_duration, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographics, $content_types, $origins, $genres_include, $genres_exclude, $statuses),
				));
			}
			break;
	}
} else {
	$max_items=24;

	$sections = array();

	$force_recommended_ids_list = array();
	if ($special_day!==NULL) {
		if ($special_day=='fools') {
			$result_recos = query_version_ids_for_fools_day(10);
			$special_day_title = sprintf(lang('catalogue.featured.fools_day.title'), date('Y'));
			$special_day_description = sprintf(lang('catalogue.featured.fools_day.description'), CURRENT_SITE_NAME);
		} else if ($special_day=='sant_jordi') {
			$result_recos = query_version_ids_for_sant_jordi(10);
			$special_day_title = lang('catalogue.featured.sant_jordi.title');
			$special_day_description = sprintf(lang('catalogue.featured.sant_jordi.description'), CURRENT_SITE_NAME);
		} else if ($special_day=='tots_sants') {
			$result_recos = query_version_ids_for_tots_sants(10);
			$special_day_title = lang('catalogue.featured.halloween.title');
			$special_day_description = sprintf(lang('catalogue.featured.halloween.description'), CURRENT_SITE_NAME);
		} else if ($special_day=='nadal') {
			if (date('m-d')>='12-25' && date('m-d')<='12-31') {
				$result_recos = query_version_ids_for_nadal(10);
				$special_day_title = sprintf(lang('catalogue.featured.christmas.title'), date('Y'));
				$special_day_description = sprintf(lang('catalogue.featured.christmas.description'), CURRENT_SITE_NAME);
			} else {
				//No special selection: just show the lights
			}
		} else {
			die("Unsupported special day $special_day!");
		}
		if (!empty($result_recos)) {
			while ($row = mysqli_fetch_assoc($result_recos)) {
				array_push($force_recommended_ids_list, $row['id']);
			}
			mysqli_free_result($result_recos);
		}
	}

	array_push($sections, array(
		'type' => 'recommendations',
		'title' => $special_day,
		'specific_version' => TRUE,
		'use_version_param' => TRUE,
		'result' => query_home_recommended_items($user, $force_recommended_ids_list, 10),
	));

	if (!empty($user)) {
		array_push($sections, array(
			'type' => 'chapters-carousel',
			'title' => '<i class="fa fa-fw fa-eye"></i> '.CATALOGUE_CONTINUE_WATCHING_STRING,
			'specific_version' => TRUE,
			'use_version_param' => TRUE,
			'result' => query_home_continue_watching_by_user_id($user['id']),
		));
	}

	array_push($sections, array(
		'type' => 'chapters-carousel-last-update',
		'title' => '<i class="fa fa-fw fa-clock-rotate-left"></i> '.lang('catalogue.generic.latest_published_chapters'),
		'specific_version' => TRUE,
		'use_version_param' => TRUE,
		'result' => query_home_last_updated($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw far fa-clock"></i> '.CATALOGUE_MOST_RECENT_STRING,
		'specific_version' => FALSE,
		'use_version_param' => FALSE,
		'result' => query_home_more_recent($user, $max_items),
	));

	if (!empty($user)) {
		array_push($sections, array(
			'type' => 'carousel',
			'title' => '<i class="fa fa-fw fa-star"></i> '.lang('catalogue.generic.recommended_for_you'),
			'specific_version' => FALSE,
			'use_version_param' => FALSE,
			'result' => query_home_user_recommendations_by_user_id($user, $max_items),
		));
	}

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw far fa-circle-check"></i> '.CATALOGUE_LAST_FINISHED_SERIALIZED_STRING,
		'specific_version' => TRUE,
		'use_version_param' => TRUE,
		'result' => query_home_last_finished_by_type($user, $max_items, CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw far fa-circle-check"></i> '.CATALOGUE_LAST_FINISHED_SINGLE_STRING,
		'specific_version' => TRUE,
		'use_version_param' => TRUE,
		'result' => query_home_last_finished_by_type($user, $max_items, CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-chart-simple"></i> '.sprintf(lang('catalogue.generic.most_popular'), CURRENT_SITE_NAME),
		'specific_version' => FALSE,
		'use_version_param' => FALSE,
		'result' => query_home_most_popular($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw '.CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON.'"></i> '.CATALOGUE_BEST_SERIALIZED_STRING,
		'specific_version' => FALSE,
		'use_version_param' => FALSE,
		'result' => query_home_best_rated($user, CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw '.CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON.'"></i> '.CATALOGUE_BEST_SINGLE_STRING,
		'specific_version' => FALSE,
		'use_version_param' => FALSE,
		'result' => query_home_best_rated($user, CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID, $max_items),
	));

	array_push($sections, array(
		'type' => 'carousel',
		'title' => '<i class="fa fa-fw fa-dice"></i> '.lang('catalogue.generic.try_your_luck').' <span class="sort-order refresh-random-button" onclick="refreshRandomResults();"><span class="fa fa-fw fa-refresh"></span> <span class="sort-description">'.lang('catalogue.generic.try_your_luck.retry').'</span></span>',
		'specific_version' => FALSE,
		'use_version_param' => FALSE,
		'result' => query_home_random($user, $max_items),
	));

	array_push($sections, array(
		'type' => 'comments',
		'title' => '<i class="fa fa-fw fa-comment"></i> '.lang('catalogue.generic.latest_comments'),
		'specific_version' => TRUE,
		'use_version_param' => TRUE,
		'result' => query_home_comments($user, 5),
	));

	$featured_single_result = query_home_featured_singles($user, 5);
}

$i=0;
$has_some_result = FALSE;
$real_carousels = 0;
foreach($sections as $section){
	$result = $section['result'];
	$uses_swiper = FALSE;
	if ($section['type']=='carousel' || $section['type']=='chapters-carousel' || $section['type']=='chapters-carousel-last-update' || $section['type']=='recommendations') {
		$uses_swiper = TRUE;
	}

	if ($section['type']=='chapters-carousel' && empty($user)) {
		continue;
	} else if (mysqli_num_rows($result)>0 || $section['type']=='static'){
		$has_some_result = TRUE;
		if ($section['type']=='carousel') {
			$real_carousels++;
		}
?>
				<div class="section<?php echo $section['type']=='recommendations' ? ' featured-section' : ''; ?>">
<?php
		if ($section['type']!='recommendations') {
?>
					<h2 class="section-title-main"><?php echo $section['title'].(defined('PAGE_IS_SEARCH') && mysqli_num_rows($result)>0 ? ' ('.mysqli_num_rows($result).')' : ''); ?></h2>
<?php
		}
		if (mysqli_num_rows($result)==0){ //Default search case ('static'), because other types are filtered out
			if ($is_full_catalogue && (mysqli_num_rows($sections[$i+1]['result'])>0 || mysqli_num_rows($sections[$i+2]['result'])>0)) {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo CATALOGUE_NO_RESULTS_FOUND_STRING; ?></div></div>
<?php
			} else {
?>
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('catalogue.generic.no_results_found'); ?></div></div>
<?php
			}
		} else {
?>
					<div class="section-content<?php echo $uses_swiper ? ' swiper' : ''; ?><?php echo ($section['type']=='carousel' || $section['type']=='chapters-carousel' || $section['type']=='chapters-carousel-last-update') ? ' carousel' : ($section['type']=='recommendations' ? ' recommendations theme-dark' : ($section['type']=='comments' ? ' home-comments' : ' catalogue')); ?>">
<?php
			if ($uses_swiper) {
?>
						<div class="<?php echo $uses_swiper ? 'swiper-wrapper' : 'static-wrapper'; ?>">
<?php
			}
			if ($section['type']=='recommendations') {
				if (CATALOGUE_SPECIAL_TITLE!='') {
?>
							<div class="<?php echo $uses_swiper ? 'swiper-slide' : 'static-slide'; ?>">
								<div class="recommendation special-day-header">
									<img class="background" src="<?php echo STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'; ?>" alt="">
									<div class="infoholder" data-swiper-parallax="-30%">
										<div class="dataholder">
											<div class="title"><span class="fa <?php echo CATALOGUE_SPECIAL_ICON; ?>"></span><br><?php echo CATALOGUE_SPECIAL_TITLE; ?></div>
											<div class="divisions"><?php echo CATALOGUE_SPECIAL_DESCRIPTION; ?></div>
										</div>
									</div>
								</div>
							</div>
<?php
				}
				if (is_user_birthday()) {
?>
							<div class="<?php echo $uses_swiper ? 'swiper-slide' : 'static-slide'; ?>">
								<div class="recommendation special-day-header">
									<img class="background" src="<?php echo STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'; ?>" alt="">
									<div class="infoholder" data-swiper-parallax="-30%">
										<div class="dataholder">
											<div class="title"><span class="fa fa-cake-candles"></span><br><?php echo sprintf(lang('catalogue.featured.birthday.title'), $user['username']); ?></div>
											<div class="divisions"><?php echo sprintf(lang('catalogue.featured.birthday.description'), get_user_age(), CURRENT_SITE_NAME); ?></div>
										</div>
									</div>
								</div>
							</div>
<?php
				}
				if (is_advent_days() && mysqli_num_rows(query_current_advent_calendar())>0 && !SITE_IS_HENTAI && !DISABLE_ADVENT) {
?>
							<div class="<?php echo $uses_swiper ? 'swiper-slide' : 'static-slide'; ?>">
<?php
					print_featured_advent();
?>
							</div>
<?php
				} else if ($special_day!==NULL && !empty($force_recommended_ids_list)) {
?>
							<div class="<?php echo $uses_swiper ? 'swiper-slide' : 'static-slide'; ?>">
								<div class="recommendation special-day-header">
									<img class="background" src="<?php echo STATIC_URL.'/images/site/background_dark'.(SITE_IS_HENTAI ? '_hentai' : '').'_hd.jpg'; ?>" alt="">
									<div class="infoholder" data-swiper-parallax="-30%">
										<div class="dataholder">
<?php
					if (file_exists(STATIC_URL.'/images/site/special_day_'.$special_day.'.png')) {
?>
											<img class="special-day-image" src="<?php echo STATIC_URL.'/images/site/special_day_'.$special_day.'.png'; ?>" alt="">
<?php
					}
?>
											<div class="title"><?php echo $special_day_title; ?></div>
											<div class="divisions"><?php echo $special_day_description; ?></div>
										</div>
									</div>
								</div>
							</div>
<?php
				}
			}
			while ($row = mysqli_fetch_assoc($result)){
?>
							<div class="<?php echo isset($row['best_status']) ? 'status-'.get_status($row['best_status']).' ' : ''; ?><?php echo $uses_swiper ? 'swiper-slide' : 'static-slide'; ?>">
<?php
				if ($section['type']=='recommendations') {
					print_featured_item($row, $section['title'], $section['specific_version'], $section['use_version_param'], !empty($force_recommended_ids_list));
				} else if ($section['type']=='chapters-carousel'){
					print_chapter_item($row);
				} else if ($section['type']=='chapters-carousel-last-update'){
					print_chapter_item_last_update($row);
				} else if ($section['type']=='comments') {
					print_comment_home($row);
				} else {
					print_carousel_item($row, $section['specific_version'], $section['use_version_param']);
				}
?>
							</div>
<?php
			}
?>
						</div>
<?php
			if ($uses_swiper) {
				if ($section['type']=='recommendations') {
?>
						<div class="swiper-pagination"></div>
<?php
				}
?>
						<div class="swiper-button-prev"></div>
						<div class="swiper-button-next"></div>
					</div>
<?php
			}
		}
?>
				</div>
<?php
	}
	mysqli_free_result($result);

	if (!defined('PAGE_IS_SEARCH') && $real_carousels==2 && $i<count($sections)-1 && $row = mysqli_fetch_assoc($featured_single_result)) {
?>
				<div class="section">
					<h2 class="section-title-main"><i class="fa fa-fw fa-torii-gate"></i> <?php echo CATALOGUE_FEATURED_SINGLE_STRING; ?></h2>
					<div class="section-content theme-dark">
<?php
		print_featured_item_single($row, FALSE, FALSE);
?>
					</div>
				</div>
<?php
		$real_carousels=0;
	}
	$i++;
}

if (!$has_some_result) {
?>
				<div class="section">
					<div class="section-content section-empty"><div><i class="fa fa-fw fa-ban"></i><br><?php echo lang('catalogue.generic.nothing_at_all'); ?></div></div>
				</div>
<?php
}

if (defined('PAGE_IS_SEARCH')) {
	require_once(__DIR__.'/../common/footer_text.inc.php');
} else {
?>
				<div id="bottom-navigation">
					<a class="normal-button" href="<?php echo SITE_BASE_URL.lang('url.random'); ?>" rel="nofollow"><?php echo lang('catalogue.generic.random_button'); ?> <i class="fa fa-fw fa-shuffle"></i></a>
					<a class="normal-button" href="<?php echo SITE_BASE_URL.lang('url.search'); ?>"><?php echo lang('catalogue.generic.explore_button'); ?> <i class="fa fa-fw fa-arrow-right"></i></a>
				</div>
<?php
}
?>

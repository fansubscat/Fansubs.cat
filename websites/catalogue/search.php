<?php
define('PAGE_STYLE_TYPE', 'catalogue');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

define('PAGE_TITLE', lang('catalogue.search.page_title'));

if (is_robot()) {
	define('PAGE_EXTRA_BODY_CLASS', 'has-search-results');
}

$_GET['query']=str_replace('%2F', '/', isset($_GET['query']) ? $_GET['query'] : '');

define('PAGE_PATH', lang('url.search').(isset($_GET['query']) ? '/'.urlencode($_GET['query']) : ''));
define('PAGE_IS_SEARCH', TRUE);
if (!is_robot()) {
	define('SKIP_FOOTER', TRUE);
}

require_once(__DIR__.'/../common/header.inc.php');

function get_time_from_duration($duration){
	if ($duration==120) {
		return '2:00:00+';
	}
	$minutes = $duration % 60;
	return intdiv($duration, 60).':'. ($minutes>9 ? $minutes : '0'.$minutes).':00';
}

function get_pages_from_duration($duration){
	if ($duration==100) {
		return lang('catalogue.search.more_than_max_pages');
	}
	return sprintf(lang('catalogue.search.number_of_pages'), $duration);
}

function get_rating_from_integer($value){
	if ($value==1) {
		return '+7';
	} else if ($value==2) {
		return '+13';
	} else if ($value==3) {
		return '+16';
	} else if ($value==4) {
		return '+18';
	} else {
		return 'TP';
	}
}

function get_score_from_integer($value){
	if ($value==0) {
		return '-';
	}
	return number_format($value/(float)10, 1, ',');
}

function get_year_from_integer($value){
	if ($value==1950) {
		return '-';
	}
	return $value;
}

//Check and restore search parameters
if (isset($_GET['type'])) {
	$param_type = $_GET['type'];
} else {
	$param_type = 'all';
}
if (isset($_GET['status']) && is_array($_GET['status']) && count($_GET['status'])>0) {
	$param_status_array = $_GET['status'];
} else {
	if ((!empty($user) && $user['show_cancelled_projects']) || (empty($user) && !empty($_COOKIE['show_cancelled_projects']))) {
		$param_status_array = array(1,3,2,4,5);
	} else {
		$param_status_array = array(1,3,2);
	}
}
if (isset($_GET['min_duration']) && preg_match("/\\d*/", $_GET['min_duration']) && $_GET['min_duration']>=CATALOGUE_MINIMUM_DURATION && $_GET['min_duration']<=CATALOGUE_MAXIMUM_DURATION) {
	$param_min_duration = $_GET['min_duration'];
} else {
	$param_min_duration = CATALOGUE_MINIMUM_DURATION;
}
if (isset($_GET['max_duration']) && preg_match("/\\d*/", $_GET['max_duration']) && $_GET['max_duration']>=CATALOGUE_MINIMUM_DURATION && $_GET['max_duration']<=CATALOGUE_MAXIMUM_DURATION) {
	$param_max_duration = $_GET['max_duration'];
} else {
	$param_max_duration = CATALOGUE_MAXIMUM_DURATION;
}
if (isset($_GET['min_rating']) && preg_match("/\\d/", $_GET['min_rating']) && $_GET['min_rating']>=0 && $_GET['min_rating']<=4) {
	$param_min_rating = $_GET['min_rating'];
} else {
	$param_min_rating = 0;
}
if (isset($_GET['max_rating']) && preg_match("/\\d/", $_GET['max_rating']) && $_GET['max_rating']>=0 && $_GET['max_rating']<=4) {
	$param_max_rating = $_GET['max_rating'];
} else {
	$param_max_rating = 4;
}
if (isset($_GET['min_score']) && preg_match("/\\d*/", $_GET['min_score']) && $_GET['min_score']>=0 && $_GET['min_score']<=100) {
	$param_min_score = $_GET['min_score'];
} else {
	$param_min_score = 0;
}
if (isset($_GET['max_score']) && preg_match("/\\d*/", $_GET['max_score']) && $_GET['max_score']>=0 && $_GET['max_score']<=100) {
	$param_max_score = $_GET['max_score'];
} else {
	$param_max_score = 100;
}
if (isset($_GET['min_year']) && preg_match("/\\d\\d\\d\\d/", $_GET['min_year']) && $_GET['min_year']>=1950 && $_GET['min_year']<=date('Y')) {
	$param_min_year = $_GET['min_year'];
} else {
	$param_min_year = 1950;
}
if (isset($_GET['max_year']) && preg_match("/\\d\\d\\d\\d/", $_GET['max_year']) && $_GET['max_year']>=1950 && $_GET['max_year']<=date('Y')) {
	$param_max_year = $_GET['max_year'];
} else {
	$param_max_year = date('Y');
}
if (isset($_GET['fansub'])) {
	$param_fansub = $_GET['fansub'];
} else {
	if ((!empty($user) && count($user['blacklisted_fansub_ids'])>0) || (empty($user) && count(get_cookie_blacklisted_fansub_ids())>0)) {
		$param_fansub = -2;
	} else {
		$param_fansub = -1;
	}
}
if (isset($_GET['hide_lost_content']) && $_GET['hide_lost_content']==1) {
	$param_hide_lost_content = TRUE;
} else {
	if ((!empty($user) && $user['show_lost_projects']) || (empty($user) && !empty($_COOKIE['show_lost_projects']))) {
		$param_hide_lost_content = FALSE;
	} else {
		$param_hide_lost_content = TRUE;
	}
}
if (!isset($_GET['full_catalogue']) || (isset($_GET['full_catalogue']) && $_GET['full_catalogue']==1)) {
	$param_show_full_catalogue = TRUE;
} else {
	$param_show_full_catalogue = FALSE;
}
if (isset($_GET['content_types']) && is_array($_GET['content_types']) && count($_GET['content_types'])>0) {
	$param_content_types_array = $_GET['content_types'];
} else {
	$param_content_types_array = array('fansub','fandub');
}
if (isset($_GET['demographics']) && is_array($_GET['demographics']) && count($_GET['demographics'])>0) {
	$param_demographics_array = $_GET['demographics'];
}
if (isset($_GET['origins']) && is_array($_GET['origins']) && count($_GET['origins'])>0) {
	$param_origins_array = $_GET['origins'];
}
if (isset($_GET['genres_include']) && is_array($_GET['genres_include']) && count($_GET['genres_include'])>0) {
	$param_genres_include_array = $_GET['genres_include'];
}
if (isset($_GET['genres_exclude']) && is_array($_GET['genres_exclude']) && count($_GET['genres_exclude'])>0) {
	$param_genres_exclude_array = $_GET['genres_exclude'];
}
?>
					<div class="search-layout<?php echo !empty($_GET['focus']) ? ' search-layout-visible' : ''; ?>">
						<input class="search-base-url" type="hidden" value="<?php echo lang('url.search'); ?>">
						<div class="search-filter-title"><?php echo lang('catalogue.search.filters'); ?></div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="catalogue-search-query"><?php echo lang('catalogue.search.query'); ?></label>
							<input id="catalogue-search-query" type="text" oninput="loadSearchResults();" value="<?php echo isset($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="<?php echo lang('catalogue.search.query.placeholder'); ?>"<?php echo !empty($_GET['focus']) ? ' autofocus' : ''; ?>>
							<label><?php echo lang('catalogue.search.type'); ?></label>
							<div id="catalogue-search-type" class="singlechoice-selector singlechoice-type">
								<div class="singlechoice-button<?php echo $param_type=='all' ? ' singlechoice-selected' : ''; ?>" onclick="singlechoiceChange(this);" data-value="all"><i class="fa fa-fw fa-grip"></i><?php echo lang('catalogue.search.type.all'); ?></div>
								<div class="singlechoice-button<?php echo $param_type==CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID ? ' singlechoice-selected' : ''; ?>" onclick="singlechoiceChange(this);" data-value="<?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_DB_ID; ?>"><i class="fa fa-fw <?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_ICON; ?>"></i><?php echo CATALOGUE_ITEM_SUBTYPE_SINGLE_NAME; ?></div>
								<div class="singlechoice-button<?php echo $param_type==CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID ? ' singlechoice-selected' : ''; ?>" onclick="singlechoiceChange(this);" data-value="<?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_DB_ID; ?>"><i class="fa fa-fw <?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_ICON; ?>"></i><?php echo CATALOGUE_ITEM_SUBTYPE_SERIALIZED_NAME; ?></div>
							</div>
							<label><?php echo lang('catalogue.search.status'); ?></label>
<?php
$statuses=array(1,3,2,4,5);
foreach ($statuses as $status_id) {
?>
							<div class="search-checkboxes search-status status-<?php echo get_status($status_id); ?>">
								<input id="catalogue-search-status-<?php echo $status_id; ?>" data-id="<?php echo $status_id; ?>" type="checkbox" oninput="loadSearchResults();"<?php echo in_array($status_id, $param_status_array) ? ' checked' : ''; ?>>
								<label for="catalogue-search-status-<?php echo $status_id; ?>" class="for-checkbox"><span class="status-indicator <?php echo get_status_css_icons($status_id); ?>"></span> <?php echo get_status_description_short($status_id); ?></label>
							</div>
<?php
}
?>
							<label><?php echo lang('catalogue.search.average_duration'); ?></label>
							<div id="catalogue-search-duration" class="double-slider-container">
								<input id="duration-from-slider" class="double-slider-from" type="range" value="<?php echo $param_min_duration; ?>" min="<?php echo CATALOGUE_MINIMUM_DURATION; ?>" max="<?php echo CATALOGUE_MAXIMUM_DURATION; ?>" onchange="loadSearchResults();">
								<input id="duration-to-slider" class="double-slider-to" type="range" value="<?php echo $param_max_duration; ?>" min="<?php echo CATALOGUE_MINIMUM_DURATION; ?>" max="<?php echo CATALOGUE_MAXIMUM_DURATION; ?>" onchange="loadSearchResults();">
								<div id="duration-from-input" data-value-formatting="<?php echo CATALOGUE_DURATION_SLIDER_FORMATTING; ?>" class="double-slider-input-from"><?php echo CATALOGUE_DURATION_SLIDER_FORMATTING=='pages' ? get_pages_from_duration($param_min_duration) : get_time_from_duration($param_min_duration); ?></div>
								<div id="duration-to-input" data-value-formatting="<?php echo CATALOGUE_DURATION_SLIDER_FORMATTING; ?>-max" class="double-slider-input-to"><?php echo CATALOGUE_DURATION_SLIDER_FORMATTING=='pages' ? get_pages_from_duration($param_max_duration) : get_time_from_duration($param_max_duration); ?></div>
							</div>
<?php
if (!SITE_IS_HENTAI) {
?>
							<label><?php echo lang('catalogue.search.age_rating'); ?></label>
							<div id="catalogue-search-rating" class="double-slider-container">
								<input id="rating-from-slider" class="double-slider-from" type="range" value="<?php echo $param_min_rating; ?>" min="0" max="4" onchange="loadSearchResults();">
								<input id="rating-to-slider" class="double-slider-to" type="range" value="<?php echo $param_max_rating; ?>" min="0" max="4" onchange="loadSearchResults();">
								<div id="rating-from-input" data-value-formatting="rating" class="double-slider-input-from"><?php echo get_rating_from_integer($param_min_rating); ?></div>
								<div id="rating-to-input" data-value-formatting="rating" class="double-slider-input-to"><?php echo get_rating_from_integer($param_max_rating); ?></div>
							</div>
<?php
}
?>
							<label><?php echo sprintf(lang('catalogue.search.external_score'), CATALOGUE_SCORE_SOURCE); ?></label>
							<div id="catalogue-search-score" class="double-slider-container">
								<input id="score-from-slider" class="double-slider-from" type="range" value="<?php echo $param_min_score; ?>" min="0" max="100" onchange="loadSearchResults();">
								<input id="score-to-slider" class="double-slider-to" type="range" value="<?php echo $param_max_score; ?>" min="0" max="100" onchange="loadSearchResults();">
								<div id="score-from-input" data-value-formatting="score" class="double-slider-input-from"><?php echo get_score_from_integer($param_min_score); ?></div>
								<div id="score-to-input" data-value-formatting="score" class="double-slider-input-to"><?php echo get_score_from_integer($param_max_score); ?></div>
							</div>
							<label><?php echo CATALOGUE_FIRST_PUBLISH_STRING; ?></label>
							<div id="catalogue-search-year" class="double-slider-container">
								<input id="year-from-slider" class="double-slider-from" type="range" value="<?php echo $param_min_year; ?>" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<input id="year-to-slider" class="double-slider-to" type="range" value="<?php echo $param_max_year; ?>" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<div id="year-from-input" data-value-formatting="year" class="double-slider-input-from"><?php echo get_year_from_integer($param_min_year); ?></div>
								<div id="year-to-input" data-value-formatting="year" class="double-slider-input-to"><?php echo get_year_from_integer($param_max_year); ?></div>
							</div>
							<label for="catalogue-search-fansub"><?php echo lang('catalogue.search.fansub'); ?></label>
							<select id="catalogue-search-fansub" onchange="loadSearchResults();">
<?php
if ((!empty($user) && count($user['blacklisted_fansub_ids'])>0) || (empty($user) && count(get_cookie_blacklisted_fansub_ids())>0)) {
?>
								<option value="-2"<?php echo $param_fansub==-2 ? ' selected' : ''; ?>><?php echo lang('catalogue.search.fansub.all_but_blacklist'); ?></option>
								<option value="-1"<?php echo $param_fansub==-1 ? ' selected' : ''; ?>><?php echo lang('catalogue.search.fansub.all_including_blacklist'); ?></option>
<?php
} else {
?>
								<option value="-1"<?php echo $param_fansub==-1 ? ' selected' : ''; ?>><?php echo lang('catalogue.search.fansub.all'); ?></option>
<?php
}
$result = query_all_fansubs_with_versions($user);
while ($row = mysqli_fetch_assoc($result)) {
?>
								<option value="<?php echo $row['slug']; ?>"<?php echo $param_fansub==$row['slug'] ? ' selected' : ''; ?>><?php echo $row['name']; ?></option>
<?php
}
?>
							</select>
<?php
if (CATALOGUE_HAS_ORIGIN && !SITE_IS_HENTAI) {
?>
							<label><?php echo lang('catalogue.search.category'); ?></label>
<?php
	$origins=array(
		'manga' => lang('catalogue.search.category.manga'),
		'manhua' => lang('catalogue.search.category.manhua'),
		'manhwa' => lang('catalogue.search.category.manhwa'),
		'novel' => lang('catalogue.search.category.light_novel'),
	);
	foreach ($origins as $key => $value) {
?>
							<div class="search-checkboxes search-origins">
								<input id="catalogue-search-origins-<?php echo $key; ?>" data-id="<?php echo $key; ?>" type="checkbox" oninput="loadSearchResults();"<?php echo (isset($param_origins_array) && !in_array($key, $param_origins_array)) ? '' : ' checked'; ?>>
								<label for="catalogue-search-origins-<?php echo $key; ?>" class="for-checkbox"><?php echo $value; ?></label>
							</div>
<?php
	}
} else if (CATALOGUE_HAS_FANDUBS) {
?>
							<label><?php echo lang('catalogue.search.content_type'); ?></label>
							<div class="search-checkboxes search-content_types">
								<input id="catalogue-search-content_types-dub" data-id="fandub" type="checkbox" oninput="loadSearchResults();"<?php echo (isset($param_content_types_array) && !in_array('fandub', $param_content_types_array)) ? '' : ' checked'; ?>>
								<label for="catalogue-search-content_types-dub" class="for-checkbox"><?php echo lang('catalogue.search.content_type.dubbed'); ?></label>
							</div>
							<div class="search-checkboxes search-content_types">
								<input id="catalogue-search-content_types-sub" data-id="fansub" type="checkbox" oninput="loadSearchResults();"<?php echo (isset($param_content_types_array) && !in_array('fansub', $param_content_types_array)) ? '' : ' checked'; ?>>
								<label for="catalogue-search-content_types-sub" class="for-checkbox"><?php echo lang('catalogue.search.content_type.subbed'); ?></label>
							</div>
<?php
}
?>
							<label><?php echo lang('catalogue.search.include_also'); ?></label>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-lost" type="checkbox" oninput="loadSearchResults();"<?php echo $param_hide_lost_content ? '' : ' checked'; ?>>
								<label for="catalogue-search-include-lost" class="for-checkbox"><?php echo lang('catalogue.search.include_also.lost_chapters'); ?></label>
							</div>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-full-catalogue" type="checkbox" oninput="loadSearchResults();"<?php echo $param_show_full_catalogue ? ' checked' : ''; ?>>
								<label for="catalogue-search-include-full-catalogue" class="for-checkbox"><?php echo lang('catalogue.search.include_also.other_results'); ?></label>
							</div>
<?php
if (CATALOGUE_HAS_DEMOGRAPHIES && !SITE_IS_HENTAI) {
?>
							<label><?php echo lang('catalogue.search.demography'); ?></label>
<?php
	$result=query_filter_demographics();
	while ($row=mysqli_fetch_assoc($result)) {
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-<?php echo $row['id']; ?>" data-id="<?php echo $row['id']; ?>" type="checkbox" oninput="loadSearchResults();"<?php echo (isset($param_demographics_array) && !in_array($row['id'], $param_demographics_array)) ? '' : ' checked'; ?>>
								<label for="catalogue-search-demographics-<?php echo $row['id']; ?>" class="for-checkbox"><?php echo $row['name']; ?></label>
							</div>
<?php
	}
	mysqli_free_result($result);
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-not-set" data-id="-1" type="checkbox" oninput="loadSearchResults();"<?php echo (isset($param_demographics_array) && !in_array(-1, $param_demographics_array)) ? '' : ' checked'; ?>>
								<label for="catalogue-search-demographics-not-set" class="for-checkbox">No definida</label>
							</div>
<?php
}
?>
							<label><?php echo lang('catalogue.search.genres'); ?></label>
<?php
$result=query_filter_genders();
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include<?php echo (isset($param_genres_include_array) && in_array($row['id'], $param_genres_include_array)) ? ' tristate-selected' : ''; ?>" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-exclude<?php echo (isset($param_genres_exclude_array) && in_array($row['id'], $param_genres_exclude_array)) ? ' tristate-selected' : ''; ?>" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
							<label><?php echo lang('catalogue.search.themes'); ?></label>
<?php
$result=query_filter_themes();
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include<?php echo (isset($param_genres_include_array) && in_array($row['id'], $param_genres_include_array)) ? ' tristate-selected' : ''; ?>" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-exclude<?php echo (isset($param_genres_exclude_array) && in_array($row['id'], $param_genres_exclude_array)) ? ' tristate-selected' : ''; ?>" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
						</form>
					</div>
					<div class="search-layout-toggle-button<?php echo !empty($_GET['focus']) ? ' search-layout-toggle-button-visible' : ''; ?>"><i class="fa fa-fw fa-chevron-right" onclick="toggleSearchLayout();"></i></div>
					<div class="results-layout catalogue-search<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	define('ROBOT_INCLUDED', TRUE);
	if (!empty($_GET['fansub'])) {
		$_POST['fansub']=$_GET['fansub'];
	}
	include(__DIR__.'/results.php');
	define('SKIP_FOOTER', TRUE);
}
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message"><?php echo lang('catalogue.search.loading_results'); ?></div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message"><?php echo lang('catalogue.error_contacting_server'); ?></div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

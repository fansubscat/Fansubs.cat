<?php
define('PAGE_STYLE_TYPE', 'news');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

define('PAGE_TITLE', lang('news.search.page_title'));

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

function get_month_from_yyyy_mm($yyyymm){
	$start_date = strtotime(date(NEWS_STARTING_MONTH.'-01'));
	$start_year = date('Y', $start_date);
	$start_month = date('m', $start_date);
	$passed_date = strtotime(date($yyyymm.'-01'));
	$passed_year = date('Y', $passed_date);
	$passed_month = date('m', $passed_date);
	return (($passed_year - $start_year) * 12) + ($passed_month - $start_month);
}

$max_date = get_month_from_yyyy_mm(date('Y-m'));

//Check and restore search parameters
if (isset($_GET['min_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_GET['min_month']) && $_GET['min_month']>=NEWS_STARTING_MONTH) {
	$param_min_month_checked = $_GET['min_month'];
	$param_min_month = get_month_from_yyyy_mm($param_min_month_checked);
} else {
	$param_min_month_checked = NEWS_STARTING_MONTH;
	$param_min_month = get_month_from_yyyy_mm(NEWS_STARTING_MONTH);
}
if (isset($_GET['max_month']) && preg_match("/\\d\\d\\d\\d\\-\\d\\d/", $_GET['max_month']) && $_GET['max_month']<=date('Y-m')) {
	$param_max_month_checked = $_GET['max_month'];
	$param_max_month = get_month_from_yyyy_mm($param_max_month_checked);
} else {
	$param_max_month_checked = date('Y-m');
	$param_max_month = get_month_from_yyyy_mm(date('Y-m'));
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
?>
					<div class="search-layout<?php echo !empty($_GET['focus']) ? ' search-layout-visible' : ''; ?>">
						<input class="search-base-url" type="hidden" value="<?php echo lang('url.search'); ?>">
						<input class="search-min-month" type="hidden" value="<?php echo NEWS_STARTING_MONTH; ?>">
						<div class="search-filter-title"><?php echo lang('news.search.filters'); ?></div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="news-search-query"><?php echo lang('news.search.query'); ?></label>
							<input id="news-search-query" type="text" oninput="loadSearchResults(1);" value="<?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="<?php echo lang('news.search.query.placeholder'); ?>"<?php echo !empty($_GET['focus']) ? ' autofocus' : ''; ?>>
							<label for="news-search-date"><?php echo lang('news.search.publish_month'); ?></label>
							<div id="news-search-date" class="double-slider-container">
								<input id="date-from-slider" class="double-slider-from" type="range" value="<?php echo $param_min_month; ?>" min="0" max="<?php echo $max_date; ?>" onchange="loadSearchResults(1);">
								<input id="date-to-slider" class="double-slider-to" type="range" value="<?php echo $param_max_month; ?>" min="0" max="<?php echo $max_date; ?>" onchange="loadSearchResults(1);">
								<div id="date-from-input" value-formatting="date" class="double-slider-input-from"><?php echo date(lang('date.month_year_short'),strtotime(date($param_min_month_checked.'-01'))); ?></div>
								<div id="date-to-input" value-formatting="date" class="double-slider-input-to"><?php echo date(lang('date.month_year_short'),strtotime(date($param_max_month_checked.'-01'))); ?></div>
							</div>
							<label for="news-search-fansub"><?php echo lang('news.search.fansub'); ?></label>
							<select id="news-search-fansub" onchange="loadSearchResults(1);">
<?php
if ((!empty($user) && count($user['blacklisted_fansub_ids'])>0) || (empty($user) && count(get_cookie_blacklisted_fansub_ids())>0)) {
?>
								<option value="-2"<?php echo $param_fansub==-2 ? ' selected' : ''; ?>><?php echo lang('news.search.fansub.all_but_blacklist'); ?></option>
								<option value="-1"<?php echo $param_fansub==-1 ? ' selected' : ''; ?>><?php echo lang('news.search.fansub.all_including_blacklist'); ?></option>
								<option value="-3"<?php echo $param_fansub==-3 ? ' selected' : ''; ?>><?php echo sprintf(lang('news.search.fansub.only_site'), CURRENT_SITE_NAME); ?></option>
<?php
} else {
?>
								<option value="-1"<?php echo $param_fansub==-1 ? ' selected' : ''; ?>><?php echo lang('news.search.fansub.all'); ?></option>
								<option value="-3"<?php echo $param_fansub==-3 ? ' selected' : ''; ?>><?php echo sprintf(lang('news.search.fansub.only_site'), CURRENT_SITE_NAME); ?></option>
<?php
}
$result = query_all_fansubs_with_news(!empty($user) ? $user : NULL);
while ($row = mysqli_fetch_assoc($result)) {
?>
								<option value="<?php echo $row['slug']; ?>"<?php echo $param_fansub==$row['slug'] ? ' selected' : ''; ?>><?php echo $row['name']; ?></option>
<?php
}
?>
							</select>
						</form>
					</div>
					<div class="search-layout-toggle-button<?php echo !empty($_GET['focus']) ? ' search-layout-toggle-button-visible' : ''; ?>" onclick="toggleSearchLayout();"><i class="fa fa-fw fa-chevron-right"></i></div>
					<div class="results-layout news-search<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	if (!empty($_GET['fansub'])) {
		$_POST['fansub']=$_GET['fansub'];
	}
	include(__DIR__.'/results.php');
	define('SKIP_FOOTER', TRUE);
}
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message"><?php echo lang('news.loading_results'); ?></div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message"><?php echo lang('news.error_contacting_server'); ?></div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

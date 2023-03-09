<?php
define('PAGE_STYLE_TYPE', 'news');
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

define('PAGE_TITLE', 'Resultats de la cerca');

if (is_robot()) {
	define('PAGE_EXTRA_BODY_CLASS', 'has-search-results');
}

define('PAGE_PATH', '/cerca'.(!empty($_GET['query']) ? '/'.$_GET['query'] : ''));
define('PAGE_IS_SEARCH', TRUE);
define('SKIP_FOOTER', TRUE);

require_once("../common.fansubs.cat/header.inc.php");

$start_date = strtotime(date('2003-05-01'));
$today_date = strtotime(date('Y-m-01'));

$start_year = date('Y', $start_date);
$today_year = date('Y', $today_date);

$start_month = date('m', $start_date);
$today_month = date('m', $today_date);

$max_date = (($today_year - $start_year) * 12) + ($today_month - $start_month);
?>
					<div class="search-layout">
						<input class="search-base-url" type="hidden" value="<?php echo SITE_IS_HENTAI ? '/hentai/cerca' : '/cerca'; ?>">
						<div class="search-filter-title">Filtres de cerca</div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="news-search-query">Text a cercar</label>
							<input id="news-search-query" type="text" oninput="loadSearchResults(1);" value="<?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Cerca...">
							<label for="news-search-date">Mes de publicació</label>
							<div id="news-search-date" class="double-slider-container">
								<input id="date-from-slider" class="double-slider-from" type="range" value="0" min="0" max="<?php echo $max_date; ?>" onchange="loadSearchResults(1);">
								<input id="date-to-slider" class="double-slider-to" type="range" value="<?php echo $max_date; ?>" min="0" max="<?php echo $max_date; ?>" onchange="loadSearchResults(1);">
								<div id="date-from-input" value-formatting="date" class="double-slider-input-from">05/2003</div>
								<div id="date-to-input" value-formatting="date" class="double-slider-input-to"><?php echo date('m/Y'); ?></div>
							</div>
							<label>Inclou-hi també...</label>
<?php
if ((!empty($user) && count($user['blacklisted_fansub_ids'])>0) || (empty($user) && count(get_cookie_blacklisted_fansub_ids())>0)) {
?>
							<div class="search-checkboxes">
								<input id="news-search-include-blacklisted" type="checkbox" oninput="loadSearchResults(1);" checked>
								<label for="news-search-include-blacklisted" class="for-checkbox">Fansubs a la llista negra</label>
							</div>
<?php
}
?>
							<div class="search-checkboxes">
								<input id="news-search-include-own" type="checkbox" oninput="loadSearchResults(1);" checked>
								<label for="news-search-include-own" class="for-checkbox">Notícies de Fansubs.cat</label>
							</div>
						</form>
					</div>
					<div class="search-layout-toggle-button" onclick="toggleSearchLayout();"><i class="fa fa-fw fa-chevron-right"></i></div>
					<div class="results-layout news-search<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	include("results.php");
}
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message">S’estan carregant les notícies...</div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

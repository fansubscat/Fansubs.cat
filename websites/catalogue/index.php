<?php
$style_type='catalogue';
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

if (!empty($_GET['search'])) {
	$hide_search=TRUE;
}

if (!empty($_GET['hentai'])) {
	$page_title='Hentai';
	$hentai_subquery=" AND s.rating='XXX'";
} else {
	$hentai_subquery=" AND (s.rating IS NULL OR s.rating<>'XXX')";
}

if ($cat_config['items_type']=='liveaction') {
	$hide_hentai=TRUE;
}

if (!empty($_GET['hentai'])) {
	if (empty($user) && !is_robot()) {
		header("Location: $users_url/inicia-la-sessio");
		die();
	} else if (!is_robot() && !is_adult()) {
		$_GET['hentai']=0;
		$_GET['code']=403;
		http_response_code(403);
		include('error.php');
		die();
	}
}

if (is_robot() && empty($_GET['search'])) {
	$extra_body_class='has-carousel';
}

require_once("../common.fansubs.cat/header.inc.php");

if (!empty($_GET['search'])) {
	$skip_footer=TRUE;
?>
					<div class="search-layout<?php echo !empty($_GET['search']) ? '' : ' hidden'; ?>">
						<input class="search-base-url" type="hidden" value="<?php echo !empty($_GET['hentai']) ? '/hentai/cerca' : '/cerca'; ?>">
						<div class="search-filter-title">Filtres de cerca</div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="catalogue-search-query">Text a cercar</label>
							<input id="catalogue-search-query" type="text" oninput="loadSearchResults();" value="<?php echo !empty($_GET['query']) ? htmlentities($_GET['query']) : ''; ?>">
							<label for="catalogue-search-type">Tipus</label>
							<input id="catalogue-search-type" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-status">Estat</label>
							<input id="catalogue-search-status" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-duration">Durada</label>
							<div id="catalogue-search-duration" class="double-slider-container">
								<input id="duration-from-slider" class="double-slider-from" type="range" value="0" min="0" max="120" onchange="loadSearchResults();">
								<input id="duration-to-slider" class="double-slider-to" type="range" value="120" min="0" max="120" onchange="loadSearchResults();">
								<div id="duration-from-input" value-formatting="time" class="double-slider-input-from">0:00:00</div>
								<div id="duration-to-input" value-formatting="time-max" class="double-slider-input-to">2:00:00+</div>
							</div>
							<label for="catalogue-search-rating">Valoració per edats</label>
							<div id="catalogue-search-rating" class="double-slider-container">
								<input id="rating-from-slider" class="double-slider-from" type="range" value="0" min="0" max="4" onchange="loadSearchResults();">
								<input id="rating-to-slider" class="double-slider-to" type="range" value="4" min="0" max="4" onchange="loadSearchResults();">
								<div id="rating-from-input" value-formatting="rating" class="double-slider-input-from">TP</div>
								<div id="rating-to-input" value-formatting="rating" class="double-slider-input-to">+18</div>
							</div>
							<label for="catalogue-search-score">Puntuació a MyAnimeList</label>
							<div id="catalogue-search-score" class="double-slider-container">
								<input id="score-from-slider" class="double-slider-from" type="range" value="0" min="0" max="100" onchange="loadSearchResults();">
								<input id="score-to-slider" class="double-slider-to" type="range" value="100" min="0" max="100" onchange="loadSearchResults();">
								<div id="score-from-input" value-formatting="score" class="double-slider-input-from">0,0</div>
								<div id="score-to-input" value-formatting="score" class="double-slider-input-to">10,0</div>
							</div>
							<label>Inclou també...</label>
							<div>
								<input id="catalogue-search-include-blacklisted" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-blacklisted" class="for-checkbox">Fansubs ocultats</label>
							</div>
							<div>
								<input id="catalogue-search-include-lost" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-lost" class="for-checkbox">Amb capítols perduts</label>
							</div>
<?php
	if (is_adult() && empty($_GET['hentai'])) {
?>
							<div>
								<input id="catalogue-search-include-hentai" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-hentai" class="for-checkbox">Hentai</label>
							</div>
<?php
	}
?>
							<label for="catalogue-search-genre">Gèneres</label>
							<input id="catalogue-search-genre" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-demography">Demografies</label>
							<input id="catalogue-search-demography" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-theme">Temàtiques</label>
							<input id="catalogue-search-theme" type="text" oninput="loadSearchResults();">
						</form>
					</div>
<?php
}
?>
					<div class="results-layout catalogue-<?php echo !empty($_GET['search']) ? 'search' : 'index'; ?><?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	if ($cat_config['items_type']=='liveaction' || !empty($_GET['hentai'])) {
		$number=25;
	} else {
		$number=50;
	}
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt FROM series s WHERE s.type='${cat_config['items_type']}'$hentai_subquery AND EXISTS (SELECT id FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
	if (empty($_GET['search'])) {
?>
						<div class="section">
							<div class="site-message absolutely-real"><?php printf($cat_config['site_robot_message'.(!empty($_GET['hentai']) ? '_hentai' : '')], $totalnumber); ?></div>
						</div>
<?php
	}
	include("results.php");
} 
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message">S’estan carregant les darreres novetats...</div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

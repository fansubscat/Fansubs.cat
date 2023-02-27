<?php
$style_type='catalogue';
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

if (!empty($_GET['search'])) {
	$hide_search=TRUE;
}

require_once("../common.fansubs.cat/header.inc.php");

if (!empty($_GET['search'])) {
?>
					<div class="search-layout<?php echo !empty($_GET['search']) ? '' : ' hidden'; ?>">
						<div class="search-filter-title">Filtres de cerca</div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="catalogue-search-query">Text a cercar</label>
							<input id="catalogue-search-query" type="text" oninput="loadSearchResults();" value="<?php echo !empty($_GET['query']) ? htmlentities($_GET['query']) : ''; ?>">
							<label for="catalogue-search-type">Tipus</label>
							<input id="catalogue-search-type" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-status">Estat</label>
							<input id="catalogue-search-status" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-duration">Durada</label>
							<input id="catalogue-search-duration" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-rating">Valoració per edats</label>
							<input id="catalogue-search-rating" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-score">Puntuació a MyAnimeList</label>
							<input id="catalogue-search-score" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-genre">Gèneres</label>
							<input id="catalogue-search-genre" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-demography">Demografies</label>
							<input id="catalogue-search-demography" type="text" oninput="loadSearchResults();">
							<label for="catalogue-search-theme">Temàtiques</label>
							<input id="catalogue-search-theme" type="text" oninput="loadSearchResults();">
							<label>Inclou també</label>
							<div>
								<input id="catalogue-search-include-blacklisted" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-blacklisted" class="for-checkbox">Fansubs de la llista negra</label>
							</div>
							<div>
								<input id="catalogue-search-include-lost" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-lost" class="for-checkbox">Projectes amb capítols perduts</label>
							</div>
							<div>
								<input id="catalogue-search-include-explicit" type="checkbox" oninput="loadSearchResults();">
								<label for="catalogue-search-include-explicit" class="for-checkbox">Contingut pornogràfic</label>
							</div>
						</form>
					</div>
<?php
}
?>
					<div class="results-layout catalogue-<?php echo !empty($_GET['search']) ? 'search' : 'index'; ?><?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	if ($cat_config['items_type']=='liveaction') {
		$number=25;
	} else {
		$number=50;
	}
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt FROM series s WHERE s.type='${cat_config['items_type']}' AND EXISTS (SELECT id FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
?>
						<div class="section">
							<div class="site-message absolutely-real"><?php printf($cat_config['site_robot_message'], $totalnumber); ?></div>
						</div>
<?php
	include("results.php");
} 
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message"><?php echo !empty($_GET['search']) ? (empty($_GET['query']) ? 'S’està carregant el catàleg sencer...' : 'S’estan carregant els resultats de la cerca...') : 'S’estan carregant les darreres novetats...'; ?></div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

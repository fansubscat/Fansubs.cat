<?php
$style_type='catalogue';
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

$page_title='Resultats de la cerca';

validate_hentai();

if ($is_hentai_site) {
	$page_title.=' | Hentai';
}

$social_url=($is_hentai_site ? '/hentai' : '').'/cerca/'.(!empty($_GET['query']) ? urldecode($_GET['query']) : '');

$is_searching=TRUE;
$skip_footer=TRUE;

require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="search-layout">
						<input class="search-base-url" type="hidden" value="<?php echo $is_hentai_site ? '/hentai/cerca' : '/cerca'; ?>">
						<div class="search-filter-title">Filtres de cerca</div>
						<form class="search-filter-form" onsubmit="return false;" novalidate>
							<label for="catalogue-search-query">Text a cercar</label>
							<input id="catalogue-search-query" type="text" oninput="loadSearchResults();" value="<?php echo !empty($_GET['query']) ? htmlspecialchars(urldecode($_GET['query'])) : ''; ?>" placeholder="Cerca...">
							<label for="catalogue-search-type">Tipus</label>
							<div id="catalogue-search-type" class="singlechoice-selector singlechoice-type">
								<div class="singlechoice-button singlechoice-selected" onclick="singlechoiceChange(this);" data-value="all"><i class="fa fa-fw fa-grip"></i>Tots</div>
								<div class="singlechoice-button" onclick="singlechoiceChange(this);" data-value="<?php echo $cat_config['filmsoneshots_db']; ?>"><i class="fa fa-fw <?php echo $cat_config['filmsoneshots_icon']; ?>"></i><?php echo $cat_config['filmsoneshots']; ?></div>
								<div class="singlechoice-button" onclick="singlechoiceChange(this);" data-value="<?php echo $cat_config['serialized_db']; ?>"><i class="fa fa-fw <?php echo $cat_config['serialized_icon']; ?>"></i><?php echo $cat_config['serialized']; ?></div>
							</div>
							<label>Estat</label>
<?php
$statuses=array(1,3,2,4,5);
foreach ($statuses as $status_id) {
?>
							<div class="search-checkboxes search-status status-<?php echo get_status($status_id); ?>">
								<input id="catalogue-search-status-<?php echo $status_id; ?>" data-id="<?php echo $status_id; ?>" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-status-<?php echo $status_id; ?>" class="for-checkbox"><span class="status-indicator"></span> <?php echo get_status_description_short($status_id); ?></label>
							</div>
<?php
}
?>
							<label for="catalogue-search-duration">Durada mitjana</label>
							<div id="catalogue-search-duration" class="double-slider-container">
								<input id="duration-from-slider" class="double-slider-from" type="range" value="<?php echo $cat_config['items_type']=='manga' ? '1' : '0'; ?>" min="<?php echo $cat_config['items_type']=='manga' ? '1' : '0'; ?>" max="<?php echo $cat_config['items_type']=='manga' ? '100' : '120'; ?>" onchange="loadSearchResults();">
								<input id="duration-to-slider" class="double-slider-to" type="range" value="<?php echo $cat_config['items_type']=='manga' ? '100' : '120'; ?>" min="<?php echo $cat_config['items_type']=='manga' ? '1' : '0'; ?>" max="<?php echo $cat_config['items_type']=='manga' ? '100' : '120'; ?>" onchange="loadSearchResults();">
								<div id="duration-from-input" value-formatting="<?php echo $cat_config['items_type']=='manga' ? 'pages' : 'time'; ?>" class="double-slider-input-from"><?php echo $cat_config['items_type']=='manga' ? '1 pàg.' : '0:00:00'; ?></div>
								<div id="duration-to-input" value-formatting="<?php echo $cat_config['items_type']=='manga' ? 'pages' : 'time'; ?>-max" class="double-slider-input-to"><?php echo $cat_config['items_type']=='manga' ? '100+ pàg.' : '2:00:00+'; ?></div>
							</div>
<?php
if (!$is_hentai_site) {
?>
							<label for="catalogue-search-rating">Valoració per edats</label>
							<div id="catalogue-search-rating" class="double-slider-container">
								<input id="rating-from-slider" class="double-slider-from" type="range" value="0" min="0" max="4" onchange="loadSearchResults();">
								<input id="rating-to-slider" class="double-slider-to" type="range" value="4" min="0" max="4" onchange="loadSearchResults();">
								<div id="rating-from-input" value-formatting="rating" class="double-slider-input-from">TP</div>
								<div id="rating-to-input" value-formatting="rating" class="double-slider-input-to">+18</div>
							</div>
<?php
}
?>
							<label for="catalogue-search-score">Puntuació a <?php echo $cat_config['items_type']=='liveaction' ? 'MyDramaList' : 'MyAnimeList'; ?></label>
							<div id="catalogue-search-score" class="double-slider-container">
								<input id="score-from-slider" class="double-slider-from" type="range" value="0" min="0" max="100" onchange="loadSearchResults();">
								<input id="score-to-slider" class="double-slider-to" type="range" value="100" min="0" max="100" onchange="loadSearchResults();">
								<div id="score-from-input" value-formatting="score" class="double-slider-input-from">-</div>
								<div id="score-to-input" value-formatting="score" class="double-slider-input-to">10,0</div>
							</div>
							<label for="catalogue-search-year">Any de primera <?php echo $cat_config['items_type']=='manga' ? 'publicació' : 'emissió'; ?></label>
							<div id="catalogue-search-year" class="double-slider-container">
								<input id="year-from-slider" class="double-slider-from" type="range" value="1950" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<input id="year-to-slider" class="double-slider-to" type="range" value="<?php echo date('Y'); ?>" min="1950" max="<?php echo date('Y'); ?>" onchange="loadSearchResults();">
								<div id="year-from-input" value-formatting="year" class="double-slider-input-from">-</div>
								<div id="year-to-input" value-formatting="year" class="double-slider-input-to"><?php echo date('Y'); ?></div>
							</div>
							<label>Inclou-hi també...</label>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-blacklisted" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-include-blacklisted" class="for-checkbox">Els meus fansubs exclosos</label>
							</div>
							<div class="search-checkboxes">
								<input id="catalogue-search-include-lost" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-include-lost" class="for-checkbox">Fitxes amb capítols perduts</label>
							</div>
<?php
if ($cat_config['items_type']!='liveaction' && !$is_hentai_site) {
?>
							<label>Demografies</label>
<?php
	$result=query("SELECT * FROM genre WHERE type='demographics' ORDER BY name ASC");
	while ($row=mysqli_fetch_assoc($result)) {
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-<?php echo $row['id']; ?>" data-id="<?php echo $row['id']; ?>" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-demographics-<?php echo $row['id']; ?>" class="for-checkbox"><?php echo $row['name']; ?></label>
							</div>
<?php
	}
	mysqli_free_result($result);
?>
							<div class="search-checkboxes search-demographics">
								<input id="catalogue-search-demographics-not-set" data-id="-1" type="checkbox" oninput="loadSearchResults();" checked>
								<label for="catalogue-search-demographics-not-set" class="for-checkbox">No definida</label>
							</div>
<?php
}
?>
							<label>Gèneres</label>
<?php
$result=query("SELECT g.* FROM genre g WHERE (g.type='genre' OR g.type='explicit') AND g.name<>'Hentai' AND EXISTS(SELECT s.id FROM rel_series_genre sg LEFT JOIN series s ON sg.series_id=s.id WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND sg.genre_id=g.id$hentai_subquery) ORDER BY g.name ASC");
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-neutral tristate-selected" onclick="tristateChange(this);"><i class="fa fa-fw fa-minus"></i></div>
								<div class="tristate-button tristate-exclude" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
							<label>Temàtiques</label>
<?php
$result=query("SELECT g.* FROM genre g WHERE g.type='theme' AND EXISTS(SELECT s.id FROM rel_series_genre sg LEFT JOIN series s ON sg.series_id=s.id WHERE (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0 AND sg.genre_id=g.id$hentai_subquery) ORDER BY g.name ASC");
while ($row=mysqli_fetch_assoc($result)) {
?>
							
							<div class="tristate-selector tristate-genres" data-id="<?php echo $row['id']; ?>">
								<div class="tristate-button tristate-include" onclick="tristateChange(this);"><i class="fa fa-fw fa-check"></i></div>
								<div class="tristate-button tristate-neutral tristate-selected" onclick="tristateChange(this);"><i class="fa fa-fw fa-minus"></i></div>
								<div class="tristate-button tristate-exclude" onclick="tristateChange(this);"><i class="fa fa-fw fa-xmark"></i></div>
								<div class="tristate-description"><?php echo htmlspecialchars($row['name']); ?></div>
							</div>
<?php
}
mysqli_free_result($result);
?>
						</form>
					</div>
					<div class="search-layout-toggle-button" onclick="toggleSearchLayout();"><i class="fa fa-fw fa-chevron-right"></i></div>
					<div class="results-layout catalogue-search<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
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

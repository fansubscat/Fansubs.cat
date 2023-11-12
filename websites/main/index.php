<?php
define('PAGE_STYLE_TYPE', 'main');
define('SITE_TITLE', 'Fansubs.cat: Anime, manga i imatge real en català');
require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="main-site-logo">
						<?php include(STATIC_DIRECTORY.'/images/site/logo.svg'); ?>
<?php
if (!empty($special_day) && file_exists(STATIC_DIRECTORY.'/images/site/logo_layer_'.$special_day.'.png')) {
?>
						<img class="logo-layer" src="<?php echo STATIC_URL; ?>/images/site/logo_layer_<?php echo $special_day; ?>.png">
<?php
}
?>
					</div>
					<div class="main-buttons">
						<a class="main-button" href="<?php echo ANIME_URL; ?>">Anime</a>
						<a class="main-button" href="<?php echo MANGA_URL; ?>">Manga</a>
						<a class="main-button" href="<?php echo LIVEACTION_URL; ?>">Imatge real</a>
					</div>
					<div class="secondary-buttons">
						<a class="secondary-button" href="<?php echo NEWS_URL; ?>">Notícies</a>
						<a class="secondary-button" href="/llista-de-fansubs">Fansubs</a>
					</div>
<?php
if (is_advent_days()) {
?>
					<div class="main-buttons">
						<a class="main-button advent-button" href="<?php echo ADVENT_URL; ?>">Calendari d’advent</a>
					</div>
<?php
}
?>
					<div class="tertiary-buttons">
						<a class="tertiary-button" href="/qui-som">Qui som?</a>
						<a class="tertiary-button" href="/enllacos">Enllaços</a>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

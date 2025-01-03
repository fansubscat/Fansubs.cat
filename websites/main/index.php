<?php
define('PAGE_STYLE_TYPE', 'main');

if (str_ends_with($_SERVER['HTTP_HOST'], 'hentai.cat')) {
	define('SITE_TITLE', 'Hentai.cat: Hentai en català');
} else {
	define('SITE_TITLE', 'Fansubs.cat: Anime, manga i imatge real en català');
}

require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

validate_hentai();

require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="main-site-logo">
						<?php include(STATIC_DIRECTORY.'/images/site/'.(SITE_IS_HENTAI ? 'logo_hentai.svg' : 'logo.svg')); ?>
<?php
if (!empty($special_day) && file_exists(STATIC_DIRECTORY.'/images/site/logo_'.(SITE_IS_HENTAI ? 'hentai_' : '').'layer_'.$special_day.'.png')) {
?>
						<img class="logo-layer" src="<?php echo STATIC_URL; ?>/images/site/logo_<?php echo SITE_IS_HENTAI ? 'hentai_' : ''; ?>layer_<?php echo $special_day; ?>.png">
<?php
}
?>
					</div>
					<div class="main-buttons">
						<a class="main-button" href="<?php echo ANIME_URL; ?>">Anime</a>
						<a class="main-button" href="<?php echo MANGA_URL; ?>">Manga</a>
<?php
if (!SITE_IS_HENTAI) {
?>
						<a class="main-button" href="<?php echo LIVEACTION_URL; ?>">Imatge real</a>
<?php
}
?>
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
<?php
if (!SITE_IS_HENTAI) {
?>
						<a class="tertiary-button" href="/qui-som">Qui som?</a>
						<a class="tertiary-button" href="/enllacos">Enllaços</a>
<?php
} else {
?>
						<a class="tertiary-button" href="<?php echo 'https://www.'.MAIN_DOMAIN; ?>">Fansubs.cat</a>
<?php
}
?>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

<?php
define('PAGE_STYLE_TYPE', 'main');
require_once(__DIR__.'/../../common/config.inc.php');

if (str_ends_with($_SERVER['HTTP_HOST'], HENTAI_DOMAIN)) {
	define('SITE_TITLE', 'Hentai.cat: Hentai en català');
} else {
	define('SITE_TITLE', 'Fansubs.cat: Anime, manga i imatge real en català');
}

require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
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
if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
?>
						<a class="main-button" href="<?php echo LIVEACTION_URL; ?>">Imatge real</a>
<?php
}
?>
					</div>
					<div class="secondary-buttons">
<?php
if (!DISABLE_NEWS) {
?>
						<a class="secondary-button" href="<?php echo NEWS_URL; ?>">Notícies</a>
<?php
}
?>
						<a class="secondary-button" href="/llista-de-fansubs">Fansubs</a>
					</div>
<?php
if (is_advent_days() && !DISABLE_ADVENT) {
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
<?php
	if (!DISABLE_LINKS) {
?>
						<a class="tertiary-button" href="/enllacos">Enllaços</a>
<?php
	}
} else {
?>
						<a class="tertiary-button" href="<?php echo 'https://www.'.MAIN_DOMAIN; ?>">Fansubs.cat</a>
<?php
}
?>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

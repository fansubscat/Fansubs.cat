<?php
define('PAGE_STYLE_TYPE', 'main');
define('SITE_TITLE_OVERRIDE', TRUE);

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
						<a class="main-button" href="<?php echo ANIME_URL; ?>"><?php echo lang('main.button.anime'); ?></a>
						<a class="main-button" href="<?php echo MANGA_URL; ?>"><?php echo lang('main.button.manga'); ?></a>
<?php
if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
?>
						<a class="main-button" href="<?php echo LIVEACTION_URL; ?>"><?php echo lang('main.button.liveaction'); ?></a>
<?php
}
?>
					</div>
					<div class="secondary-buttons">
<?php
if (!SITE_IS_HENTAI && !DISABLE_COMMUNITY) {
?>
						<a class="secondary-button" href="<?php echo COMMUNITY_URL; ?>"><?php echo lang('main.button.community'); ?></a>
<?php
}
?>
<?php
if (!DISABLE_NEWS) {
?>
						<a class="secondary-button" href="<?php echo NEWS_URL; ?>"><?php echo lang('main.button.news'); ?></a>
<?php
}
?>
						<a class="secondary-button" href="<?php echo lang('url.fansubs'); ?>"><?php echo lang('main.button.fansubs'); ?></a>
					</div>
<?php
if (!SITE_IS_HENTAI && is_advent_days() && !DISABLE_ADVENT) {
?>
					<div class="main-buttons">
						<a class="main-button advent-button" href="<?php echo ADVENT_URL; ?>"><?php echo lang('main.button.advent_calendar'); ?></a>
					</div>
<?php
}
?>
					<div class="tertiary-buttons">
<?php
if (!SITE_IS_HENTAI) {
?>
						<a class="tertiary-button" href="<?php echo lang('url.who'); ?>"><?php echo lang('main.button.who'); ?></a>
<?php
	if (!DISABLE_LINKS) {
?>
						<a class="tertiary-button" href="<?php echo lang('url.links'); ?>"><?php echo lang('main.button.links'); ?></a>
<?php
	}
} else {
?>
						<a class="tertiary-button" href="<?php echo 'https://www.'.MAIN_DOMAIN; ?>"><?php echo MAIN_SITE_NAME; ?></a>
<?php
}
?>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

<?php
require_once("config.inc.php");
$page_title="Fansubs.cat";
$social_title="Fansubs.cat";
$social_url=$main_url.'/';
$social_image_url=$main_url.'/images/social.jpg';
$social_description='Fansubs.cat és el portal dels fansubs en català. Hi trobaràs l’anime, el manga i tota la resta de contingut de tots els fansubs en català.';
$show_social=TRUE;
require_once("header.inc.php");
?>
				<?php include('images/logo.svg'); ?>
				<div class="main-buttons">
					<a class="main-button" href="<?php echo $anime_url; ?>">Anime</a>
					<a class="main-button" href="<?php echo $manga_url; ?>">Manga</a>
					<a class="main-button" href="<?php echo $liveaction_url; ?>">Acció real</a>
				</div>
				<div class="secondary-buttons">
					<a class="secondary-button" href="<?php echo $news_url; ?>">Notícies</a>
					<a class="secondary-button" href="/qui-som/">Qui som?</a>
				</div>
<?php
require_once("footer.inc.php");
?>

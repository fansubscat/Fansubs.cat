<?php
define('PAGE_STYLE_TYPE', 'main');
require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="main-site-logo">
						<?php include(STATIC_DIRECTORY.'/images/site/logo.svg'); ?>
					</div>
					<div class="main-buttons">
						<a class="main-button" href="<?php echo ANIME_URL; ?>">Anime</a>
						<a class="main-button" href="<?php echo MANGA_URL; ?>">Manga</a>
						<a class="main-button" href="<?php echo LIVEACTION_URL; ?>">Imatge real</a>
					</div>
					<div class="secondary-buttons">
						<a class="secondary-button" href="<?php echo NEWS_URL; ?>">Notícies</a>
						<a class="secondary-button" href="/qui-som">Qui som?</a>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

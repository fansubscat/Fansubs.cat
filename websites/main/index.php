<?php
$style_type='main';
require_once("../common.fansubs.cat/header.inc.php");
?>
				<?php include($static_directory.'/common/images/logo.svg'); ?>
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
require_once("../common.fansubs.cat/footer.inc.php");
?>

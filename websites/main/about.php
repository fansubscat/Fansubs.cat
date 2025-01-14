<?php
require_once(__DIR__.'/../common/initialization.inc.php');

define('PAGE_TITLE', lang('main.who.page_title'));
define('PAGE_PATH', lang('url.who'));
define('PAGE_STYLE_TYPE', 'text');
define('PAGE_DESCRIPTION', sprintf(lang('main.who.page_description'), MAIN_SITE_NAME));
define('PAGE_DISABLED_IF_HENTAI', TRUE);
require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-users"></i> <?php echo lang('main.who.header'); ?></h2>
						<?php echo sprintf(lang('main.who.explanation'), MAIN_SITE_NAME); ?>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

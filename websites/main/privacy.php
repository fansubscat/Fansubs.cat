<?php
require_once(__DIR__.'/../common/initialization.inc.php');

define('PAGE_TITLE', lang('main.privacy_policy.page_title'));
define('PAGE_PATH', lang('url.privacy_policy'));
define('PAGE_STYLE_TYPE', 'text');
define('PAGE_DESCRIPTION', sprintf(lang('main.privacy_policy.page_description'), CURRENT_SITE_NAME));

require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="text-page">
						<h2 class="section-title"><i class="fa fa-fw fa-user-lock"></i> <?php echo lang('main.privacy_policy.header'); ?></h2>
						<?php echo sprintf(lang('main.privacy_policy.explanation'), CURRENT_SITE_NAME); ?>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

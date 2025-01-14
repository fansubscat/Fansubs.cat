<?php
define('PAGE_STYLE_TYPE', 'news');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

validate_hentai();

require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="results-layout catalogue-index">
<?php
if (is_robot()){
?>
						<div class="section">
							<div class="site-message robo-message"><?php echo sprintf(SITE_IS_HENTAI ? lang('news.robot_message.hentai') : lang('news.robot_message'), CURRENT_SITE_NAME); ?></div>
						</div>
<?php
}
include(__DIR__.'/results.php'); 
?>					</div>
					<div class="loading-layout hidden">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message"><?php echo lang('news.search.loading_results'); ?></div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message"><?php echo lang('news.error_contacting_server'); ?></div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

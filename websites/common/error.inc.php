<?php
require_once(__DIR__.'/initialization.inc.php');
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'text');
}
define('PAGE_TITLE', lang('error.page_title'));
define('ERROR_PAGE', TRUE);
$code = !empty($_GET['code']) ? $_GET['code'] : 404;
http_response_code($code);
require_once(__DIR__.'/header.inc.php');
?>
				<div class="text-page centered error-page">
					<h2 class="section-title"><?php echo $code==403 ? lang('error.403.header') : ($code==451 ? lang('error.451.header') : lang('error.404.header')); ?></h2>
					<div class="section-content">
						<img class="error-image" src="https://i.imgur.com/RYbcQlZ.gif" alt="">
					</div>
					<div class="section-content new-paragraph">
<?php
if ($code==403){
?>
						<?php echo sprintf(lang('error.403.explanation'), SITE_BASE_URL); ?>
<?php
}  else {
?>
						<?php echo sprintf(lang('error.404.explanation'), SITE_BASE_URL); ?>
<?php
}
?>
					</div>
				</div>
<?php
require_once(__DIR__.'/footer.inc.php');
?>

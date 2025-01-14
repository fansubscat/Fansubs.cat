<?php
define('PAGE_STYLE_TYPE', 'catalogue');
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');

validate_hentai();

if (is_robot()) {
	define('PAGE_EXTRA_BODY_CLASS', 'has-carousel');
}

require_once(__DIR__.'/../common/header.inc.php');
?>
					<div class="results-layout catalogue-index<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	$result = query_total_number_of_series(CATALOGUE_ROUND_INTERVAL);
	$row = mysqli_fetch_assoc($result);
?>
						<div class="section featured-section">
							<div class="site-message robo-message"><?php printf(CATALOGUE_ROBOT_MESSAGE, $row['cnt']); ?></div>
						</div>
<?php
	mysqli_free_result($result);
	include(__DIR__.'/results.php');
} 
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message"><?php echo lang('catalogue.loading_results'); ?></div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message"><?php echo lang('catalogue.error_contacting_server'); ?></div>
					</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

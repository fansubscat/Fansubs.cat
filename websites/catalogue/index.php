<?php
define('PAGE_STYLE_TYPE', 'catalogue');
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");

validate_hentai();

if (is_robot()) {
	define('PAGE_EXTRA_BODY_CLASS', 'has-carousel');
}

require_once("../common.fansubs.cat/header.inc.php");
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
	include("results.php");
} 
?>					</div>
					<div class="loading-layout<?php echo !is_robot() ? '' : ' hidden'; ?>">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message">S’estan carregant les darreres novetats...</div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

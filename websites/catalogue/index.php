<?php
$style_type='catalogue';
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");
require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="search-layout hidden">
						Els filtres van aquí
					</div>
					<div class="results-layout catalogue-<?php echo !empty($_GET['search']) ? 'search' : 'index'; ?><?php echo is_robot() ? '' : ' hidden'; ?>">
						<input type="hidden" class="catalogue-search-query" value="<?php echo htmlentities(!empty($_GET['query']) ? $_GET['query'] : ''); ?>">
<?php
if (is_robot()){
	if ($cat_config['items_type']=='liveaction') {
		$number=25;
	} else {
		$number=50;
	}
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt FROM series s WHERE s.type='${cat_config['items_type']}' AND EXISTS (SELECT id FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
?>
						<div class="section">
							<div class="site-message absolutely-real"><?php printf($cat_config['site_robot_message'], $totalnumber); ?></div>
						</div>
<?php
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

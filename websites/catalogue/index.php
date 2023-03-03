<?php
$style_type='catalogue';
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("libraries/parsedown.inc.php");
require_once("common.inc.php");

$is_hentai_site=!empty($_GET['hentai']);

if ($is_hentai_site) {
	$page_title='Hentai';
	$hentai_subquery=" AND s.rating='XXX'";
} else {
	$hentai_subquery=" AND (s.rating IS NULL OR s.rating<>'XXX')";
}

if ($cat_config['items_type']=='liveaction' || (!empty($user) && $user['hide_hentai_access']==1)) {
	$hide_hentai=TRUE;
}

if ($is_hentai_site) {
	if (empty($user) && !is_robot()) {
		header("Location: $users_url/inicia-la-sessio");
		die();
	} else if (!is_robot() && !is_adult()) {
		$_GET['hentai']=0;
		$_GET['code']=403;
		http_response_code(403);
		include('error.php');
		die();
	}
}

if (is_robot()) {
	$extra_body_class='has-carousel';
}

require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="results-layout catalogue-index<?php echo is_robot() ? '' : ' hidden'; ?>">
<?php
if (is_robot()){
	if ($cat_config['items_type']=='liveaction' || $is_hentai_site) {
		$number=25;
	} else {
		$number=50;
	}
	$restotalnumber = query("SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt FROM series s WHERE s.type='${cat_config['items_type']}'$hentai_subquery AND EXISTS (SELECT id FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)");
	$totalnumber = mysqli_fetch_assoc($restotalnumber)['cnt'];
	mysqli_free_result($restotalnumber);
?>
						<div class="section">
							<div class="site-message absolutely-real"><?php printf($cat_config['site_robot_message'.($is_hentai_site ? '_hentai' : '')], $totalnumber); ?></div>
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

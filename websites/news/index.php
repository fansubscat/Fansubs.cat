<?php
define('PAGE_STYLE_TYPE', 'news');
require_once("../common.fansubs.cat/header.inc.php");
?>
					<div class="results-layout catalogue-index">
<?php
if (is_robot()){
?>
						<div class="section">
							<div class="site-message absolutely-real">Et donem la benvinguda a Fansubs.cat! Aquí trobaràs les darreres notícies de tots els fansubs en català! Les notícies s'obtenen automàticament dels diferents webs dels fansubs. Per a accedir a cada notícia, només cal que hi facis clic!</div>
						</div>
<?php
}
include("results.php"); 
?>					</div>
					<div class="loading-layout hidden">
						<div class="loading-spinner"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div>
						<div class="loading-message">S’estan carregant els resultats de la cerca...</div>
					</div>
					<div class="error-layout hidden">
						<div class="error-icon"><i class="fa-3x fas fa-circle-exclamation"></i></div>
						<div class="error-message">S’ha produït un error en contactar amb el servidor. Torna-ho a provar.</div>
					</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

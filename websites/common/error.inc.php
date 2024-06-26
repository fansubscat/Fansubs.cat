<?php
require_once(dirname(__FILE__)."/config.inc.php");
if (!defined('PAGE_STYLE_TYPE')) {
	define('PAGE_STYLE_TYPE', 'text');
}
define('PAGE_TITLE', 'Error');
define('ERROR_PAGE', TRUE);
$code = !empty($_GET['code']) ? $_GET['code'] : 404;
if (defined('COPYRIGHT_ISSUE')) {
	$code = 451;
}
http_response_code($code);
require_once(dirname(__FILE__)."/header.inc.php");
?>
				<div class="text-page centered error-page">
					<h2 class="section-title"><?php echo $code==403 ? "Ostres! Sembla que no tens permís..." : ($code==451 ? "Ostres! No es pot accedir a aquest contingut..." : "Ostres! Sembla que la pàgina no existeix..."); ?></h2>
					<div class="section-content">
						<img class="error-image" src="https://i.imgur.com/RYbcQlZ.gif" alt="">
					</div>
					<div class="section-content new-paragraph">
<?php
if ($code==403){
?>
						<strong>No tens permís per a accedir a aquesta adreça.</strong><br>Fes bondat i fes servir només la part pública de la web ;)<br><br>Et recomanem que tornis a la <a href="<?php echo SITE_BASE_URL; ?>">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
} else if ($code==451){
?>
						<strong>Aquest contingut s’ha retirat a causa d’una notificació d’infracció del copyright.</strong><br /><br />Et recomanem que tornis a la <a href="<?php echo SITE_BASE_URL; ?>">pàgina principal</a> i provis de trobar una altra cosa!
<?php
}  else {
?>
						<strong>És possible que hagis seguit un enllaç antic o que el contingut ja no estigui disponible.</strong><br>Qui sap, potser l’han llicenciat en català i tot! O potser no i només és un error...<br><br>Et recomanem que tornis a la <a href="<?php echo SITE_BASE_URL; ?>">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
}
?>
					</div>
				</div>
<?php
require_once("footer.inc.php");
?>

<?php
require_once("db.inc.php");

$header_page_title='Error';
$header_tab='error';

$code = !empty($_GET['code']) ? $_GET['code'] : 404;

http_response_code($code);

require_once('header.inc.php');
?>
				<div class="section" style="text-align: center;">
					<h2 class="section-title"><?php echo $code==403 ? "Meeec! Permís denegat!" : "Meeec! La pàgina no existeix!"; ?></h2>
					<div class="section-content">
<?php
if ($code==403){
?>
						<strong>No tens permís per a accedir a aquesta adreça.</strong><br /><br />Et recomanem que tornis a la <a href="/">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
} else {
?>
						<strong>És possible que hagis seguit un enllaç antic o que l'anime ja no estigui disponible.</strong><br /><br />Et recomanem que tornis a la <a href="/">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
}
?>
					</div>
				</div>
<?php
require_once('footer.inc.php');
?>

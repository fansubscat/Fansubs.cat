<?php
require_once("db.inc.php");

$header_page_title='Error';
$header_tab='error';

$code = !empty($_GET['code']) ? $_GET['code'] : 404;

http_response_code($code);

require_once('header.inc.php');
?>
				<div class="section" style="text-align: center;">
					<h2 class="section-title"><?php echo $code==403 ? "Ostres! Sembla que no tens permís..." : "Ostres! Sembla que la pàgina no existeix..."; ?></h2>
					<div class="section-content">
						<img src="https://i.imgur.com/RYbcQlZ.gif" alt="Quina llàstima..." style="max-width: 100%;" />
					</div>
					<div class="section-content new-paragraph">
<?php
if ($code==403){
?>
						<strong>No tens permís per a accedir a aquesta adreça.</strong><br />Fes bondat i fes servir només la part pública de la web ;)<br /><br />Et recomanem que tornis a la <a href="/">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
} else {
?>
						<strong>És possible que hagis seguit un enllaç antic o que el contingut ja no estigui disponible.</strong><br />Qui sap, potser l'han llicenciat en català i tot! O potser no i només és un error...<br /><br />Et recomanem que tornis a la <a href="<?php echo $config['base_url']; ?>/">pàgina principal</a> i provis de trobar el que cerques allà!
<?php
}
?>
					</div>
				</div>
<?php
require_once('footer.inc.php');
?>

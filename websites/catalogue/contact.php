<?php
require_once('config.inc.php');
if (!empty($_POST['address']) && !empty($_POST['message']) && !empty($_POST['magic']) && $_POST['magic']=='1714') {
	$address = $_POST['address'];
	$message = $_POST['message'];

	$body = "S'ha rebut un nou missatge a ${config['site_title']}.\n\n";
	$body .= "Adreça electrònica: $address\n";
	$body .= "Missatge:\n\n$message";
	mail('info@fansubs.cat','Fansubs.cat - Nou missatge', $body,'','-f info@fansubs.cat -F Fansubs.cat');
}
?>

<?php
if (!empty($_POST['address']) && !empty($_POST['message']) && !empty($_POST['magic']) && $_POST['magic']=='1714') {
	$address = $_POST['address'];
	$message = $_POST['message'];

	$body = "S'ha rebut un nou missatge a Fansubs.cat - Anime.\n\n";
	$body .= "Adreça electrònica: $address\n";
	$body .= "Missatge:\n\n$message";
	mail('info@fansubs.cat','Fansubs.cat - Anime - Nou missatge', $body,'','-f info@fansubs.cat -F Fansubs.cat');
}
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.$main_url);
	die();
}

$page_title="Inicia la sessió";
$social_url='/inicia-la-sessio/';
$style_type='login';
require_once("../common.fansubs.cat/header.inc.php");
require_once("../common.fansubs.cat/footer.inc.php");
?>

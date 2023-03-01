<?php
require_once("../common.fansubs.cat/user_init.inc.php");

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.$main_url);
	die();
}

if (!empty($_GET['register'])) {
	$default_login_section='register';
} else if (!empty($_GET['forgot_password'])) {
	$default_login_section='forgot_password';
} else {
	$default_login_section='login';
}

$page_title="Inicia la sessió";
$social_url='/inicia-la-sessio';
$style_type='login';
require_once("../common.fansubs.cat/header.inc.php");
require_once("../common.fansubs.cat/footer.inc.php");
?>

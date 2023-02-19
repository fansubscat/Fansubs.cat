<?php
require_once("../common.fansubs.cat/user_init.inc.php");

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.$main_url);
	die();
}

$page_title="Restableix la contrasenya";
$social_url='/restableix-la-contrasenya/';
$style_type='reset_password';
require_once("../common.fansubs.cat/header.inc.php");
require_once("../common.fansubs.cat/footer.inc.php");
?>

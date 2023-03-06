<?php
require_once("../common.fansubs.cat/user_init.inc.php");

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.MAIN_URL);
	die();
}

define('PAGE_TITLE', 'Restableix la contrasenya');
define('PAGE_PATH', '/restableix-la-contrasenya');
define('PAGE_STYLE_TYPE', 'reset_password');

require_once("../common.fansubs.cat/header.inc.php");
require_once("../common.fansubs.cat/footer.inc.php");
?>

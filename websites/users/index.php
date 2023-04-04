<?php
require_once("../common.fansubs.cat/user_init.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'El meu perfil');
define('PAGE_STYLE_TYPE', 'users');

require_once("../common.fansubs.cat/header.inc.php");
?>
Aquí anirà el perfil de l’usuari.
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'Perfil d’usuari: '.$user['username']);
define('PAGE_STYLE_TYPE', 'text');

require_once("../common.fansubs.cat/header.inc.php");
?>
Aquí anirà el perfil de l’usuari.
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

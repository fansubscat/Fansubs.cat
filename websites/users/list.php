<?php
require_once("../common.fansubs.cat/user_init.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'La meva llista');
define('PAGE_PATH', '/la-meva-llista');
define('PAGE_STYLE_TYPE', 'text');

require_once("../common.fansubs.cat/header.inc.php");
?>
Aquí anirà la llista de l’usuari.
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

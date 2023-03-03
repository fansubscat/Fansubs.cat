<?php
$style_type='text';
require_once("../common.fansubs.cat/header.inc.php");

if (empty($user)) {
	header("Location: $users_url/inicia-la-sessio");
	die();
}
?>
Aquí anirà la llista de l’usuari.
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

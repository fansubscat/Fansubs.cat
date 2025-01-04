<?php
require_once(__DIR__.'/../common/user_init.inc.php');
$_SESSION = array();
$params = session_get_cookie_params();
setcookie(session_name(), '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
session_destroy();

header('Location: '.MAIN_URL);
?>

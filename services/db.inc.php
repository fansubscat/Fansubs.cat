<?php
require_once('config.inc.php');

function crash($string){
	ob_end_clean();
	die($string);
}

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or die('Could not connect to database');

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_set_charset($db_connection, 'utf8mb4') or crash(mysqli_error($db_connection));
?>

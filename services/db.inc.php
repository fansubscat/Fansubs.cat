<?
require_once('config.inc.php');

function crash($string){
	ob_end_clean();
	die($string);
}

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or die('Could not connect to database');

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_query($db_connection, "SET NAMES 'utf8'") or crash(mysqli_error($db_connection));
mysqli_query($db_connection, "SET CHARACTER SET 'utf8'") or crash(mysqli_error($db_connection));
?>

<?
require_once('config.inc.php');

function crash($string){
	ob_end_clean();
	http_response_code(500);
	die($string);
}

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or crash('{"status": "ko", "error": "Internal error: Could not connect to database."}');

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_query($db_connection, "SET NAMES 'utf8'") or crash('{"status": "ko", "error": "Internal error: '.mysqli_error($db_connection).'"}');
mysqli_query($db_connection, "SET CHARACTER SET 'utf8'") or crash('{"status": "ko", "error": "Internal error: '.mysqli_error($db_connection).'"}');
?>

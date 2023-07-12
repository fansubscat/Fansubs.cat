<?php
require_once('config.inc.php');

function log_action($action, $text=NULL){
	global $db_connection;
	if (!empty($text)){
		$text = "'".mysqli_real_escape_string($db_connection, $text)."'";
	} else {
		$text = "NULL";
	}
	mysqli_query($db_connection, "INSERT INTO admin_log (action, text, author, date) VALUES ('".mysqli_real_escape_string($db_connection, $action)."',$text,'API', CURRENT_TIMESTAMP)");
}

function crash($string){
	ob_end_clean();
	http_response_code(500);
	$response = array(
		'status' => 'ko',
		'error' => array(
				'code' => 'SERVER_ERROR',
				'description' => $string
			)
	);
	die(json_encode($response));
}

mysqli_report(MYSQLI_REPORT_OFF);

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or crash('Internal error: Could not connect to database.');

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_set_charset($db_connection, 'utf8') or crash(mysqli_error($db_connection));
?>

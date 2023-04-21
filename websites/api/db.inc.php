<?php
require_once('config.inc.php');

function log_action($action, $text=NULL){
	global $db_connection;
	if (!empty($text)){
		$text = "'".escape($text)."'";
	} else {
		$text = "NULL";
	}
	mysqli_query($db_connection, "INSERT INTO admin_log (action, text, author, date) VALUES ('".escape($action)."',$text,'[API]', CURRENT_TIMESTAMP)");
}

function crash($string){
	http_response_code(500);
	ob_end_clean();
	log_action('crash', $string);
	$response = array(
		'status' => 'ko',
		'error' => array(
				'code' => 'SERVER_ERROR',
				'description' => $string
			)
	);
	die(json_encode($response));
}

function escape($string){
	global $db_connection;
	return mysqli_real_escape_string($db_connection, $string);
}

function query($query){
	global $db_connection;
	$result = mysqli_query($db_connection, $query) or crash(mysqli_error($db_connection)."\n"."Consulta original: $query");
	return $result;
}

function get_previous_query_num_affected_rows(){
	global $db_connection;
	return mysqli_affected_rows($db_connection);
}

$db_connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or crash('Could not connect to database.');
mysqli_set_charset($db_connection, DB_CHARSET) or crash(mysqli_error($db_connection));
?>

<?php
require_once(__DIR__.'/initialization.inc.php');

function log_action($action, $text=NULL){
	global $db_connection;
	if (!empty($text)){
		$text = "'".escape($text)."'";
	} else {
		$text = "NULL";
	}
	if (!empty($_SESSION['username'])){
		$username = "'".escape($_SESSION['username'])."'";
	} else {
		$username = "NULL";
	}
	mysqli_query($db_connection, "INSERT INTO admin_log (action, text, author, date) VALUES ('".escape($action)."', $text, $username, CURRENT_TIMESTAMP)");
}

function crash($string){
	http_response_code(500);
	ob_end_clean();
	define('ERROR_DESCRIPTION', $string);
	include(__DIR__.'/tiny_error.inc.php');
	log_action('crash', $string);
	die();
}

function escape($string){
	global $db_connection;
	return mysqli_real_escape_string($db_connection, $string);
}

function escape_for_like($string){
	global $db_connection;
	$string = str_replace('\\', '\\\\', $string);
	return mysqli_real_escape_string($db_connection, $string);
}

function query($query){
	global $db_connection;
	$start = microtime(true);
	$result = mysqli_query($db_connection, $query) or crash(mysqli_error($db_connection)."<br><br>".sprintf(lang('generic.db_query'), $query));
	$end = microtime(true);
	if (($end - $start)>2) {
		log_action('slow-query', "Slow query detected (time: " . ($end - $start) . "s): $query");
	}
	return $result;
}

function get_previous_query_num_affected_rows(){
	global $db_connection;
	return mysqli_affected_rows($db_connection);
}

mysqli_report(MYSQLI_REPORT_OFF);

//Connect to database and initialize it
$db_connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or crash(lang('generic.db_error'));
mysqli_set_charset($db_connection, DB_CHARSET) or crash(mysqli_error($db_connection));
?>

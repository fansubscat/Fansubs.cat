<?php
require_once('config.inc.php');

function log_action($action, $text=NULL){
	global $db_connection;
	if (!empty($text)){
		$text = "'".escape($text)."'";
	} else {
		$text = "NULL";
	}
	query("INSERT INTO admin_log (action, text, author, date) VALUES ('".escape($action)."',$text,'Intern [S]', CURRENT_TIMESTAMP)", TRUE);
}

function crash($string){
	log_action('service-crash', $string);
	die($string);
}

function escape($string){
	global $db_connection;
	return mysqli_real_escape_string($db_connection, $string);
}

function query($query, $ignore_crash=FALSE){
	global $db_connection;
	if ($ignore_crash){
		$result = mysqli_query($db_connection, $query);
	} else {
		$result = mysqli_query($db_connection, $query) or crash(mysqli_error($db_connection)."\n"."Original query: $query");
	}
	return $result;
}

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or crash('Unable to connect to database');

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_set_charset($db_connection, 'utf8mb4') or crash(mysqli_error($db_connection));
?>

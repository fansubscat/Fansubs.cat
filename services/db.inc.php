<?php
require_once('config.inc.php');

function log_action($action, $text=NULL){
	global $db_connection;
	if (!empty($text)){
		$text = "'".escape($text)."'";
	} else {
		$text = "NULL";
	}
	mysqli_query($db_connection, "INSERT INTO admin_log (action, text, author, date) VALUES ('".escape($action)."', $text, 'Intern [S]', CURRENT_TIMESTAMP)");
}

function crash($string){
	log_action('service-crash', $string);
	die($string);
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

$db_connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) or crash("No s’ha pogut connectar a la base de dades.");

mysqli_set_charset($db_connection, DB_CHARSET) or crash(mysqli_error($db_connection));
?>

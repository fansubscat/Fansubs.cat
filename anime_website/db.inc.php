<?php
require_once('config.inc.php');

function log_action($action, $entity=NULL, $text=NULL){
	global $db_connection;
	if (!empty($entity)){
		$entity = "'".escape($entity)."'";
	} else {
		$entity = "NULL";
	}
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
	query("INSERT INTO action_log (action, entity, text, author, date) VALUES ('".escape($action)."',$entity,$text,$username, CURRENT_TIMESTAMP)", TRUE);
}

function crash($string){
	ob_end_clean();
	log_action('crash', NULL, $string);
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

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or crash("No s'ha pogut connectar a la base de dades.");

unset($db_host, $db_name, $db_user, $db_passwd);

mysqli_set_charset($db_connection, 'utf8mb4') or crash(mysqli_error($db_connection));
?>

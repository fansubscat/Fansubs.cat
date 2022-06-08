<?php
require_once('config.inc.php');

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
	query("INSERT INTO admin_log (action, text, author, date) VALUES ('".escape($action)."', $text, $username, CURRENT_TIMESTAMP)", TRUE);
}

function crash($string){
	ob_end_clean();
	log_action('crash', $string);
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
		$result = mysqli_query($db_connection, $query) or crash(mysqli_error($db_connection)."\n"."Consulta original: $query");
	}
	return $result;
}

function query_single($query, $ignore_crash=FALSE){
	global $db_connection;
	if ($ignore_crash){
		$result = mysqli_query($db_connection, $query);
	} else {
		$result = mysqli_query($db_connection, $query) or crash(mysqli_error($db_connection)."\n"."Consulta original: $query");
	}
	$row = mysqli_fetch_array($result);
	if ($row!==FALSE) {
		return $row[0];
	}
	return NULL;
}

$db_connection = mysqli_connect($db_host,$db_user,$db_passwd, $db_name) or crash("No s'ha pogut connectar a la base de dades.");

$memcached = new Memcached();
$memcached->addServer($memcached_host, $memcached_port);

unset($db_host, $db_name, $db_user, $db_passwd, $memcached_host, $memcached_port);

mysqli_set_charset($db_connection, 'utf8mb4') or crash(mysqli_error($db_connection));
?>

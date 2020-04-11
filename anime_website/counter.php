<?php
ob_start();
require_once("db.inc.php");

$link_id = $_GET['link_id'];

if (is_numeric($link_id)){
	$link_id = escape($link_id);
	query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT counter+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1)");
}

ob_flush();
mysqli_close($db_connection);
?>

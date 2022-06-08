<?php
require_once("db.inc.php");
if (!empty($_POST['type']) && !empty($_POST['text']) && !empty($_POST['file_id'])) {
	$link_id = escape((int)$_POST['link_id']);
	$play_time = escape((int)$_POST['location']);
	$type = escape($_POST['type']);
	$text = escape($_POST['text']);
	$ip = escape($_SERVER['REMOTE_ADDR']);
	$ua = escape($_SERVER['HTTP_USER_AGENT']);
	query("INSERT INTO reported_error (file_id, location, type, text, ip, date, user_agent) VALUES ($file_id, $location, '$type', '$text', '$ip', CURRENT_TIMESTAMP, '$ua')");
}
?>

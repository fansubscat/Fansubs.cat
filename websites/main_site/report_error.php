<?php
require_once("db.inc.php");
if (!empty($_POST['type']) && !empty($_POST['text']) && !empty($_POST['link_id'])) {
	$link_id = escape((int)$_POST['link_id']);
	$play_time = escape((int)$_POST['play_time']);
	$type = escape($_POST['type']);
	$text = escape($_POST['text']);
	$ip = escape($_SERVER['REMOTE_ADDR']);
	$ua = escape($_SERVER['HTTP_USER_AGENT']);
	query("INSERT INTO reported_error (link_id, play_time, type, text, ip, date, user_agent) VALUES ($link_id, $play_time, '$type', '$text', '$ip', CURRENT_TIMESTAMP, '$ua')");
}
?>

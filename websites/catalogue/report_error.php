<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function report_error(){
	global $user;
	if (!empty($_POST['type']) && !empty($_POST['text']) && !empty($_POST['file_id']) && !empty($_POST['view_id'])) {
		if (!empty($user)) {
			query_insert_reported_error($_POST['view_id'], $_POST['file_id'], $user['id'], NULL, $_POST['position'], $_POST['type'], $_POST['text'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		} else {
			//Anon
			query_insert_reported_error($_POST['view_id'], $_POST['file_id'], NULL, session_id(), $_POST['position'], $_POST['type'], $_POST['text'], $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);
		}
	}

	return array('result' => 'ok');
}

echo json_encode(report_error());
?>

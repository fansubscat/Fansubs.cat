<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

function mark_as_seen(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['file_id']) || empty($_POST['action'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$file_ids = $_POST['file_id'];
	$action = $_POST['action'];
	foreach ($file_ids as $file_id) {
		query_insert_or_update_user_seen_for_file_id($user['id'], $file_id, $action=='add' ? TRUE : FALSE);
	}

	//We assume that file_ids will always be on the same version
	query_insert_or_update_user_version_followed_by_file_id($user['id'], $file_ids[0]);

	return array('result' => 'ok');
}

echo json_encode(mark_as_seen());
?>

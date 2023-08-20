<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function remove_from_continue_watching(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['file_id'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$file_id = $_POST['file_id'];

	//We assume that file_ids will always be on the same version
	query_delete_user_version_followed_by_file_id($user['id'], $file_id);

	return array('result' => 'ok');
}

echo json_encode(remove_from_continue_watching());
?>

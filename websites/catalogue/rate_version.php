<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function rate_version(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['version_id'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$version_id = $_POST['version_id'];
	$rating = !empty($_POST['rating']) ? ($_POST['rating']==1 ? 1 : -1) : 0;

	//We assume that file_ids will always be on the same version
	query_insert_or_update_user_version_rating_for_version_id($user['id'], $version_id, $rating);

	return array('result' => 'ok');
}

echo json_encode(rate_version());
?>

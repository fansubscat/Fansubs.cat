<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function get_user_data(){
	global $user;
	//Check if we have all the data
	if (empty($user)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	return array('result' => 'ok', 'user' => $user);
}

echo json_encode(get_user_data());
?>

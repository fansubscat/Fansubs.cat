<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function save_to_my_list(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['series_id']) || !is_numeric($_POST['series_id']) || empty($_POST['action'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$series_id = intval($_POST['series_id']);
	$action = $_POST['action'];

	//Check if series exists
	$result = query_series_by_id($series_id);
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}
	
	if ($action=='add') {
		query_insert_to_user_series_list($user['id'], $series_id);
	} else {
		query_delete_from_user_series_list($user['id'], $series_id);
	}

	return array('result' => 'ok');
}

echo json_encode(save_to_my_list());
?>

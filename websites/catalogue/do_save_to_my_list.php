<?php
require_once("../common.fansubs.cat/user_init.inc.php");

function save_to_my_list(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['series_slug']) || empty($_POST['action'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$series_slug = escape($_POST['series_slug']);
	$action = $_POST['action'];

	//Check if series exists
	$result = query("SELECT * FROM series WHERE slug='$series_slug'");
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}

	$row = mysqli_fetch_assoc($result);
	
	if ($action=='add') {
		query("REPLACE INTO user_series_list (user_id, series_id) VALUES (".$user['id'].", ".$row['id'].")");
	} else {
		query("DELETE FROM user_series_list WHERE user_id=".$user['id']." AND series_id=".$row['id']);
	}

	return array('result' => 'ok');
}

echo json_encode(save_to_my_list());
?>

<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function upload_avatar(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['site_theme'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$site_theme = ($_POST['site_theme']=='light' ? 1 : 0);

	//Set attribute
	query_update_user_site_theme_by_user_id($site_theme, $user['id']);

	return array('result' => 'ok');
}

echo json_encode(upload_avatar());
?>

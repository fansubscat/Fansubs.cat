<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

function sendDeleteProfileEmail($email, $username) {
	$message = sprintf(lang('email.delete_profile.body'), $username, CURRENT_SITE_NAME_ACCOUNT, MAIN_URL.lang('url.contact_us'), CURRENT_SITE_NAME);
	send_email($email, $username, sprintf(lang('email.delete_profile.subject'), CURRENT_SITE_NAME_ACCOUNT), $message);
}

function delete_profile(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['password'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Check if user exists
	$result = query_user_by_username($user['username']);
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 3);
	}
	$row = mysqli_fetch_assoc($result);
	if (!password_verify($_POST['password'], $row['password'])){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 2);
	}
	mysqli_free_result($result);

	//Destroy user
	query_delete_user_blacklist($user['id']);
	query_delete_user_file_seen_status($user['id']);
	query_delete_user_series_list($user['id']);
	query_delete_user_version_followed($user['id']);
	query_delete_user_version_rating($user['id']);
	query_update_view_sessions_for_user_removal($user['id']);
	query_update_comments_for_user_removal($user['id']);
	query_delete_user($user['id']);
	
	//Delete from community
	if (!DISABLE_COMMUNITY) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/delete_user');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 
			  json_encode(array(
			  	'user_id' => $user['forum_user_id'],
			  	)));
		curl_exec($curl);
		curl_close($curl);
	}

	sendDeleteProfileEmail($user['email'], $user['username']);

	//Destroy session completely, just like logging out
	$_SESSION = array();
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
	session_destroy();

	return array('result' => 'ok');
}

echo json_encode(delete_profile());
?>

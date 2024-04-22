<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function sendDeleteProfileEmail($email, $username) {
	$message = "Bon dia, $username,\n\nAquest correu confirma que hem eliminat totes les teves dades personals de ".CURRENT_SITE_NAME.".\n\nSi mai desitges tornar, estarem encantats de rebre’t.\n\nSi et cal contactar amb nosaltres per qualsevol altre motiu, ens pots escriure un missatge en aquest enllaç: ".MAIN_URL."/contacta-amb-nosaltres\n\n".CURRENT_SITE_NAME.".";
	mail($email,'Confirmació d’eliminació del compte de '.CURRENT_SITE_NAME, $message,'From: '.CURRENT_SITE_NAME.' <'.EMAIL_ACCOUNT.'>','-f '.EMAIL_ACCOUNT.' -F "'.CURRENT_SITE_NAME.'"');
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

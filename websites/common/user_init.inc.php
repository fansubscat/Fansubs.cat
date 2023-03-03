<?php
function is_adult(){
	global $user;
	return (!empty($user) && date_diff(date_create_from_format('Y-m-d', $user['birthdate']), date_create(date('Y-m-d')))->format('%Y')>=18);
}

function is_robot(){
	return !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']);
}

function get_cookie_blacklisted_fansub_ids() {
	$fansub_ids = array();
	if (!empty($_COOKIE['blacklisted_fansub_ids'])) {
		$exploded = explode(',',$_COOKIE['blacklisted_fansub_ids']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($fansub_ids, intval($id));
			}
		}
	}
	return $fansub_ids;
}

require_once(dirname(__FILE__)."/db.inc.php");
ob_start();
session_set_cookie_params($cookie_duration, '/', $cookie_domain, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username'])){
	$result = query("SELECT * FROM user WHERE username='".escape($_SESSION['username'])."'");
	if (mysqli_num_rows($result)==0){
		//User has been deleted, kill session
		$_SESSION = array();
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		session_destroy();
	} else {
		//Store user data in $user for later usage
		$user = mysqli_fetch_assoc($result);
		$user['password']=NULL;
		mysqli_free_result($result);
		$user['series_list_ids']=array();
		$result = query("SELECT series_id FROM user_series_list WHERE user_id='".$user['id']."'");
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($user['series_list_ids'], $row['series_id']);
		}
		mysqli_free_result($result);
	}
}
?>

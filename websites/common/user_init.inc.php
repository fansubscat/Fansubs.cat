<?php
require_once(dirname(__FILE__)."/db.inc.php");
require_once(dirname(__FILE__)."/queries.inc.php");

ob_start();
session_set_cookie_params(COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if (!empty($_SESSION['username'])){
	$result = query_user_by_username($_SESSION['username']);
	if (mysqli_num_rows($result)==0){
		//User has been deleted, kill session
		$_SESSION = array();
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 60*60*24, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
		session_destroy();
	} else {
		//Store user data in $user for later usage
		$user = mysqli_fetch_assoc($result);
		mysqli_free_result($result);
		$user['password']=NULL;
		$user['series_list_ids']=array();
		$result = query_user_list_series_ids_by_user_id($user['id']);
		while ($row = mysqli_fetch_assoc($result)) {
			array_push($user['series_list_ids'], $row['series_id']);
		}
		mysqli_free_result($result);
	}
}
?>

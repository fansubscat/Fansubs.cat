<?php
require_once("db.inc.php");
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
	}
}
?>

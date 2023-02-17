<?php
require_once("user_init.inc.php");

function login(){
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$username = escape($_POST['username']);

	//Check if user exists
	$result = query("SELECT * FROM user WHERE username='$username'");
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}
	$row = mysqli_fetch_assoc($result);
	if (!password_verify($_POST['password'], $row['password'])){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(login());
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");

function resetPassword(){
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['code'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$username = escape($_POST['username']);
	$password = escape(password_hash($_POST['password'], PASSWORD_BCRYPT));
	$code = $_POST['code'];

	//Check if user exists
	$result = query("SELECT * FROM user WHERE username='$username'");
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}
	$row = mysqli_fetch_assoc($result);
	if ($row['reset_password_code']!=$code){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 3);
	}

	query("UPDATE user SET password='$password',reset_password_code=NULL WHERE username='$username'");

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(resetPassword());
?>

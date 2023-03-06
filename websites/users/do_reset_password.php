<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function resetPassword(){
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['code'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$username = $_POST['username'];
	$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
	$code = $_POST['code'];

	//Check if user exists
	$result = query_user_by_username($username);
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}
	$row = mysqli_fetch_assoc($result);
	if ($row['reset_password_code']!=$code){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 3);
	}

	query_update_user_password_hash_and_disable_reset_password_by_username($password, $username)

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(resetPassword());
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function login(){
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Check if user exists
	$result = query_user_by_username($_POST['username']);
	if (mysqli_num_rows($result)==0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 2);
	}
	
	$row = mysqli_fetch_assoc($result);
	if (!password_verify($_POST['password'], $row['password'])){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 2);
	}

	if (SITE_IS_HENTAI && date_diff(date_create_from_format('Y-m-d H:i:s', $row['birthdate'].' 00:00:00'), date_create(date('Y-m-d').' 00:00:00'))->format('%Y')<18) {
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 3);
	}
	mysqli_free_result($result);

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(login());
?>

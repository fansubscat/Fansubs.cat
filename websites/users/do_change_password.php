<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function sendChangePasswordEmail($email, $username) {
	$message = "Bon dia, $username,\n\nAquest correu confirma que s’ha canviat la contrasenya del teu compte a Fansubs.cat.\n\nSi et cal contactar amb nosaltres per qualsevol altre motiu, ens pots escriure un missatge en aquest enllaç: ".MAIN_URL."/contacta-amb-nosaltres\n\nFansubs.cat.";
	mail($email,'S’ha canviat la contrasenya de Fansubs.cat', $message,'From: Fansubs.cat <'.EMAIL_ACCOUNT.'>','-f '.EMAIL_ACCOUNT.' -F "Fansubs.cat"');
}

function delete_profile(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['password']) || empty($_POST['old_password'])) {
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
	if (!password_verify($_POST['old_password'], $row['password'])){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 2);
	}
	mysqli_free_result($result);

	$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
	query_update_user_password_hash_and_disable_reset_password_by_username($password, $user['username']);

	sendChangePasswordEmail($user['email'], $user['username']);

	return array('result' => 'ok');
}

echo json_encode(delete_profile());
?>

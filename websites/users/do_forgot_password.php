<?php
require_once("user_init.inc.php");

function sendForgotPasswordEmail($email, $username, $code) {
	global $users_url;
	$message = "Bon dia, $username,\n\nReps aquest correu perquè has demanat restablir la contrasenya del teu usuari a Fansubs.cat.\n\nSi no has estat tu qui ho ha demanat, pots ignorar aquest correu.\n\nPer a restablir la contrasenya, visita el següent enllaç: $users_url/restableix-la-contrasenya?usuari=".urlencode($username)."&codi=$code\n\nFansubs.cat.";
	mail($email,'Restabliment de la contrasenya de Fansubs.cat', $message,'From: Fansubs.cat <info@fansubs.cat>','-f info@fansubs.cat -F "Fansubs.cat"');
}

function forgotPassword(){
	//Check if we have all the data
	if (empty($_POST['email_address'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$email = escape($_POST['email_address']);

	//Check if mail exists
	$result = query("SELECT * FROM user WHERE email='$email'");
	if (mysqli_num_rows($result)>0){
		$row = mysqli_fetch_assoc($result);
		$code = substr(md5(uniqid(mt_rand(), true)) , 0, 16);
		query("UPDATE user SET reset_password_code='".$code."' WHERE id=".$row['id']);
		sendForgotPasswordEmail($row['email'], $row['username'], $code);
	}

	return array('result' => 'ok');
}

echo json_encode(forgotPassword());
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function sendForgotPasswordEmail($email, $username, $code) {
	$message = "Bon dia, $username,\n\nReps aquest correu perquè has demanat restablir la contrasenya del teu usuari a Fansubs.cat.\n\nSi no has estat tu qui ho ha demanat, pots ignorar aquest correu.\n\nPer a restablir la contrasenya, visita el següent enllaç: ".USERS_URL."/restableix-la-contrasenya?usuari=".urlencode($username)."&codi=$code\n\nFansubs.cat.";
	mail($email,'Restabliment de la contrasenya de Fansubs.cat', $message,'From: Fansubs.cat <'.EMAIL_ACCOUNT.'>','-f '.EMAIL_ACCOUNT.' -F "Fansubs.cat"');
}

function forgotPassword(){
	//Check if we have all the data
	if (empty($_POST['email_address'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Check if mail exists
	$result = query_user_by_email($_POST['email_address']);
	if (mysqli_num_rows($result)>0){
		$row = mysqli_fetch_assoc($result)
		$code = substr(md5(uniqid(mt_rand(), true)) , 0, 16);
		query_update_user_reset_password_code_by_id($code, $row['id']);
		sendForgotPasswordEmail($row['email'], $row['username'], $code);
	}
	mysqli_free_result($result);

	return array('result' => 'ok');
}

echo json_encode(forgotPassword());
?>

<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

function sendForgotPasswordEmail($email, $username, $code) {
	$message = sprintf(lang('email.forgot_password.body'), $username, CURRENT_SITE_NAME_ACCOUNT, USERS_URL.lang('url.reset_password').'?user='.urlencode($username).'&code='.$code, CURRENT_SITE_NAME);
	send_email($email, $username, sprintf(lang('email.forgot_password.subject'), CURRENT_SITE_NAME_ACCOUNT), $message);
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
		$row = mysqli_fetch_assoc($result);
		$code = substr(md5(uniqid(mt_rand(), true)) , 0, 16);
		query_update_user_reset_password_code_by_user_id($code, $row['id']);
		sendForgotPasswordEmail($row['email'], $row['username'], $code);
	}
	mysqli_free_result($result);

	return array('result' => 'ok');
}

echo json_encode(forgotPassword());
?>

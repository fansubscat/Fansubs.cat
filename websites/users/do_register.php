<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function string_ends_with($haystack, $needle) {
	$length = strlen($needle);
	if(!$length) {
		return TRUE;
	}
	return substr($haystack, -$length) === $needle;
}

function sendRegistrationEmail($email, $username) {
	$message = sprintf(lang('email.register.body'), $username, CURRENT_SITE_NAME_ACCOUNT, MAIN_URL.lang('url.login'), MAIN_URL.lang('url.contact_us'), CURRENT_SITE_NAME);
	send_email($email, $username, sprintf(lang('email.register.subject'), CURRENT_SITE_NAME_ACCOUNT), $message);
}

function register_user(){
	global $db_connection;
	
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email_address']) || empty($_POST['birthday_day']) || empty($_POST['birthday_month']) || empty($_POST['birthday_year']) || !is_numeric($_POST['birthday_day']) || !is_numeric($_POST['birthday_month']) || !is_numeric($_POST['birthday_year'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables
	$username = $_POST['username'];
	$password = password_hash($_POST['password'], PASSWORD_BCRYPT);
	$email_address = $_POST['email_address'];
	$birth_day = $_POST['birthday_day'];
	$birth_month = $_POST['birthday_month'];
	$birth_year = $_POST['birthday_year'];

	//Check for valid date
	if (!checkdate($birth_month, $birth_day, $birth_year)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 4);
	}

	//Check for valid year
	if ($birth_year<1900) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 5);
	}
	if (time() < strtotime('-1 hour', date_timestamp_get(date_create_from_format('Y-m-d', $birth_year.'-'.$birth_month.'-'.$birth_day)))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 6);
	}

	//Check for younger than 13
	if (time() < strtotime('+13 years', date_timestamp_get(date_create_from_format('Y-m-d', $birth_year.'-'.$birth_month.'-'.$birth_day)))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 7);
	}

	//Check for younger than 18 (hentai only)
	if (SITE_IS_HENTAI && time() < strtotime('+18 years', date_timestamp_get(date_create_from_format('Y-m-d', $birth_year.'-'.$birth_month.'-'.$birth_day)))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 10);
	}

	//Check correctly formed e-mail address
	if (!preg_match("/.*@.*\\..*/",$email_address)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 8);
	}

	//Check that username is not an e-mail address
	if (preg_match("/.*@.*\\..*/",$username)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 11);
	}

	//Check that username has no unsupported emojis
	if (preg_match("/[\x{10000}-\x{10FFFF}]/u",$username)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 12);
	}

	//Check not from a blocked domain
	foreach (BLACKLISTED_EMAIL_DOMAINS as $domain) {
		if (string_ends_with($email_address, $domain)) {
			http_response_code(400);
			return array('result' => 'ko', 'code' => 9);
		}
	}

	//Check if user exists
	$result = query_user_by_username($username);
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 2);
	}

	//Check if email exists
	$result = query_user_by_email($email_address);
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 3);
	}

	//Insert user
	query_insert_registered_user($username, $password, $email_address, $birth_year."-".$birth_month."-".$birth_day);
	$user_id=mysqli_insert_id($db_connection);
	
	//Register in community too
	if (!DISABLE_COMMUNITY) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/create_user');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 
			  json_encode(array(
			  	'username' => $username,
			  	'email' => $email_address,
			  	'birthdate' => $birth_year."-".$birth_month."-".$birth_day,
			  	)));
		$output = curl_exec($curl);
		
		curl_close($curl);

		$result = json_decode($output);

		if (!empty($result) && $result->status=='ok') {
			query("UPDATE user SET forum_user_id=".$result->user_id." WHERE id=".$user_id);
		}
	}
	
	sendRegistrationEmail($email_address, $username);

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(register_user());
?>

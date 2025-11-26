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

function edit_profile(){
	global $user;
	//Check if we have all the data
	if (empty($user) || empty($_POST['username']) || empty($_POST['email_address']) || empty($_POST['birthday_day']) || empty($_POST['birthday_month']) || empty($_POST['birthday_year']) || empty($_POST['avatar']) || empty($_POST['pronoun']) || !is_numeric($_POST['birthday_day']) || !is_numeric($_POST['birthday_month']) || !is_numeric($_POST['birthday_year'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables
	$username = $_POST['username'];
	$email_address = $_POST['email_address'];
	$pronoun = $_POST['pronoun'];
	$birth_day = $_POST['birthday_day'];
	$birth_month = $_POST['birthday_month'];
	$birth_year = $_POST['birthday_year'];
	$avatar = $_POST['avatar'];

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

	//Check that username is not a fake fansub
	if (!str_ends_with($user['username'], lang('generic.user.fansub_username_suffix')) && str_ends_with($username, lang('generic.user.fansub_username_suffix'))) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 13);
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

	//Check if email exists
	$result = query_user_by_email_except_self($email_address, $user['id']);
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 3);
	}

	//Check if username exists
	$result = query_user_by_username_except_self($username, $user['id']);
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		mysqli_free_result($result);
		return array('result' => 'ko', 'code' => 10);
	}

	if (str_starts_with($avatar, 'http')) {
		query_update_user_profile($user['id'], $username, $email_address, $pronoun, $birth_year."-".$birth_month."-".$birth_day, NULL);
	} else {
		$avatar_filename = get_nanoid().'.png';
		$user['avatar_filename'] = $avatar_filename;
		file_put_contents(STATIC_DIRECTORY.'/images/avatars/'.$avatar_filename, file_get_contents($avatar));
		query_update_user_profile($user['id'], $username, $email_address, $pronoun, $birth_year."-".$birth_month."-".$birth_day, $avatar_filename);
	}
	
	//Update profile in community too
	if (!DISABLE_COMMUNITY) {
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/update_profile');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, 
			  json_encode(array(
			  	'username_old' => $user['username'],
			  	'username' => $username,
			  	'email' => $email_address,
			  	'pronoun' => $pronoun,
			  	'avatar_url' => get_user_avatar_url($user),
			  	'birthdate' => $birth_year."-".$birth_month."-".$birth_day,
			  	)));
		curl_exec($curl);
		curl_close($curl);
	}

	//Set the session username (in case it was changed), the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(edit_profile());
?>

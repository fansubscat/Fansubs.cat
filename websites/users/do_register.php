<?php
require_once("../common.fansubs.cat/user_init.inc.php");

function register_user(){
	//Check if we have all the data
	if (empty($_POST['username']) || empty($_POST['password']) || empty($_POST['email_address']) || empty($_POST['birthday_day']) || empty($_POST['birthday_month']) || empty($_POST['birthday_year']) && is_numeric($_POST['birthday_day']) && is_numeric($_POST['birthday_month']) && is_numeric($_POST['birthday_year'])) {
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

	//Check correctly formed e-mail address
	if (!preg_match("/.*@.*\\..*/",$email_address)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 8);
	}

	//Check if user exists
	$result = query("SELECT * FROM user WHERE username='$username'");
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}

	//Check if email exists
	$result = query("SELECT * FROM user WHERE email='$email_address'");
	if (mysqli_num_rows($result)>0){
		http_response_code(400);
		return array('result' => 'ko', 'code' => 3);
	}

	//Insert user
	query("INSERT INTO user (username, password, email, birthdate, created, created_by, updated, updated_by) VALUES ('".escape($username)."', '".escape($password)."', '".escape($email_address)."', '".escape($birth_year)."-".escape($birth_month)."-".escape($birth_day)."', CURRENT_TIMESTAMP, 'Themself', CURRENT_TIMESTAMP, 'Themself')");

	//Set the session username, the next request will fill in the $user variable automatically
	$_SESSION['username']=$_POST['username'];

	return array('result' => 'ok');
}

echo json_encode(register_user());
?>

<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

function sendContactEmail($sender_email, $username, $message) {
	$message = "$username ($sender_email) ha enviat el següent missatge mitjançant el formulari de contacte de ".CURRENT_SITE_NAME.":\n\n$message\n\n".CURRENT_SITE_NAME.".";
	send_email(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME, CURRENT_SITE_NAME.' - Nou missatge', $message);
}

function contactEmail(){
	//Check if we have all the data
	if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['message']) || empty($_POST['question'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables	
	$email = $_POST['email'];
	$name = $_POST['name'];
	$message = $_POST['message'];
	$question = $_POST['question'];

	//Check if question is valid
	$security_responses = array("català", "catala", "valencià", "valencia", "valensia", "valensià", "catalá", "valenciá");
	if (!in_array(strtolower($question), $security_responses)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}

	//Check correctly formed e-mail address
	if (!preg_match("/.*@.*\\..*/",$email)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 3);
	}

	sendContactEmail($email, $name, $message);

	return array('result' => 'ok');
}

echo json_encode(contactEmail());
?>

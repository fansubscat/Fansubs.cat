<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function leave_comment(){
	global $user,$db_connection;
	//Check if we have all the data
	if (empty($user)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}
	if (empty($_POST['version_id']) || empty($_POST['text'])) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 2);
	}

	$result = query_user_comments_in_the_last_minute($user['id']);

	if (mysqli_num_rows($result)>0) {
		http_response_code(403);
		return array('result' => 'ko', 'code' => 3);
	}

	//Transfer to variables
	$version_id = intval($_POST['version_id']);
	$text = $_POST['text'];
	$has_spoilers = $_POST['has_spoilers']=='true' ? 1 : 0;

	//Update DB
	query_insert_comment($user['id'], $version_id, $text, $has_spoilers);
	$comment_id=mysqli_insert_id($db_connection);
	$result = query_comment_episode_title($comment_id);
	$row = mysqli_fetch_assoc($result);

	return array('result' => 'ok',
		'text' => str_replace("\n", "<br>", htmlentities($text)),
		'username' => htmlentities($user['username']),
		'has_spoilers' => $has_spoilers==1,
		'episode_title' => $row['episode_title']
	);
}

echo json_encode(leave_comment());
?>

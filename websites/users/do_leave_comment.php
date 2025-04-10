<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/queries.inc.php');

function get_fansub_preposition_name($text){
	$first = mb_strtoupper(substr($text, 0, 1));
	if (($first == 'A' || $first == 'E' || $first == 'I' || $first == 'O' || $first == 'U') && substr($text, 0, 4)!='One '){ //Ugly...
		return "d’$text";
	}
	return "de $text";
}

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
	if (!empty($user['fansub_id'])) {
		query_insert_comment_fansub($user['fansub_id'], $version_id, $text, $has_spoilers);
	} else {
		query_insert_comment($user['id'], $version_id, $text, $has_spoilers);
	}
	$comment_id=mysqli_insert_id($db_connection);
	
	//Get version data to post
	//Post to the community
	if (!DISABLE_COMMUNITY) {
		$query = query_comment_for_forum_posting($comment_id);
		if ($comment = mysqli_fetch_assoc($query)) {
			if (!empty($comment['forum_topic_id']) && $comment['user_status']!=1) {
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, COMMUNITY_URL.'/api/add_reply');
				curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: ".INTERNAL_SERVICES_TOKEN));
				curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, 
					  json_encode(array(
					  	'username' => $comment['username'],
					  	'topic_id' => $comment['forum_topic_id'],
					  	'subject' => 'Re: '. $comment['version_title'].' (versió '.get_fansub_preposition_name($comment['version_fansub_names']).')',
					  	'message' => ($comment['has_spoilers'] ? '[spoiler]' : '').$comment['text'].($comment['has_spoilers'] ? '[/spoiler]' : ''),
					  	'timestamp' => date('U'),
					  	)));
					$output = curl_exec($curl);
					curl_close($curl);

					$result = json_decode($output);

					if (!empty($result) && $result->status=='ok') {
						query("UPDATE comment SET forum_post_id=".$result->post_id." WHERE id=".$comment_id);
					}
			}
		}
	}
	
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

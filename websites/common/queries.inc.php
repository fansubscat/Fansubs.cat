<?php
require_once(dirname(__FILE__)."/db.inc.php");

function query_user_by_username($username) {
	$username_escaped = escape($username);
	$final_query = "SELECT *
			FROM user
			WHERE username='$username_escaped'";
	return query($final_query);
}

function query_user_list_series_ids_by_user_id($user_id) {
	$user_id_escaped = escape($user_id);
	$final_query = "SELECT series_id
			FROM user_series_list
			WHERE user_id=$user_id_escaped";
	return query($final_query);
}
?>

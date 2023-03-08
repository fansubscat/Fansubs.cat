<?php
function is_adult(){
	global $user;
	return (!empty($user) && date_diff(date_create_from_format('Y-m-d', $user['birthdate']), date_create(date('Y-m-d')))->format('%Y')>=18);
}

function is_robot(){
	return !empty($_SERVER['HTTP_USER_AGENT']) && preg_match('/bot|crawl|slurp|spider|mediapartners/i', $_SERVER['HTTP_USER_AGENT']);
}

function get_cookie_blacklisted_fansub_ids() {
	$fansub_ids = array();
	if (!empty($_COOKIE['blacklisted_fansub_ids'])) {
		$exploded = explode(',',$_COOKIE['blacklisted_fansub_ids']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($fansub_ids, intval($id));
			}
		}
	}
	return $fansub_ids;
}

function get_cookie_viewed_files_ids() {
	$file_ids = array();
	if (!empty($_COOKIE['viewed_file_ids'])) {
		$exploded = explode(',',$_COOKIE['viewed_file_ids']);
		foreach ($exploded as $id) {
			if (intval($id)) {
				array_push($file_ids, intval($id));
			}
		}
	}
	return $file_ids;
}
?>

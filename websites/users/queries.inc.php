<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");
require_once("../common.fansubs.cat/queries.inc.php");

// SELECT

function query_user_by_email($email) {
	$email_escaped = escape($email);
	$final_query = "SELECT *
			FROM user
			WHERE email='$email_escaped'";
	return query($final_query);
}

function query_my_list_by_type($user, $type, $hentai) {
	$type = escape($type);
	$final_query = get_internal_catalogue_base_query_portion($user, FALSE)."
				AND s.type='$type'
				AND ".($hentai ? "s.rating='XXX'" : "s.rating<>'XXX'")."
				AND s.id IN (SELECT usl.series_id FROM user_series_list usl WHERE usl.user_id=${user['id']})
			GROUP BY s.id
			ORDER BY s.name ASC";
	return query($final_query);
}

function query_my_list_total_items($user) {
	$final_query = "SELECT COUNT(*) cnt FROM user_series_list usl WHERE usl.user_id=${user['id']}";
	return query($final_query);
}

// INSERT

function query_insert_registered_user($username, $password_hash, $email, $birthdate) {
	$username_escaped = escape($username);
	$password_hash_escaped = escape($password_hash);
	$email_escaped = escape($email);
	$birthdate_escaped = escape($birthdate);
	$final_query = "INSERT INTO user (username, password, email, birthdate, created, created_by, updated, updated_by)
			VALUES ('$username_escaped', '$password_hash_escaped', '$email_escaped', '$birthdate_escaped', CURRENT_TIMESTAMP, 'Themself', CURRENT_TIMESTAMP, 'Themself')";
	return query($final_query);
}

// UPDATE

function query_update_user_reset_password_code_by_user_id($code, $user_id) {
	$code_escaped = escape($code);
	$user_id = escape($user_id);
	$final_query = "UPDATE user
			SET reset_password_code='$code_escaped'
			WHERE id=$user_id";
	return query($final_query);
}

function query_update_user_password_hash_and_disable_reset_password_by_username($password_hash, $username) {
	$password_hash_escaped = escape($password_hash);
	$username_escaped = escape($username);
	$final_query = "UPDATE user
			SET password='$password_hash_escaped', reset_password_code=NULL
			WHERE username='$username_escaped'";
	return query($final_query);
}

function query_update_user_site_theme_by_user_id($site_theme, $user_id) {
	$site_theme_escaped = escape($site_theme);
	$user_id = escape($user_id);
	$final_query = "UPDATE user
			SET site_theme='$site_theme_escaped'
			WHERE id=$user_id";
	return query($final_query);
}
?>

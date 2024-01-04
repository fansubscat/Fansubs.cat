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

function query_user_by_email_except_self($email, $user_id) {
	$email_escaped = escape($email);
	$user_id = escape($user_id);
	$final_query = "SELECT *
			FROM user
			WHERE email='$email_escaped'
				AND id<>$user_id";
	return query($final_query);
}

function query_user_seen_data_by_user_id($user_id) {
	$user_id = escape($user_id);
	$final_query = "SELECT (SELECT IFNULL(SUM(progress),0)
				FROM view_session
				WHERE user_id=$user_id
					AND type='anime'
				) total_anime_seen,
				(SELECT IFNULL(SUM(progress),0)
				FROM view_session
				WHERE user_id=$user_id
					AND type='manga'
				) total_manga_seen,
				(SELECT IFNULL(SUM(progress),0)
				FROM view_session
				WHERE user_id=$user_id
					AND type='liveaction'
				) total_liveaction_seen";
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

function query_all_fansubs() {
	$final_query = "SELECT *
			FROM (SELECT f.*,
					(SELECT COUNT(DISTINCT v.series_id)
						FROM rel_version_fansub vf
						LEFT JOIN version v ON vf.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						WHERE vf.fansub_id=f.id
							AND v.is_hidden=0
					) total_series,
					(SELECT COUNT(*)
						FROM news n
						WHERE n.fansub_id=f.id
					) total_news
				FROM fansub f
			) sq
			WHERE total_series>0 OR total_news>0
			ORDER BY sq.name ASC";
	return query($final_query);
}

function query_user_comments_in_the_last_minute($user_id) {
	$user_id = escape($user_id);
	$final_query = "SELECT *
			FROM comment
			WHERE user_id=$user_id AND created>=DATE_SUB(CURRENT_TIMESTAMP, INTERVAL 60 SECOND)";
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

function query_insert_user_blacklist($user_id, $blacklisted_fansub_id) {
	$user_id = escape($user_id);
	$blacklisted_fansub_id = escape($blacklisted_fansub_id);
	$final_query = "INSERT INTO user_fansub_blacklist (user_id, fansub_id)
			VALUES ($user_id, $blacklisted_fansub_id)";
	return query($final_query);
}

function query_insert_comment($user_id, $version_id, $text) {
	$user_id = escape($user_id);
	$version_id = escape($version_id);
	$text = escape($text);
	$final_query = "INSERT INTO comment (user_id, version_id, text, created, updated)
			VALUES ($user_id, $version_id, '$text', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	return query($final_query);
}

// UPDATE

function query_update_user_reset_password_code_by_user_id($code, $user_id) {
	$code_escaped = escape($code);
	$user_id = escape($user_id);
	$final_query = "UPDATE user
			SET reset_password_code='$code_escaped',
				updated=CURRENT_TIMESTAMP,
				updated_by='Themself'
			WHERE id=$user_id";
	return query($final_query);
}

function query_update_user_password_hash_and_disable_reset_password_by_username($password_hash, $username) {
	$password_hash_escaped = escape($password_hash);
	$username_escaped = escape($username);
	$final_query = "UPDATE user
			SET password='$password_hash_escaped',
				reset_password_code=NULL,
				updated=CURRENT_TIMESTAMP,
				updated_by='Themself'
			WHERE username='$username_escaped'";
	return query($final_query);
}

function query_update_user_site_theme_by_user_id($site_theme, $user_id) {
	$site_theme_escaped = escape($site_theme);
	$user_id = escape($user_id);
	$final_query = "UPDATE user
			SET site_theme='$site_theme_escaped',
				updated=CURRENT_TIMESTAMP,
				updated_by='Themself'
			WHERE id=$user_id";
	return query($final_query);
}

function query_update_user_settings($user_id, $show_cancelled_projects, $show_lost_projects, $hide_hentai_access, $episode_sort_order, $manga_reader_type, $previous_chapters_read_behavior) {
	$user_id = escape($user_id);
	$show_cancelled_projects = escape($show_cancelled_projects);
	$show_lost_projects = escape($show_lost_projects);
	$hide_hentai_access = escape($hide_hentai_access);
	$episode_sort_order = escape($episode_sort_order);
	$manga_reader_type = escape($manga_reader_type);
	$previous_chapters_read_behavior = escape($previous_chapters_read_behavior);
	$final_query = "UPDATE user
			SET show_cancelled_projects=$show_cancelled_projects,
				show_lost_projects=$show_lost_projects,
				hide_hentai_access=$hide_hentai_access,
				episode_sort_order=$episode_sort_order,
				manga_reader_type=$manga_reader_type,
				previous_chapters_read_behavior=$previous_chapters_read_behavior,
				updated=CURRENT_TIMESTAMP,
				updated_by='Themself'
			WHERE id=$user_id";
	return query($final_query);
}

function query_update_view_sessions_for_user_removal($user_id) {
	$user_id = escape($user_id);
	$final_query = "UPDATE view_session
			SET user_id=NULL,
				anon_id='".escape(session_id())."'
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_update_comments_for_user_removal($user_id) {
	$user_id = escape($user_id);
	$final_query = "UPDATE comment
			SET user_id=NULL,
				text='',
				updated=CURRENT_TIMESTAMP
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_update_user_profile($user_id, $email_address, $birth_date, $avatar_filename) {
	$user_id = escape($user_id);
	$email_address = escape($email_address);
	$birth_date = escape($birth_date);
	$final_query = "UPDATE user
			SET email='$email_address',
				birthdate='$birth_date',
				avatar_filename=".(!empty($avatar_filename) ? "'".escape($avatar_filename)."'" : "avatar_filename").",
				updated=CURRENT_TIMESTAMP,
				updated_by='Themself'
			WHERE id=$user_id";
	return query($final_query);
}

// DELETE

function query_delete_user($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user
			WHERE id=$user_id";
	return query($final_query);
}

function query_delete_user_blacklist($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user_fansub_blacklist
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_delete_user_file_seen_status($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user_file_seen_status
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_delete_user_series_list($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user_series_list
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_delete_user_version_followed($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user_version_followed
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_delete_user_version_rating($user_id) {
	$user_id = escape($user_id);
	$final_query = "DELETE FROM user_version_rating
			WHERE user_id=$user_id";
	return query($final_query);
}
?>

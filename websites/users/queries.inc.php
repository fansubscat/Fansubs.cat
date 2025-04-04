<?php
require_once(__DIR__.'/../common/db.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/../common/queries.inc.php');

// SELECT

function query_user_by_email($email) {
	$email_escaped = escape($email);
	$final_query = "SELECT *
			FROM user
			WHERE email='$email_escaped'";
	return query($final_query);
}

function query_user_by_username_except_self($username, $user_id) {
	$username_escaped = escape($username);
	$user_id = escape($user_id);
	$final_query = "SELECT *
			FROM user
			WHERE username='$username_escaped'
				AND id<>$user_id";
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
	$final_query = "SELECT (SELECT IFNULL(SUM(f.length),0)
				FROM user_file_seen_status ufss
					LEFT JOIN file f ON ufss.file_id=f.id
					LEFT JOIN version v ON f.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
				WHERE ufss.user_id=$user_id
					AND ufss.is_seen=1
					AND s.type='anime'
					AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
				) total_anime_seen,
				(SELECT IFNULL(SUM(f.length),0)
				FROM user_file_seen_status ufss
					LEFT JOIN file f ON ufss.file_id=f.id
					LEFT JOIN version v ON f.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
				WHERE ufss.user_id=$user_id
					AND ufss.is_seen=1
					AND s.type='manga'
					AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
				) total_manga_seen,
				(SELECT IFNULL(SUM(f.length),0)
				FROM user_file_seen_status ufss
					LEFT JOIN file f ON ufss.file_id=f.id
					LEFT JOIN version v ON f.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
				WHERE ufss.user_id=$user_id
					AND ufss.is_seen=1
					AND s.type='liveaction'
					AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
				) total_liveaction_seen,
				(SELECT COUNT(*)
				FROM comment c
					LEFT JOIN version v ON c.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
				WHERE c.user_id=$user_id
					AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
				) total_comments_left,
				(SELECT COUNT(*)
				FROM user_version_rating vr
					LEFT JOIN version v ON vr.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
				WHERE vr.user_id=$user_id
					AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
				) total_ratings_left";
	return query($final_query);
}

function query_my_list_by_type($user, $type, $hentai) {
	$type = escape($type);
	$final_query = get_internal_catalogue_base_query_portion($user, FALSE)."
				AND s.type='$type'
				AND ".($hentai ? "s.rating='XXX'" : "s.rating<>'XXX'")."
				AND s.id IN (SELECT usl.series_id FROM user_series_list usl WHERE usl.user_id=${user['id']})
			GROUP BY s.id
			ORDER BY default_version_title ASC";
	return query($final_query);
}

function query_my_list_total_items($user) {
	$final_query = "SELECT COUNT(*) cnt
			FROM user_series_list usl
				LEFT JOIN series s ON usl.series_id=s.id
			WHERE usl.user_id=${user['id']}
				AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'";
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

function query_insert_comment($user_id, $version_id, $text, $has_spoilers) {
	$user_id = escape($user_id);
	$version_id = escape($version_id);
	$text = escape($text);
	$has_spoilers = escape($has_spoilers);
	$final_query = "INSERT INTO comment (user_id, version_id, type, fansub_id, reply_to_comment_id, last_replied, text, last_seen_episode_id, has_spoilers, created, updated)
			VALUES ($user_id, $version_id, 'user', NULL, NULL, CURRENT_TIMESTAMP, '$text', (SELECT last_seen_episode_id FROM user_version_followed WHERE user_id=$user_id AND version_id=$version_id AND last_seen_episode_id<>-1), $has_spoilers, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	return query($final_query);
}

function query_insert_comment_fansub($fansub_id, $version_id, $text, $has_spoilers) {
	$fansub_id = escape($fansub_id);
	$version_id = escape($version_id);
	$text = escape($text);
	$has_spoilers = escape($has_spoilers);
	$final_query = "INSERT INTO comment (user_id, version_id, type, fansub_id, reply_to_comment_id, last_replied, text, last_seen_episode_id, has_spoilers, created, updated)
			VALUES (NULL, $version_id, 'fansub', $fansub_id, NULL, CURRENT_TIMESTAMP, '$text', NULL, $has_spoilers, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	return query($final_query);
}

function query_comment_episode_title($comment_id) {
	$comment_id = intval($comment_id);
	$final_query = "SELECT IF(c.last_seen_episode_id IS NULL,
					NULL,
					IF((s.subtype='movie' OR s.subtype='oneshot') AND s.number_of_episodes=1,
						IF(s.type='manga','".lang('catalogue.query.read')."','".lang('catalogue.query.seen')."'),
						IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ".lang('catalogue.query.chapter')."', REPLACE(TRIM(e.number)+0, '.', ',')),
								CONCAT('".lang('catalogue.query.chapter')."', REPLACE(TRIM(e.number)+0, '.', ','))
							),
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description)),
								IFNULL(et.title, e.description)
							)
						)
					)
				) episode_title
			FROM comment c
			LEFT JOIN episode e ON c.last_seen_episode_id=e.id
			LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=c.version_id
			LEFT JOIN version v ON c.version_id=v.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN division d ON e.division_id=d.id
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			WHERE c.id=$comment_id";
	return query($final_query);
}

function query_comment_for_forum_posting($comment_id) {
	$comment_id = intval($comment_id);
	$final_query = "SELECT c.*,
				v.forum_topic_id,
				u.status user_status,
				u.username,
				UNIX_TIMESTAMP(c.created) comment_created_timestamp,
				v.title version_title,
				GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') version_fansub_names
			FROM comment c
				LEFT JOIN version v ON c.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN user u ON c.user_id=u.id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub fa ON vf.fansub_id=fa.id
			WHERE c.id=$comment_id
				AND s.rating<>'XXX'
			GROUP BY c.id";
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

function query_update_user_profile($user_id, $username, $email_address, $birth_date, $avatar_filename) {
	$user_id = escape($user_id);
	$username = escape($username);
	$email_address = escape($email_address);
	$birth_date = escape($birth_date);
	$final_query = "UPDATE user
			SET username='$username',
				email='$email_address',
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

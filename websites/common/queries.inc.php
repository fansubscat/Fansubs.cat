<?php
require_once(dirname(__FILE__)."/db.inc.php");

function query_user_by_username($username) {
	$username = escape($username);
	$final_query = "SELECT *
			FROM user
			WHERE username='$username'";
	return query($final_query);
}

function query_user_list_series_ids_by_user_id($user_id) {
	$user_id = intval($user_id);
	$final_query = "SELECT series_id
			FROM user_series_list
			WHERE user_id=$user_id";
	return query($final_query);
}

function query_user_blacklisted_fansub_ids_by_user_id($user_id) {
	$user_id = intval($user_id);
	$final_query = "SELECT fansub_id
			FROM user_fansub_blacklist
			WHERE user_id=$user_id";
	return query($final_query);
}

function get_internal_hentai_condition() {
	if (SITE_IS_HENTAI) {
		return "s.rating='XXX'";
	} else {
		return "s.rating<>'XXX'";
	}
}

function get_internal_viewed_files_condition($user) {
	if (!empty($user)) {
		$viewed_files_condition = "ls.id NOT IN (SELECT ufss.file_id
								FROM user_file_seen_status ufss
								WHERE ufss.user_id=".intval($user['id']).")";
	} else {
		$cookie_viewed_files_ids = get_cookie_viewed_files_ids(); //Already escaped
		if (count($cookie_viewed_files_ids)>0) {
			$viewed_files_condition.="ls.id NOT IN (".implode(',',$cookie_viewed_files_ids).")";
		} else {
			$viewed_files_condition='1';
		}
	}
	return $viewed_files_condition;
}

function get_internal_blacklisted_fansubs_condition($user) {
	if (!empty($user)) {
		$blacklisted_fansubs_condition = "v.id NOT IN (
							SELECT vf2.version_id
							FROM rel_version_fansub vf2
							WHERE vf2.fansub_id IN (
								SELECT ufbl.fansub_id
								FROM user_fansub_blacklist ufbl
								WHERE ufbl.user_id=".intval($user['id'])."
								)
							)";
	} else {
		$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids(); //Already escaped
		if (count($cookie_blacklisted_fansub_ids)>0) {
			$blacklisted_fansubs_condition = "v.id NOT IN (
								SELECT vf2.version_id
								FROM rel_version_fansub vf2
								WHERE vf2.fansub_id IN (".implode(',',$cookie_blacklisted_fansub_ids).")
							)";
		} else {
			$blacklisted_fansubs_condition = "1";
		}
	}
	return $blacklisted_fansubs_condition;
}

function get_internal_cancelled_projects_condition($user) {
	if (!empty($user)) {
		if (empty($user['show_cancelled_projects'])) {
			return "v.status<>5 AND v.status<>4";
		}
	} else {
		if (empty($_COOKIE['show_cancelled_projects']) && !is_robot()) {
			return "v.status<>5 AND v.status<>4";
		}
	}
	return "1";
}

function get_internal_lost_projects_condition($user) {
	if (!empty($user)) {
		if (empty($user['show_lost_projects'])) {
			return "v.is_missing_episodes=0";
		}
	} else {
		if (empty($_COOKIE['show_lost_projects']) || !is_robot()) {
			return "v.is_missing_episodes=0";
		}
	}
	return "1";
}

function get_internal_catalogue_base_query_portion($user, $apply_hentai_rule=TRUE) {
	return "SELECT s.*,
			(SELECT nv.id
				FROM version nv
				WHERE nv.files_updated=MAX(v.files_updated)
					AND nv.series_id=s.id
					AND nv.is_hidden=0
				LIMIT 1
			) version_id,
			GROUP_CONCAT(DISTINCT CONCAT(v.id, '___', v.status, '___', f.name, '___', f.type, '___', f.id)
				ORDER BY v.status,
					v.files_updated,
					v.id,
					f.name
				SEPARATOR '|'
			) fansub_info,
			GROUP_CONCAT(DISTINCT sg.genre_id) genres,
			GROUP_CONCAT(DISTINCT CONCAT(g.id,'|',g.type,'|',REPLACE(REPLACE(g.name, ' ', ' '), '-', '‑'))
				ORDER BY g.name
				SEPARATOR ' • '
			) genre_names,
			MIN(v.status) best_status,
			MAX(v.files_updated) last_updated,
			(SELECT COUNT(d.id)
				FROM division d
				WHERE d.series_id=s.id
					AND d.number_of_episodes>0
			) divisions,
			s.number_of_episodes,
			(SELECT MAX(ls.created)
				FROM file ls
					LEFT JOIN version vs ON ls.version_id=vs.id
				WHERE vs.series_id=s.id
					AND vs.is_hidden=0
					AND ".get_internal_viewed_files_condition($user)."
			) last_file_created
		FROM series s
			LEFT JOIN version v ON s.id=v.series_id
			LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
			LEFT JOIN fansub f ON vf.fansub_id=f.id
			LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
			LEFT JOIN genre g ON sg.genre_id = g.id
		WHERE (SELECT COUNT(*)
			FROM version v
			WHERE v.series_id=s.id
				AND v.is_hidden=0
			)>0
			AND v.is_hidden=0
			AND ".($apply_hentai_rule ? get_internal_hentai_condition() : '1');
}

function query_number_of_versions_by_series_id($series_id) {
	$series_id = intval($series_id);
	$final_query = "SELECT COUNT(*) cnt
			FROM version
			WHERE series_id=$series_id
				AND is_hidden=0";
	return query($final_query);
}

function query_series_by_id($series_id) {
	$series_id = intval($series_id);
	$final_query = "SELECT *
			FROM series
			WHERE id=$series_id";
	return query($final_query);
}

// INSERT

function query_insert_to_user_series_list($user_id, $series_id) {
	$user_id = intval($user_id);
	$series_id = intval($series_id);
	$final_query = "REPLACE INTO user_series_list (user_id, series_id)
			VALUES ($user_id, $series_id)";
	return query($final_query);
}

// DELETE

function query_delete_from_user_series_list($user_id, $series_id) {
	$user_id = intval($user_id);
	$series_id = intval($series_id);
	$final_query = "DELETE FROM user_series_list
			WHERE user_id=$user_id AND series_id=$series_id";
	return query($final_query);
}
?>

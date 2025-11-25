<?php
require_once(__DIR__.'/../common/db.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/../common/queries.inc.php');

// INTERNAL

function get_internal_home_base_query($user) {
	$base_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_blacklisted_fansubs_condition($user)."
				AND ".get_internal_cancelled_projects_condition($user)."
				AND ".get_internal_lost_projects_condition($user);
	return $base_query;
}

function get_internal_most_popular_series_from_date($since_date) {
	//Input is already escaped
	$final_query = "SELECT b.series_id,
				IFNULL(MAX(b.total_views), 0) max_views,
				IFNULL(SUM(b.total_length), 0) total_length
			FROM (
				SELECT a.series_id,
					SUM(a.views) total_views,
					SUM(a.length) total_length
				FROM (
					SELECT SUM(vi.views) views,
						SUM(vi.total_length) length,
						f.version_id,
						s.id series_id,
						f.episode_id
					FROM views vi
						LEFT JOIN file f ON vi.file_id=f.id
						LEFT JOIN episode e ON f.episode_id=e.id
						LEFT JOIN series s ON e.series_id=s.id
					WHERE (SELECT COUNT(*)
						FROM version v
						WHERE s.type='".CATALOGUE_ITEM_TYPE."'
							AND v.series_id=s.id
							AND v.is_hidden=0
					)>0
						AND f.episode_id IS NOT NULL
						AND vi.views>0
						AND vi.day>='$since_date'
					GROUP BY f.episode_id
					) a
				GROUP BY a.episode_id
				) b
			GROUP BY b.series_id
			ORDER BY max_views DESC,
				total_length DESC,
				b.series_id DESC";
	return query($final_query);
}

function get_internal_recommendations_by_user_id($user_id) {
	//Input is already escaped
	$final_query = "SELECT s.id
			FROM rel_series_genre sg
				LEFT JOIN series s ON sg.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND sg.series_id NOT IN (SELECT DISTINCT v.series_id
					    FROM user_file_seen_status ufss
					    LEFT JOIN file f ON ufss.file_id=f.id
					    LEFT JOIN version v ON f.version_id=v.id
						WHERE ufss.is_seen=1
						AND ufss.user_id=$user_id)
			GROUP BY series_id
			HAVING COUNT(CASE WHEN genre_id IN (SELECT g.id
					FROM series s
					LEFT JOIN rel_series_genre sg ON sg.series_id=s.id
					LEFT JOIN genre g ON sg.genre_id=g.id
					WHERE s.id IN (SELECT DISTINCT v.series_id
					    FROM user_file_seen_status ufss
					    LEFT JOIN file f ON ufss.file_id=f.id
					    LEFT JOIN version v ON f.version_id=v.id
						WHERE ufss.is_seen=1
						AND ufss.user_id=$user_id
						AND ufss.last_viewed>='".date("Y-m-d",strtotime("-3 months"))."')
					GROUP BY g.id) THEN 1 END)>=2
			ORDER BY 
				COUNT(CASE WHEN genre_id IN (SELECT g.id
					FROM series s
					LEFT JOIN rel_series_genre sg ON sg.series_id=s.id
					LEFT JOIN genre g ON sg.genre_id=g.id
					WHERE s.id IN (SELECT DISTINCT v.series_id
					    FROM user_file_seen_status ufss
					    LEFT JOIN file f ON ufss.file_id=f.id
					    LEFT JOIN version v ON f.version_id=v.id
						WHERE ufss.is_seen=1
						AND ufss.user_id=$user_id
						AND ufss.last_viewed>='".date("Y-m-d",strtotime("-3 months"))."')
					GROUP BY g.id) THEN 1 END) DESC,
				RAND()";
	return query($final_query);
}

function get_internal_demographics_condition($demographic_ids, $show_no_demographics) {
	//Input is already escaped
	$demographics_condition = defined('ROBOT_INCLUDED') ? "1" : "0";
	$no_demographics_condition = defined('ROBOT_INCLUDED') ? "1" : "0";
	if (count($demographic_ids)>0) {
		$demographics_condition = "s.id IN (SELECT sg.series_id
						FROM rel_series_genre sg
						WHERE sg.genre_id IN(".implode(',',$demographic_ids).")
						)";
	}
	if ($show_no_demographics) {
		$no_demographics_condition = "s.id IN (SELECT s.id
						FROM series s
						WHERE NOT EXISTS (SELECT sg.series_id
							FROM rel_series_genre sg
								LEFT JOIN genre g ON sg.genre_id=g.id
							WHERE g.type='demographics'
								AND sg.series_id=s.id
							)
						)";
	}
	return "(($demographics_condition) OR ($no_demographics_condition))";
}

function get_internal_origins_condition($origins) {
	//Input is already escaped
	if (count($origins)>0) {
		return "s.comic_type IN ('".implode("','",$origins)."')";
	} else {
		return "0";
	}
}

function get_internal_content_types_condition($content_types) {
	//Input is already escaped
	if (count($content_types)>0) {
		return "f.type IN ('".implode("','", $content_types)."')";
	} else if (!CATALOGUE_HAS_FANDUBS) {
		return "1";
	}
	return "0";
}

function get_internal_included_genres_condition($genres_id) {
	//Input is already escaped
	$included_genres_condition = "1";
	if (count($genres_id)>0) {
		foreach ($genres_id as $genre_id) {
			$included_genres_condition .= " AND s.id IN (SELECT sg.series_id
								FROM rel_series_genre sg
								WHERE sg.genre_id=".intval($genre_id)."
							)";
		}
	}
	return $included_genres_condition;
}

function get_internal_excluded_genres_condition($genres_id) {
	//Input is already escaped
	$excluded_genres_condition = "1";
	if (count($genres_id)>0) {
		foreach ($genres_id as $genre_id) {
			$excluded_genres_condition .= " AND s.id NOT IN (SELECT sg.series_id
								FROM rel_series_genre sg
								WHERE sg.genre_id=".intval($genre_id)."
							)";
		}
	}
	return $excluded_genres_condition;
}

function get_internal_statuses_condition($statuses) {
	//Input is already escaped
	if (count($statuses)>0) {
		return "v.status IN (".implode(',', $statuses).")";
	}
	return defined('ROBOT_INCLUDED') ? "1" : "0";
}

function get_internal_length_condition($type, $length_type, $min_length, $max_length) {
	//Input is already escaped
	if ($type=='manga') {
		if ($length_type=='pages') {
			return "s.id IN (SELECT DISTINCT s.id
					FROM series s
						LEFT JOIN version v ON s.id=v.series_id
						LEFT JOIN file f ON v.id=f.version_id
					WHERE s.type='$type'
						AND f.is_lost=0
					GROUP BY s.id HAVING AVG(f.length)>=".intval($min_length)." AND AVG(f.length)<=".intval($max_length==100 ? 1000000 : $max_length)."
				)";
		} else if ($min_length!=0 || $max_length!=120) {
			return "0";
		}
	} else { //Anime and live action
		if ($length_type=='time') {
			return "s.id IN (SELECT DISTINCT s.id
					FROM series s
						LEFT JOIN version v ON s.id=v.series_id
						LEFT JOIN file f ON v.id=f.version_id
					WHERE s.type='$type'
						AND f.is_lost=0
					GROUP BY s.id HAVING AVG(f.length)>=".(intval($min_length)*60)." AND AVG(f.length)<=".(intval($max_length==120 ? 1000000 : $max_length)*60)."
				)";
		} else if ($min_length!=1 || $max_length!=100) {
			return "0";
		}
	}
	return "1";
}

// INSERT

function query_insert_or_update_user_position_for_file_id($user_id, $file_id, $position) {
	$file_id = intval($file_id);
	$user_id = intval($user_id);
	$position = intval($position);
	$final_query = "INSERT INTO user_file_seen_status
				(user_id, file_id, is_seen, position, last_viewed)
			VALUES ($user_id, $file_id, FALSE, $position, CURRENT_TIMESTAMP)
			ON DUPLICATE KEY UPDATE position=IF(is_seen=1,0,$position),last_viewed=CURRENT_TIMESTAMP";
	return query($final_query);
}

function query_insert_or_update_user_seen_for_file_id($user_id, $file_id, $is_seen) {
	$user_id = intval($user_id);
	$file_id = intval($file_id);
	$is_seen = ($is_seen===TRUE ? 1 : 0);
	$last_viewed = ($is_seen==1 ? 'CURRENT_TIMESTAMP' : 'NULL');
	$final_query = "INSERT INTO user_file_seen_status
				(user_id, file_id, is_seen, position, last_viewed)
			VALUES ($user_id, $file_id, $is_seen, 0, $last_viewed)
			ON DUPLICATE KEY UPDATE is_seen=$is_seen,position=0,last_viewed=$last_viewed";
	return query($final_query);
}

function query_insert_or_update_user_version_rating_for_version_id($user_id, $version_id, $rating) {
	$version_id = intval($version_id);
	$user_id = intval($user_id);
	$rating = intval($rating);
	$final_query = "INSERT INTO user_version_rating
				(user_id, version_id, rating)
			VALUES ($user_id, $version_id, $rating)
			ON DUPLICATE KEY UPDATE rating=$rating";
	return query($final_query);
}

function query_insert_view_session($view_id, $file_id, $type, $user_id, $anon_id, $length, $source, $ip, $user_agent) {
	$view_id = escape($view_id);
	$file_id = intval($file_id);
	$type = escape($type);
	$user_id = $user_id!==NULL ? intval($user_id) : 'NULL';
	$anon_id = $anon_id!==NULL ? "'".escape($anon_id)."'" : 'NULL';
	$length = intval($length);
	$source = escape($source);
	$ip = escape($ip);
	$user_agent = escape($user_agent);
	$final_query = "INSERT INTO view_session
				(id, file_id, type, user_id, anon_id, progress, length, created, updated, view_counted, shared_play_session_id, is_casted, source, ip, user_agent)
			VALUES ('$view_id', $file_id, '$type', $user_id, $anon_id, 0, $length, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, NULL, 0, '$source', '$ip', '$user_agent')";
	return query($final_query);
}

function query_insert_shared_play_session($id, $file_id, $position, $length, $state) {
	$id = escape($id);
	$file_id = intval($file_id);
	$position = intval($position);
	$length = intval($length);
	$state = empty($state) ? 'NULL' : "'".escape($state)."'";
	$final_query = "INSERT INTO shared_play_session
				(id, file_id, position, length, state, created, updated)
			VALUES ('$id', $file_id, $position, $length, $state, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	return query($final_query);
}

function query_insert_reported_error($view_id, $file_id, $user_id, $anon_id, $position, $type, $text, $ip, $user_agent) {
	$view_id = escape($view_id);
	$file_id = intval($file_id);
	$user_id = $user_id!==NULL ? intval($user_id) : 'NULL';
	$anon_id = $anon_id!==NULL ? "'".escape($anon_id)."'" : 'NULL';
	$position = intval($position);
	$type = escape($type);
	$text = escape($text);
	$ip = escape($ip);
	$user_agent = escape($user_agent);
	$final_query = "INSERT INTO reported_error
				(view_id, file_id, user_id, anon_id, position, type, text, date, ip, user_agent)
			VALUES ('$view_id', $file_id, $user_id, $anon_id, $position, '$type', '$text', CURRENT_TIMESTAMP, '$ip', '$user_agent')";
	return query($final_query);
}

function query_update_shared_play_session($id, $position, $state) {
	$id = escape($id);
	$position = intval($position);
	$state = empty($state) ? 'NULL' : "'".escape($state)."'";
	$final_query = "UPDATE shared_play_session
			SET position=LEAST($position,length),
				updated=CURRENT_TIMESTAMP,
				state=$state
			WHERE id='$id'";
	return query($final_query);
}

function query_update_view_session_basic_attrs($view_id, $length, $source, $ip, $user_agent) {
	$view_id = escape($view_id);
	$length = intval($length);
	$source = escape($source);
	$ip = escape($ip);
	$user_agent = escape($user_agent);
	$final_query = "UPDATE view_session
			SET length=$length,
				source=IF(is_casted=1,source,'$source'),
				ip='$ip',
				user_agent='$user_agent',
				updated=CURRENT_TIMESTAMP
			WHERE id='$view_id'";
	return query($final_query);
}

function query_update_view_session_progress($view_id, $progress, $is_casted, $source, $ip, $user_agent) {
	$view_id = escape($view_id);
	$progress = intval($progress);
	$is_casted = intval($is_casted);
	$source = escape($source);
	$ip = escape($ip);
	$user_agent = escape($user_agent);
	$final_query = "UPDATE view_session
			SET progress=GREATEST(progress,$progress),
				updated=CURRENT_TIMESTAMP,
				is_casted=$is_casted,
				source='$source',
				ip='$ip',
				user_agent='$user_agent'
			WHERE id='$view_id'";
	return query($final_query);
}

function query_update_view_session_shared_play_session_id($view_id, $shared_play_session_id) {
	$view_id = escape($view_id);
	$shared_play_session_id = empty($shared_play_session_id) ? 'NULL' : "'".escape($shared_play_session_id)."'";
	$final_query = "UPDATE view_session
			SET shared_play_session_id=$shared_play_session_id,
				updated=CURRENT_TIMESTAMP
			WHERE id='$view_id'";
	return query($final_query);
}

function query_update_view_session_view_counted($view_id) {
	$view_id = escape($view_id);
	$final_query = "UPDATE view_session
			SET view_counted=CURRENT_TIMESTAMP
			WHERE id='$view_id'
				AND view_counted IS NULL";
	return query($final_query);
}

function query_insert_or_update_user_version_followed_by_file_id($user_id, $file_id) {
	$user_id = intval($user_id);
	$file_id = intval($file_id);
	$final_query = "INSERT INTO user_version_followed (user_id, version_id, last_seen_episode_id)
			VALUES ($user_id, (SELECT f2.version_id
						FROM file f2
						WHERE f2.id=$file_id), IFNULL((SELECT e.id last_episode_seen_id
									FROM user_file_seen_status ufss
 									LEFT JOIN file f ON ufss.file_id=f.id
									LEFT JOIN episode e ON f.episode_id=e.id
									LEFT JOIN division d ON e.division_id=d.id
									LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=f.version_id
									WHERE ufss.is_seen=1
										AND ufss.user_id=$user_id
										AND f.version_id IN (SELECT f2.version_id
												FROM file f2
												WHERE f2.id=$file_id)
									ORDER BY d.number DESC, 
										e.number IS NULL ASC,
										e.number DESC,
										IFNULL(et.title, e.description) DESC
									LIMIT 1),-1)
			)
			ON DUPLICATE KEY UPDATE last_seen_episode_id=IFNULL((SELECT e.id last_episode_seen_id
									FROM user_file_seen_status ufss
 									LEFT JOIN file f ON ufss.file_id=f.id
									LEFT JOIN episode e ON f.episode_id=e.id
									LEFT JOIN division d ON e.division_id=d.id
									LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=f.version_id
									WHERE ufss.is_seen=1
										AND ufss.user_id=$user_id
										AND f.version_id IN (SELECT f2.version_id
												FROM file f2
												WHERE f2.id=$file_id)
									ORDER BY d.number DESC, 
										e.number IS NULL ASC,
										e.number DESC,
										IFNULL(et.title, e.description) DESC
									LIMIT 1),-1)";
	return query($final_query);
}

function query_delete_user_version_followed_by_file_id($user_id, $file_id) {
	$user_id = intval($user_id);
	$file_id = intval($file_id);
	$final_query = "DELETE FROM user_version_followed
			WHERE user_id=$user_id
				AND version_id=(SELECT f.version_id
						FROM file f
						WHERE f.id=$file_id)";
	return query($final_query);
}

function query_delete_shared_play_session($id) {
	$id = escape($id);
	$final_query = "DELETE FROM shared_play_session
			WHERE id='$id'";
	return query($final_query);
}

function query_save_click($file_id, $type, $date) {
	$file_id = intval($file_id);
	$type = escape($type);
	$date = escape($date);
	$final_query = "REPLACE INTO views
			SELECT $file_id,
				'$date',
				'$type',
				IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
				IFNULL((SELECT views FROM views WHERE file_id=$file_id AND day='$date'),0),
				IFNULL((SELECT total_length FROM views WHERE file_id=$file_id AND day='$date'),0)";
	return query($final_query);
}

function query_save_view($file_id, $type, $date, $length) {
	$file_id = intval($file_id);
	$type = escape($type);
	$date = escape($date);
	$length = intval($length);
	$final_query = "REPLACE INTO views
			SELECT $file_id,
				'$date',
				'$type',
				IFNULL((SELECT clicks FROM views WHERE file_id=$file_id AND day='$date'),0),
				IFNULL((SELECT views+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
				IFNULL((SELECT total_length+$length FROM views WHERE file_id=$file_id AND day='$date'),$length)";
	return query($final_query);
}

// SELECT

function query_total_number_of_series($round_interval) {
	$round_interval = intval($round_interval);
	$final_query = "SELECT FLOOR((COUNT(*)-1)/$round_interval)*$round_interval cnt
			FROM series s
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND EXISTS(SELECT id
					FROM version v
					WHERE v.series_id=s.id
						AND v.is_hidden=0
					)";
	return query($final_query);
}

function query_random_series($user) {
	$final_query = get_internal_home_base_query($user)."
			GROUP BY s.id
			ORDER BY RAND()
			LIMIT 1";
	return query($final_query);
}

function query_manga_division_data_from_file_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id = intval($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				v.slug,
				IF(s.subtype='oneshot', NULL, d.number) division_number,
				f.version_id
			FROM file f
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='manga'
				AND f.id=$old_piwigo_id";
	return query($final_query);
}

function query_manga_division_data_from_division_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id = intval($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				dv.slug,
				IF(s.type='oneshot', NULL, d.number) division_number,
				(SELECT id FROM version v WHERE v.series_id=s.id LIMIT 1) version_id
			FROM division d
				LEFT JOIN series s ON d.series_id=s.id
				LEFT JOIN version dv ON dv.id=s.default_version_id
			WHERE s.type='manga'
				AND d.id=$old_piwigo_id";
	return query($final_query);
}

function query_manga_series_data_from_series_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id = intval($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				dv.slug
			FROM series s
				LEFT JOIN version dv ON dv.id=s.default_version_id
			WHERE s.type='manga'
				AND s.id=$old_piwigo_id";
	return query($final_query);
}

function query_series_data_from_slug_and_type($slug, $type) {
	$slug = escape($slug);
	$type = escape($type);
	$final_query = "SELECT dv.slug,
				s.rating
			FROM series s
				LEFT JOIN version dv ON dv.id=s.default_version_id
			WHERE s.type='$type'
				AND dv.slug LIKE '$slug/%'";
	return query($final_query);
}

function query_filter_demographics() {
	$final_query = "SELECT *
			FROM genre
			WHERE type='demographics'
			ORDER BY name ASC";
	return query($final_query);
}

function query_filter_genders() {
	$final_query = "SELECT g.*
			FROM genre g
			WHERE (g.type='genre' OR g.type='explicit')
				AND EXISTS(SELECT s.id
					FROM rel_series_genre sg
						LEFT JOIN series s ON sg.series_id=s.id
					WHERE (SELECT COUNT(*)
						FROM version v
						WHERE v.series_id=s.id
							AND v.is_hidden=0
						)>0
						AND sg.genre_id=g.id
						AND s.type='".CATALOGUE_ITEM_TYPE."'
						AND ".get_internal_hentai_condition()."
				)
			ORDER BY g.name ASC";
	return query($final_query);
}

function query_filter_themes() {
	$final_query = "SELECT g.*
			FROM genre g
			WHERE g.type='theme'
				AND EXISTS(SELECT s.id
					FROM rel_series_genre sg
						LEFT JOIN series s ON sg.series_id=s.id
					WHERE (SELECT COUNT(*)
						FROM version v
						WHERE v.series_id=s.id
							AND v.is_hidden=0
						)>0
						AND sg.genre_id=g.id
						AND s.type='".CATALOGUE_ITEM_TYPE."'
						AND ".get_internal_hentai_condition()."
				)
			ORDER BY g.name ASC";
	return query($final_query);
}

function query_version_ids_for_fools_day($max_items) {
	$max_items = intval($max_items);
	//Worst rated completed or semi completed animes
	$final_query = "SELECT v.id
			FROM version v
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND v.status IN (1,3)
				AND s.score IS NOT NULL
				AND v.is_missing_episodes=0
			ORDER BY s.score ASC
			LIMIT $max_items";
	return query($final_query);
}

function query_version_ids_for_sant_jordi($max_items) {
	$max_items = intval($max_items);
	//Best rated completed and featurable animes of genres Romance, Boys Love and Girls Love
	$final_query = "SELECT v.id
			FROM version v
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND v.status=1
				AND v.featurable_status>=1
				AND s.score IS NOT NULL
				AND v.is_missing_episodes=0
				AND s.id IN (
					SELECT rsg.series_id
					FROM rel_series_genre rsg
					WHERE rsg.genre_id IN (7, 23, 38)
				)
			ORDER BY s.score DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_version_ids_for_tots_sants($max_items) {
	$max_items = intval($max_items);
	//Best rated completed and featurable animes of genre Horror
	$final_query = "SELECT v.id
			FROM version v
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND v.status=1
				AND v.featurable_status>=1
				AND s.score IS NOT NULL
				AND v.is_missing_episodes=0
				AND s.id IN (
					SELECT rsg.series_id
					FROM rel_series_genre rsg
					WHERE rsg.genre_id IN (21)
				)
				ORDER BY s.score DESC
				LIMIT $max_items";
	return query($final_query);
}

function query_version_ids_for_nadal($max_items) {
	$max_items = intval($max_items);
	//Best current year animes completed
	$final_query = "SELECT v.id
			FROM version v
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND YEAR(v.created)=".date('Y')."
				AND ".get_internal_hentai_condition()."
				AND v.status=1
				AND v.featurable_status>=1
				AND s.score IS NOT NULL
				AND v.is_missing_episodes=0
				ORDER BY s.score DESC
				LIMIT $max_items";
	return query($final_query);
}

function query_current_advent_calendar() {
	$final_query = "SELECT *
			FROM advent_calendar ac
			WHERE ac.year=".date('Y');
	return query($final_query);
}

function query_home_recommended_items($user, $force_recommended_ids_list, $max_items) {
	$max_items = intval($max_items);
	if (count($force_recommended_ids_list)>0) {
		$recommendations_subquery = implode(',', $force_recommended_ids_list); //No need to escape, comes from DB
	} else {
		$recommendations_subquery = "SELECT version_id
						FROM recommendation r
							LEFT JOIN version v ON r.version_id=v.id
							LEFT JOIN series s ON v.series_id=s.id
						WHERE s.type='".CATALOGUE_ITEM_TYPE."'";
	}
	$final_query = get_internal_home_base_query($user)."
				AND v.id IN ($recommendations_subquery)
			GROUP BY v.id
			ORDER BY RAND()
			LIMIT $max_items";
	return query($final_query);
}

function query_home_continue_watching_by_user_id($user_id) {
	$user_id = intval($user_id);
	$final_query = "SELECT *,
				GROUP_CONCAT(DISTINCT CONCAT(t2.version_id, '___', t2.status, '___', t2.fansub_name, '___', t2.fansub_type, '___', t2.fansub_id)
					ORDER BY t2.fansub_name
					SEPARATOR '|'
				) fansub_info,
				(SELECT COUNT(*)
					FROM version v
					WHERE v.series_id=t2.series_id
						AND v.is_hidden=0
				) total_versions
			FROM (
				SELECT *
				FROM (SELECT f.id file_id,
						f.version_id,
						IF(s.type='manga' AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
							REPLACE(IFNULL(vd.title, d.name), '".lang('catalogue.query.volume_replace_from')."', '".lang('catalogue.query.volume_replace_to')."'),
							NULL
						) division_name,
						IF(s.subtype='movie' OR s.subtype='oneshot',
							IF(s.subtype='movie', '".lang('catalogue.query.movie')."', IF(s.comic_type='novel', '".lang('catalogue.query.light_novel')."', '".lang('catalogue.query.oneshot')."')),
							IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
								CONCAT('".lang('generic.query.episode_space.short')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
								CONCAT(IFNULL(et.title, e.description))
							)
						) episode_title,
						f.extra_name,
						v.series_id,
						v.status,
						IF(s.type<>'manga',
							IFNULL(vd.title, IFNULL(d.name,v.title)),
							v.title
						) series_name,
						v.slug series_slug,
						fa.name fansub_name,
						fa.type fansub_type,
						fa.id fansub_id,
						ufss.position/f.length progress_percent,
						ufss.last_viewed last_viewed,
						f.length length,
						1 origin
					FROM user_file_seen_status ufss
						LEFT JOIN file f ON ufss.file_id=f.id
						LEFT JOIN version v ON f.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id
						LEFT JOIN fansub fa ON vf.fansub_id=fa.id
						LEFT JOIN episode e ON f.episode_id=e.id
						LEFT JOIN division d ON e.division_id=d.id
						LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
						LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
					WHERE ufss.user_id=$user_id
						AND s.type='".CATALOGUE_ITEM_TYPE."'
						AND f.is_lost=0
						AND ufss.is_seen=0
						AND ufss.position>0
						AND v.id IN (SELECT version_id FROM user_version_followed uvf WHERE user_id=$user_id)
						AND ".get_internal_hentai_condition()."
					UNION
					SELECT f.id file_id,
						f.version_id,
						IF(s.type='manga' AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
							REPLACE(IFNULL(vd.title, d.name), '".lang('catalogue.query.volume_replace_from')."', '".lang('catalogue.query.volume_replace_to')."'),
							NULL
						) division_name,
						IF(s.subtype='movie' OR s.subtype='oneshot',
							IF(s.subtype='movie', '".lang('catalogue.query.movie')."', IF(s.comic_type='novel', '".lang('catalogue.query.light_novel')."', '".lang('catalogue.query.oneshot')."')),
							IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
								CONCAT('".lang('generic.query.episode_space.short')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
								CONCAT(IFNULL(et.title, e.description))
							)
						) episode_title,
						f.extra_name,
						v.series_id,
						v.status,
						IF(s.type<>'manga',
							IFNULL(vd.title, IFNULL(d.name,v.title)),
							v.title
						) series_name,
						v.slug series_slug,
						fa.name fansub_name,
						fa.type fansub_type,
						fa.id fansub_id,
						0 progress_percent,
						CURRENT_TIMESTAMP last_viewed,
						f.length length,
						0 origin
					FROM file f
						LEFT JOIN version v ON f.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id
						LEFT JOIN fansub fa ON vf.fansub_id=fa.id
						LEFT JOIN episode e ON f.episode_id=e.id
						LEFT JOIN division d ON e.division_id=d.id
						LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
						LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
					WHERE f.id IN (
							SELECT (SELECT f.id
								FROM file f
								LEFT JOIN episode e2 ON f.episode_id=e2.id
								LEFT JOIN division d2 ON e2.division_id=d2.id
								LEFT JOIN episode_title et2 ON et2.episode_id=e2.id AND et2.version_id=f.version_id
								WHERE f.version_id=v.id
									AND f.episode_id IS NOT NULL
									AND ((e2.number IS NULL AND e1.number IS NULL AND IFNULL(et2.title,e2.description)>IFNULL(et1.title,e1.description)) OR (e2.number IS NULL AND e1.number IS NOT NULL) OR (CONCAT(NATURAL_SORT_KEY(d2.number), ':', NATURAL_SORT_KEY(e2.number))>CONCAT(NATURAL_SORT_KEY(d1.number), ':', NATURAL_SORT_KEY(e1.number))))
									AND IF((s.subtype='movie' OR s.subtype='oneshot'), 1, (d2.is_real=1 AND d1.is_real=1))
									AND f.id NOT IN (
										SELECT ufss.file_id
										FROM user_file_seen_status ufss
										WHERE ufss.user_id=$user_id
										AND ufss.is_seen=1
									)
								ORDER BY d2.number IS NULL ASC,
									d2.number ASC,
									e2.number IS NULL ASC,
									e2.number ASC,
									IFNULL(et2.title, e2.description) ASC
								LIMIT 1) newer_episode_file_id
							FROM user_version_followed uvf
							LEFT JOIN version v ON v.id=uvf.version_id
							LEFT JOIN series s ON s.id=v.series_id
							LEFT JOIN episode e1 ON e1.id=uvf.last_seen_episode_id
							LEFT JOIN division d1 ON d1.id=e1.division_id
							LEFT JOIN episode_title et1 ON et1.episode_id=uvf.last_seen_episode_id AND et1.version_id=uvf.version_id
							WHERE uvf.user_id=$user_id
						)
						AND f.version_id NOT IN (
							SELECT f.version_id
							FROM user_file_seen_status ufss
							LEFT JOIN file f ON ufss.file_id=f.id
							WHERE ufss.user_id=$user_id
							AND f.is_lost=0
							AND ufss.is_seen=0
							AND ufss.position>0
						)
						AND s.type='".CATALOGUE_ITEM_TYPE."'
						AND f.is_lost=0
						AND ".get_internal_hentai_condition()."
				) t
				ORDER BY t.origin ASC,
					t.last_viewed DESC
			) t2
			WHERE (SELECT COUNT(*)
					FROM version v
					WHERE v.series_id=t2.series_id
						AND v.is_hidden=0
				)>0
			GROUP BY t2.version_id
			ORDER BY t2.origin ASC, t2.series_name ASC";
	return query($final_query);
}

function query_home_most_popular($user, $max_items) {
	$max_items = intval($max_items);
	$result = get_internal_most_popular_series_from_date(date("Y-m-d",strtotime("-2 weeks")));
	$sort_by_popularity_in_clause = '0';
	while ($row = mysqli_fetch_assoc($result)){
		$sort_by_popularity_in_clause .= ',' . $row['series_id'];
	}
	mysqli_free_result($result);
	$final_query = get_internal_home_base_query($user)."
				AND s.id IN ($sort_by_popularity_in_clause)
			GROUP BY s.id
			ORDER BY FIELD(s.id, $sort_by_popularity_in_clause)
			LIMIT $max_items";
	return query($final_query);
}

function query_home_last_updated($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = "SELECT sq.*,
				COUNT(*) cnt
			FROM (
				SELECT *,
					GROUP_CONCAT(DISTINCT CONCAT(t.version_id, '___', t.status, '___', t.fansub_name, '___', t.fansub_type, '___', t.fansub_id)
						ORDER BY t.fansub_name
						SEPARATOR '|'
					) fansub_info,
				(SELECT COUNT(*)
					FROM version v
					WHERE v.series_id=t.series_id
						AND v.is_hidden=0
				) total_versions,
				UNIX_TIMESTAMP(t.created) file_created
				FROM (SELECT f.id file_id,
						f.created,
						f.version_id,
						IF(s.type='manga' AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
							REPLACE(IFNULL(vd.title, d.name), '".lang('catalogue.query.volume_replace_from')."', '".lang('catalogue.query.volume_replace_to')."'),
							NULL
						) division_name,
						IF(s.subtype='movie' OR s.subtype='oneshot',
							IF(s.subtype='movie', '".lang('catalogue.query.movie')."', IF(s.comic_type='novel', '".lang('catalogue.query.light_novel')."', '".lang('catalogue.query.oneshot')."')),
							IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
								CONCAT('".lang('generic.query.episode_space.short')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
								CONCAT(IFNULL(et.title, e.description))
							)
						) episode_title,
						f.extra_name,
						v.series_id,
						v.status,
						IF(s.type<>'manga',
							IFNULL(vd.title, IFNULL(d.name,v.title)),
							v.title
						) series_name,
						v.slug series_slug,
						fa.name fansub_name,
						fa.type fansub_type,
						fa.id fansub_id,
						f.length length
					FROM file f
						LEFT JOIN version v ON f.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id
						LEFT JOIN fansub fa ON vf.fansub_id=fa.id
						LEFT JOIN episode e ON f.episode_id=e.id
						LEFT JOIN division d ON e.division_id=d.id
						LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
						LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
					WHERE s.type='".CATALOGUE_ITEM_TYPE."'
						AND f.is_lost=0
						AND ".get_internal_hentai_condition()."
						AND ".get_internal_blacklisted_fansubs_condition($user)."
						AND ".get_internal_cancelled_projects_condition($user)."
						AND ".get_internal_lost_projects_condition($user)."
				) t
				GROUP BY t.file_id
			) sq
			GROUP BY sq.version_id, (sq.file_created DIV 1800)
			ORDER BY sq.file_created DESC,
				sq.file_id DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_home_last_finished_by_type($user, $max_items, $type) {
	$max_items = intval($max_items);
	$type = escape($type);
	$final_query = get_internal_home_base_query($user)."
				AND v.completed_date IS NOT NULL
				AND s.subtype='$type'
			GROUP BY v.id
			ORDER BY v.completed_date DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_home_featured_singles($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
				AND v.status IN (1, 3)
				AND s.id NOT IN (SELECT v.series_id
						FROM recommendation r
						LEFT JOIN version v ON r.version_id=v.id
					)
			GROUP BY s.id
			ORDER BY RAND()
			LIMIT $max_items";
	return query($final_query);
}

function query_home_user_recommendations_by_user_id($user, $max_items) {
	$max_items = intval($max_items);
	$user_id = intval($user['id']);
	$result = get_internal_recommendations_by_user_id($user_id);
	$sort_by_recommendations_in_clause = '0';
	while ($row = mysqli_fetch_assoc($result)){
		$sort_by_recommendations_in_clause .= ',' . $row['id'];
	}
	mysqli_free_result($result);
	$final_query = get_internal_home_base_query($user)."
				AND s.id IN ($sort_by_recommendations_in_clause)
			GROUP BY s.id
			ORDER BY FIELD(s.id, $sort_by_recommendations_in_clause)
			LIMIT $max_items";
	return query($final_query);
}

function query_home_random($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
				AND v.completed_date IS NOT NULL
			GROUP BY s.id
			ORDER BY RAND()
			LIMIT $max_items";
	return query($final_query);
}

function query_home_more_recent($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
			GROUP BY s.id
			ORDER BY s.publish_date DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_home_best_rated($user, $type, $max_items) {
	//type is already escaped, comes from code
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
				AND s.subtype='".$type."'
				AND s.score>6.5
			GROUP BY s.id
			ORDER BY RAND()
			LIMIT $max_items";
	return query($final_query);
}

function query_home_comments($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = "SELECT c.*,
				u.username,
				u.avatar_filename,
				f.id fansub_id,
				f.name fansub_name,
				UNIX_TIMESTAMP(c.created) created_timestamp,
				IF(c.last_seen_episode_id IS NULL,
					NULL,
					IF((s.subtype='movie' OR s.subtype='oneshot') AND s.number_of_episodes=1,
						IF(s.type='manga','".lang('catalogue.query.read')."','".lang('catalogue.query.seen')."'),
						IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."')),
								CONCAT('".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'))
							),
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description)),
								IFNULL(et.title, e.description)
							)
						)
					)
				) episode_title,
				IF(".(!empty($user) ? "EXISTS(SELECT * FROM user_file_seen_status ufss WHERE ufss.user_id=${user['id']} AND ufss.file_id IN (SELECT id FROM file f WHERE f.episode_id=c.last_seen_episode_id AND f.version_id=c.version_id) AND ufss.is_seen=1)" : '0').",
					1,
					0
				) is_seen_by_user,
				s.id series_id,
				v.title version_title,
				v.slug version_slug,
				(SELECT COUNT(*) FROM version v2 WHERE v2.series_id=s.id AND v2.is_hidden=0) total_versions,
				(SELECT GROUP_CONCAT(DISTINCT sf.name SEPARATOR ' + ')
					FROM rel_version_fansub svf
					LEFT JOIN fansub sf ON sf.id=svf.fansub_id
					WHERE svf.version_id=c.version_id
				) fansubs,
				ur.username replied_username
			FROM comment c
			LEFT JOIN user u ON c.user_id=u.id
			LEFT JOIN fansub f ON c.fansub_id=f.id
			LEFT JOIN episode e ON c.last_seen_episode_id=e.id
			LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=c.version_id
			LEFT JOIN version v ON c.version_id=v.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN division d ON e.division_id=d.id
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			LEFT JOIN comment cr ON c.reply_to_comment_id=cr.id
			LEFT JOIN user ur ON cr.user_id=ur.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND ".get_internal_blacklisted_fansubs_condition($user)."
				AND ".get_internal_cancelled_projects_condition($user)."
				AND ".get_internal_lost_projects_condition($user)."
				AND (u.status<>1".(!empty($user) ? " OR u.id=${user['id']}" : '').")
			ORDER BY c.created DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_series_by_slug($slug, $include_hidden) {
	$slug = escape($slug);
	$final_query = "SELECT s.*,
				v.id version_id,
				v.slug version_slug,
				v.title version_title,
				v.alternate_titles version_alternate_titles,
				v.synopsis version_synopsis,
				YEAR(s.publish_date) year,
				GROUP_CONCAT(DISTINCT CONCAT(g.id,'|',g.type,'|',g.name) ORDER BY g.name SEPARATOR ' • ') genres,
				(SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0 AND d.is_real=1) divisions
			FROM series s
				LEFT JOIN version v ON s.id=v.series_id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND v.slug='$slug'
				AND ".get_internal_hentai_condition()."
				AND ".($include_hidden ? '1' : '(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id)>0')."
			GROUP BY s.id";
	return query($final_query);
}

function query_series_by_series_only_slug($series_only_slug) {
	$series_only_slug = escape($series_only_slug);
	$final_query = "SELECT v.slug, s.type, s.rating
			FROM series s
				LEFT JOIN version v ON s.id=v.series_id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND v.slug LIKE '$series_only_slug/%'
				ORDER BY v.id=s.default_version_id DESC";
	return query($final_query);
}

function query_series_by_old_slug($old_slug) {
	$old_slug = escape($old_slug);
	$final_query = "SELECT v.slug, s.type, s.rating
			FROM old_slugs os
				LEFT JOIN version v ON os.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND os.old_slug='$old_slug'";
	return query($final_query);
}

function query_series_by_file_id($file_id) {
	$file_id = escape($file_id);
	$final_query = "SELECT s.*,
				v.title version_title,
				v.slug version_slug,
				v.synopsis version_synopsis,
				v.id version_id
			FROM file f
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND f.id=$file_id";
	return query($final_query);
}

function query_series_data_for_series_page($user, $series_id) {
	$series_id = escape($series_id);
	$final_query = "SELECT v.*,
				GROUP_CONCAT(DISTINCT CONCAT(v.id, '___', v.status, '___', f.name, '___', f.type, '___', f.id)
					ORDER BY f.name
					SEPARATOR '|'
				) fansub_info,
				GROUP_CONCAT(DISTINCT f.name
					ORDER BY f.name
					SEPARATOR ' + ') fansub_name,
				uvr.rating user_rating
			FROM version v
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN user_version_rating uvr ON v.id=uvr.version_id AND uvr.user_id=".(!empty($user) ? $user['id'] : '0')."
			WHERE v.series_id=$series_id
			GROUP BY v.id
			ORDER BY v.status ASC,
				v.created ASC";
	return query($final_query);
}

function query_episodes_for_series_version($series_id, $version_id) {
	$series_id = escape($series_id);
	$version_id = escape($version_id);
	$final_query = "SELECT e.*,
				IFNULL(et.title, e.description) title,
				d.number division_number,
				IFNULL(vd.title, d.name) division_name,
				d.number_of_episodes division_number_of_episodes
			FROM episode e
				LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=$version_id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=$version_id
			WHERE e.series_id=$series_id
			ORDER BY d.number ASC,
				e.number IS NULL ASC,
				e.number ASC,
				IFNULL(et.title,e.description) ASC";
	return query($final_query);
}

function query_available_files_in_version($version_id, $episode_ids, $linked_episode_ids) {
	$version_id = escape($version_id);
	$final_query = "SELECT f.*
			FROM file f
			WHERE ((f.episode_id IN (".implode(',',$episode_ids).") AND f.version_id=$version_id)
				OR (f.episode_id IN (".implode(',',$linked_episode_ids).") AND f.version_id IN (
					SELECT v2.id
					FROM episode e2
						LEFT JOIN series s ON e2.series_id=s.id
						LEFT JOIN version v2 ON v2.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id
					WHERE vf.fansub_id IN (
						SELECT fansub_id
						FROM rel_version_fansub
						WHERE version_id=$version_id)
					)
				)
			)
			ORDER BY f.id ASC";
	return query($final_query);
}

function query_available_seen_files_in_version($user_id, $version_id, $episode_ids, $linked_episode_ids) {
	$user_id = escape($user_id);
	$version_id = escape($version_id);
	$final_query = "SELECT f.*
			FROM file f
			WHERE ((f.episode_id IN (".implode(',',$episode_ids).") AND f.version_id=$version_id)
				OR (f.episode_id IN (".implode(',',$linked_episode_ids).") AND f.version_id IN (
					SELECT v2.id
					FROM episode e2
						LEFT JOIN series s ON e2.series_id=s.id
						LEFT JOIN version v2 ON v2.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id
					WHERE vf.fansub_id IN (
						SELECT fansub_id
						FROM rel_version_fansub
						WHERE version_id=$version_id)
					)
				)
			) AND f.id IN (
				SELECT ss.file_id
				FROM user_file_seen_status ss
				WHERE ss.user_id=$user_id
					AND ss.is_seen=1
			)
			ORDER BY f.id ASC";
	return query($final_query);
}

function query_files_by_episode_id_and_version_id($user_id, $episode_id, $version_id) {
	//This ends up being printed to the episode list: we need to get the user progress and user seen status
	$user_id = escape($user_id);
	$episode_id = escape($episode_id);
	$version_id = escape($version_id);
	$final_query = "SELECT f.*,
				IF(IFNULL(ufss.is_seen, 0)=1,1,IFNULL(ufss.position, 0)/f.length) progress_percent,
				IFNULL(ufss.is_seen, 0) is_seen
			FROM file f
				LEFT JOIN user_file_seen_status ufss ON f.id=ufss.file_id AND ufss.user_id=$user_id
			WHERE f.episode_id=$episode_id
				AND f.version_id=$version_id
			ORDER BY f.variant_name ASC,
				f.id ASC";
	return query($final_query);
}

function query_files_by_linked_episode_id_and_version_id($user_id, $linked_episode_id, $version_id) {
	//This ends up being printed to the episode list: we need to get the user progress and user seen status
	$user_id = escape($user_id);
	$linked_episode_id = escape($linked_episode_id);
	$version_id = escape($version_id);
	$final_query = "SELECT f.*,
				IF(IFNULL(ufss.is_seen, 0)=1,1,IFNULL(ufss.position, 0)/f.length) progress_percent,
				IFNULL(ufss.is_seen, 0) is_seen
			FROM file f
				LEFT JOIN user_file_seen_status ufss ON f.id=ufss.file_id AND ufss.user_id=$user_id
			WHERE f.episode_id=$linked_episode_id
				AND f.version_id IN (
					SELECT v2.id
					FROM episode e2
						LEFT JOIN series s ON e2.series_id=s.id
						LEFT JOIN version v2 ON v2.series_id=s.id
						LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id
					WHERE vf.fansub_id IN (
						SELECT fansub_id
						FROM rel_version_fansub
						WHERE version_id=$version_id
					)
						AND e2.id=$linked_episode_id
				)
			ORDER BY f.variant_name ASC,
				f.id ASC";
	return query($final_query);
}

function query_series_from_episode_id($episode_id) {
	$episode_id = escape($episode_id);
	$final_query = "SELECT s.*
			FROM episode e
				LEFT JOIN series s ON e.series_id=s.id
			WHERE e.id=$episode_id";
	return query($final_query);
}

function query_version_from_linked_episode_id_and_version_id($linked_episode_id, $version_id) {
	$linked_episode_id = escape($linked_episode_id);
	$version_id = escape($version_id);
	$final_query = "SELECT v.*,
				GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_name
			FROM version v
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
			WHERE v.id IN (
				SELECT v2.id
				FROM episode e2
					LEFT JOIN series s ON e2.series_id=s.id
					LEFT JOIN version v2 ON v2.series_id=s.id
					LEFT JOIN rel_version_fansub vf ON v2.id=vf.version_id
				WHERE vf.fansub_id IN (
					SELECT fansub_id
					FROM rel_version_fansub
					WHERE version_id=$version_id
				)
					AND e2.id=$linked_episode_id
			)";
	return query($final_query);
}

function query_extras_by_version_id($version_id) {
	$version_id = escape($version_id);
	$final_query = "SELECT DISTINCT f.extra_name
			FROM file f
			WHERE version_id=$version_id
				AND f.episode_id IS NULL
			ORDER BY extra_name ASC";
	return query($final_query);
}

function query_extras_files_by_extra_name_and_version_id($user_id, $extra_name, $version_id) {
	//This ends up being printed to the episode list: we need to get the user progress and user seen status
	$user_id = escape($user_id);
	$extra_name = escape($extra_name);
	$version_id = escape($version_id);
	$final_query = "SELECT f.*,
				IF(IFNULL(ufss.is_seen, 0)=1,1,IFNULL(ufss.position, 0)/f.length) progress_percent,
				IFNULL(ufss.is_seen, 0) is_seen
			FROM file f
				LEFT JOIN user_file_seen_status ufss ON f.id=ufss.file_id AND ufss.user_id=$user_id
			WHERE f.episode_id IS NULL
				AND f.extra_name='$extra_name'
				AND f.version_id=$version_id
			ORDER BY f.id ASC";
	return query($final_query);
}

function query_fansubs_by_version_id($version_id) {
	$version_id = escape($version_id);
	$final_query = "SELECT f.*,
				vf.downloads_url
			FROM rel_version_fansub vf
				LEFT JOIN fansub f ON vf.fansub_id=f.id
			WHERE vf.version_id=$version_id
			ORDER BY f.name ASC";
	return query($final_query);
}

function query_related_series($user, $series_id, $series_author, $num_of_genres_in_common, $max_items, $own_type) {
	$series_id=intval($series_id);
	$series_author=escape($series_author);
	$num_of_genres_in_common=intval($num_of_genres_in_common);
	$max_items = intval($max_items);

	$related_query="SELECT rs.related_series_id id,
				v.title version_title
			FROM related_series rs
				LEFT JOIN series s ON rs.related_series_id=s.id
				LEFT JOIN version v ON v.id=s.default_version_id
			WHERE s.type".($own_type==TRUE ? "=" : "<>")."'".CATALOGUE_ITEM_TYPE."'
				AND rs.series_id=$series_id
			UNION
			SELECT id,
				NULL
			FROM series s
			WHERE s.type".($own_type==TRUE ? "=" : "<>")."'".CATALOGUE_ITEM_TYPE."'
				AND id<>$series_id
				AND author='$series_author'
			UNION
			SELECT series_id id,
				NULL
			FROM rel_series_genre sg
				LEFT JOIN series s ON sg.series_id=s.id
			WHERE s.type".($own_type==TRUE ? "=" : "<>")."'".CATALOGUE_ITEM_TYPE."'
				AND sg.series_id<>$series_id
			GROUP BY series_id
			HAVING COUNT(CASE WHEN genre_id IN (SELECT genre_id FROM rel_series_genre WHERE series_id=$series_id) THEN 1 END)>=$num_of_genres_in_common
			ORDER BY version_title IS NULL ASC,
				version_title ASC,
				RAND() LIMIT $max_items";
	$resultin = query($related_query);
	$in = array(-1);
	while ($row = mysqli_fetch_assoc($resultin)) {
		$in[]=$row['id'];
	}
	mysqli_free_result($resultin);

	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type".($own_type==TRUE ? "=" : "<>")."'".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_blacklisted_fansubs_condition($user)."
				AND ".get_internal_cancelled_projects_condition($user)."
				AND ".get_internal_lost_projects_condition($user)."
				AND s.id IN (".implode(',',$in).")
			GROUP BY s.id
			ORDER BY FIELD(s.id, ".implode(',',$in).") ASC";
	return query($final_query);
}

function query_search_filter($user, $text, $type, $subtype, $min_score, $max_score, $min_year, $max_year, $min_length, $max_length, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $content_types, $origins, $genres_included_ids, $genres_excluded_ids, $statuses) {
	$text = str_replace(" ", "%", $text);
	$text = escape_for_like($text);
	$type = escape($type);
	$subtype = escape($subtype);
	$min_score = floatval($min_score);
	$max_score = floatval($max_score);
	$min_year = intval($min_year);
	$max_year = intval($max_year);
	$min_length = intval($min_length);
	$max_length = intval($max_length);
	$fansub_slug = escape($fansub_slug);
	//No need to escape $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $content_types, $origins, $genres_included_ids, $genres_excluded_ids, $statuses: they come from code
	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='$type'
				AND (s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR EXISTS(SELECT v.id FROM version v WHERE v.series_id=s.id AND (v.title LIKE '%$text%' OR v.alternate_titles LIKE '%$text%')) OR s.studio LIKE '%$text%' OR s.author LIKE '%$text%' OR s.keywords LIKE '%$text%')
				AND (".($min_score==0 ? "s.score IS NULL OR " : '')."(s.score>=$min_score AND s.score<=$max_score))
				AND ".(count($ratings)>0 ? "s.rating IN ('".implode("', '",$ratings)."')" : "1")."
				AND (".($min_year==1950 ? "s.publish_date IS NULL OR " : '')."(YEAR(s.publish_date)>=$min_year AND YEAR(s.publish_date)<=$max_year))
				AND ".($show_blacklisted_fansubs ? '1' : get_internal_blacklisted_fansubs_condition($user))."
				AND ".($show_lost_content ? '1' : 'v.is_missing_episodes=0')."
				AND ".((SITE_IS_HENTAI || CATALOGUE_ITEM_TYPE=='liveaction' || $type=='liveaction') ? '1' : get_internal_demographics_condition($demographic_ids, $show_no_demographics))."
				AND ".((SITE_IS_HENTAI || CATALOGUE_ITEM_TYPE!='manga' || $type!='manga') ? '1' : get_internal_origins_condition($origins))."
				AND ".get_internal_content_types_condition($content_types)."
				AND ".get_internal_included_genres_condition($genres_included_ids)."
				AND ".get_internal_excluded_genres_condition($genres_excluded_ids)."
				AND ".get_internal_statuses_condition($statuses)."
				AND ".($subtype=='all' ? "1" : "subtype='$subtype'")."
				AND ".get_internal_length_condition($type, $length_type, $min_length, $max_length)."
				AND ".(!empty($fansub_slug) ? "v.id IN (SELECT DISTINCT sqvf.version_id FROM rel_version_fansub sqvf LEFT JOIN fansub sqf ON sqvf.fansub_id=sqf.id WHERE sqf.slug='$fansub_slug')" : "1")."
			GROUP BY s.id
			ORDER BY default_version_title ASC";
	return query($final_query);
}

function query_autocomplete($user, $text, $type) {
	$text = str_replace(" ", "%", $text);
	$text = escape_for_like($text);
	$type = escape($type);
	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='$type'
				AND (s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR EXISTS(SELECT v.id FROM version v WHERE v.series_id=s.id AND (v.title LIKE '%$text%' OR v.alternate_titles LIKE '%$text%')) OR s.studio LIKE '%$text%' OR s.author LIKE '%$text%' OR s.keywords LIKE '%$text%')
			GROUP BY s.id
			ORDER BY default_version_title LIKE '$text%' DESC, default_version_title ASC";
	return query($final_query);
}

function query_all_fansubs_with_versions($user) {
	$final_query = "SELECT DISTINCT f.*
			FROM rel_version_fansub vf
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN version v ON vf.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND ".get_internal_blacklisted_fansubs_condition($user)."
			ORDER BY f.name ASC";
	return query($final_query);
}

function query_player_details_by_file_id($file_id) {
	$file_id = intval($file_id);
	$final_query = "SELECT GROUP_CONCAT(DISTINCT fa.name ORDER BY fa.name SEPARATOR ' + ') fansub_name,
				v.series_id series_id,
				f.version_id version_id,
				v.show_episode_numbers,
				v.title version_title,
				s.type series_type,
				s.subtype series_subtype,
				IF(f.episode_id IS NULL,TRUE,FALSE) is_extra,
				f.length,
				e.number episode_number,
				e.linked_episode_id,
				IFNULL(et.title,e.description) title,
				f.extra_name,
				s.reader_type,
				IF(f.extra_name IS NOT NULL,
					CONCAT(v.title, ' - Contingut extra'),
					IFNULL(vd.title,d.name)
				) division_name
			FROM file f
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN rel_version_fansub vf ON vf.version_id=v.id
				LEFT JOIN fansub fa ON vf.fansub_id=fa.id
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			WHERE f.id=$file_id";
	return query($final_query);
}

function query_links_by_file_id($file_id) {
	$file_id = intval($file_id);
	$final_query = "SELECT l.*
			FROM link l
			WHERE l.file_id=$file_id
			ORDER BY l.url ASC";
	return query($final_query);
}

function query_user_file_seen_status_by_file_id($user_id, $file_id) {
	$user_id = intval($user_id);
	$file_id = intval($file_id);
	$final_query = "SELECT *
			FROM user_file_seen_status
			WHERE user_id=$user_id
				AND file_id=$file_id";
	return query($final_query);
}

function query_shared_play_session_by_id($id) {
	$id = escape($id);
	$final_query = "SELECT *, UNIX_TIMESTAMP(updated) updated_timestamp
			FROM shared_play_session
			WHERE id='$id'";
	return query($final_query);
}

function query_view_session_by_id($id) {
	$id = escape($id);
	$final_query = "SELECT *
			FROM view_session
			WHERE id='$id'";
	return query($final_query);
}

function query_view_session_for_user_and_file_id($user_id, $file_id) {
	$user_id = intval($user_id);
	$file_id = intval($file_id);
	$final_query = "SELECT *
			FROM view_session
			WHERE user_id=$user_id
				AND file_id=$file_id";
	return query($final_query);
}

function query_view_session_for_anon_id_and_file_id($anon_id, $file_id) {
	$anon_id = escape($anon_id);
	$file_id = intval($file_id);
	$final_query = "SELECT *
			FROM view_session
			WHERE anon_id='$anon_id'
				AND file_id=$file_id";
	return query($final_query);
}

function query_version_by_file_id($file_id) {
	$file_id = intval($file_id);
	$final_query = "SELECT version_id
			FROM file f
			WHERE f.id=$file_id";
	return query($final_query);
}

function query_version_comments($version_id, $user) {
	$version_id = intval($version_id);
	$final_query = "SELECT c.*,
				u.username,
				u.avatar_filename,
				f.id fansub_id,
				f.name fansub_name,
				UNIX_TIMESTAMP(c.created) created_timestamp,
				IF(c.last_seen_episode_id IS NULL,
					NULL,
					IF((s.subtype='movie' OR s.subtype='oneshot') AND s.number_of_episodes=1,
						IF(s.type='manga','".lang('catalogue.query.read')."','".lang('catalogue.query.seen')."'),
						IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."')),
								CONCAT('".lang('generic.query.episode_space')."', REPLACE(TRIM(e.number)+0, '.', '".lang('generic.decimal_point')."'))
							),
							IF((SELECT COUNT(*) FROM division d2 WHERE d2.series_id=s.id AND d2.number_of_episodes>0)>1,
								CONCAT(IFNULL(vd.title,d.name), ' - ', IFNULL(et.title, e.description)),
								IFNULL(et.title, e.description)
							)
						)
					)
				) episode_title,
				IF(".(!empty($user) ? "EXISTS(SELECT * FROM user_file_seen_status ufss WHERE ufss.user_id=${user['id']} AND ufss.file_id IN (SELECT id FROM file f WHERE f.episode_id=c.last_seen_episode_id AND f.version_id=c.version_id) AND ufss.is_seen=1)" : '0').",
					1,
					0
				) is_seen_by_user
			FROM comment c
			LEFT JOIN user u ON c.user_id=u.id
			LEFT JOIN fansub f ON c.fansub_id=f.id
			LEFT JOIN episode e ON c.last_seen_episode_id=e.id
			LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=c.version_id
			LEFT JOIN version v ON c.version_id=v.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN division d ON e.division_id=d.id
			LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			WHERE c.version_id=$version_id
				AND c.reply_to_comment_id IS NULL
				AND (c.user_id IS NULL OR u.status<>1".(!empty($user) ? " OR u.id=${user['id']}" : '').")
			ORDER BY c.last_replied DESC";
	return query($final_query);
}

function query_comment_replies($comment_id) {
	$comment_id = intval($comment_id);
	$final_query = "SELECT c.*, u.username, u.avatar_filename, f.id fansub_id, f.name fansub_name, UNIX_TIMESTAMP(c.created) created_timestamp
			FROM comment c
			LEFT JOIN user u ON c.user_id=u.id
			LEFT JOIN fansub f ON c.fansub_id=f.id
			WHERE c.reply_to_comment_id=$comment_id
			ORDER BY c.created ASC";
	return query($final_query);
}
?>

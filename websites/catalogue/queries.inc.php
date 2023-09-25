<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");
require_once("../common.fansubs.cat/queries.inc.php");

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
				IFNULL(MAX(b.total_views), 0) max_views
			FROM (
				SELECT a.series_id,
					SUM(a.views) total_views
				FROM (
					SELECT SUM(vi.views) views,
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
					GROUP BY f.version_id, f.episode_id
					) a
				GROUP BY a.episode_id
				) b
			GROUP BY b.series_id
			ORDER BY max_views DESC,
				b.series_id DESC";
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
	$final_query = "INSERT INTO user_file_seen_status
				(user_id, file_id, is_seen, position, last_viewed)
			VALUES ($user_id, $file_id, $is_seen, 0, NULL)
			ON DUPLICATE KEY UPDATE is_seen=$is_seen,position=0";
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
				(id, file_id, type, user_id, anon_id, progress, length, created, updated, view_counted, is_casted, source, ip, user_agent)
			VALUES ('$view_id', $file_id, '$type', $user_id, $anon_id, 0, $length, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, NULL, 0, '$source', '$ip', '$user_agent')";
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
			SET progress=IF($is_casted=1,length,GREATEST(progress,$progress)),
				updated=CURRENT_TIMESTAMP,
				is_casted=$is_casted,
				source='$source',
				ip='$ip',
				user_agent='$user_agent'
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
									LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=f.version_id
									WHERE ufss.is_seen=1
										AND ufss.user_id=$user_id
										AND f.version_id IN (SELECT f2.version_id
												FROM file f2
												WHERE f2.id=$file_id)
									ORDER BY e.number IS NULL ASC,
										e.number DESC,
										IFNULL(et.title, e.description) DESC
									LIMIT 1),-1)
			)
			ON DUPLICATE KEY UPDATE last_seen_episode_id=IFNULL((SELECT e.id last_episode_seen_id
									FROM user_file_seen_status ufss
 									LEFT JOIN file f ON ufss.file_id=f.id
									LEFT JOIN episode e ON f.episode_id=e.id
									LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=f.version_id
									WHERE ufss.is_seen=1
										AND ufss.user_id=$user_id
										AND f.version_id IN (SELECT f2.version_id
												FROM file f2
												WHERE f2.id=$file_id)
									ORDER BY e.number IS NULL ASC,
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

function query_manga_division_data_from_file_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id = intval($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				s.slug,
				IF(s.subtype='oneshot', NULL, d.number) division_number
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
				s.slug,
				IF(s.type='oneshot', NULL, d.number) division_number
			FROM division d
				LEFT JOIN series s ON d.series_id=s.id
			WHERE s.type='manga'
				AND d.id=$old_piwigo_id";
	return query($final_query);
}

function query_manga_series_data_from_series_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id = intval($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				s.slug
			FROM series s
			WHERE s.type='manga'
				AND s.id=$old_piwigo_id";
	return query($final_query);
}

function query_series_data_from_slug_and_type($slug, $type) {
	$slug = escape($slug);
	$type = escape($type);
	$final_query = "SELECT s.slug,
				s.rating
			FROM series s
			WHERE s.type='$type'
				AND s.slug='$slug'";
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
				AND v.is_featurable=1
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
				AND v.is_featurable=1
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
				AND s.publish_date IS NOT NULL
				AND YEAR(s.publish_date)=".date('Y')."
				AND ".get_internal_hentai_condition()."
				AND v.status=1
				AND v.is_featurable=1
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
				GROUP_CONCAT(DISTINCT CONCAT(t.version_id, '___', t.status, '___', t.fansub_name, '___', t.fansub_type, '___', t.fansub_id)
					ORDER BY t.fansub_name
					SEPARATOR '|'
				) fansub_info
			FROM (SELECT f.id file_id,
					f.version_id,
					IF(s.type='manga' AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
						IF(d.name IS NULL,
							CONCAT('Vol. ', REPLACE(TRIM(d.number)+0,'.',',')),
							d.name
						),
						NULL
					) division_name,
					IF(s.show_episode_numbers=1,
						REPLACE(TRIM(e.number)+0, '.', ','),
						NULL
					) episode_number,
					IF(s.subtype='oneshot',
						'One-shot',
						IF(s.subtype='movie' AND s.number_of_episodes=1,
							'Film',
							IF(et.title IS NOT NULL,
								et.title,
								IF(e.number IS NULL,
									e.description,
									et.title
								)
							)
						)
					) episode_title,
					f.extra_name,
					v.series_id,
					v.status,
					IF((SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1 AND d.name IS NOT NULL,
						d.name,
						s.name
					) series_name,
					s.slug series_slug,
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
						IF(d.name IS NULL,
							CONCAT('Vol. ', REPLACE(TRIM(d.number)+0,'.',',')),
							d.name
						),
						NULL
					) division_name,
					IF(s.show_episode_numbers=1,
						REPLACE(TRIM(e.number)+0, '.', ','),
						NULL
					) episode_number,
					IF(s.subtype='oneshot',
						'One-shot',
						IF(s.subtype='movie' AND s.number_of_episodes=1,
							'Film',
							IF(et.title IS NOT NULL,
								et.title,
								IF(e.number IS NULL,
									e.description,
									et.title
								)
							)
						)
					) episode_title,
					f.extra_name,
					v.series_id,
					v.status,
					IF((SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1 AND d.name IS NOT NULL,
						d.name,
						s.name
					) series_name,
					s.slug series_slug,
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
					LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
				WHERE f.id IN (
						SELECT (SELECT f.id
							FROM file f
							LEFT JOIN episode e2 ON f.episode_id=e2.id
							LEFT JOIN episode_title et2 ON et2.episode_id=e2.id AND et2.version_id=f.version_id
							WHERE f.version_id=v.id
								AND f.episode_id IS NOT NULL
								AND ((e2.number IS NULL AND e1.number IS NULL AND IFNULL(et2.title,e2.description)>IFNULL(et1.title,e1.description)) OR (e2.number IS NULL AND e1.number IS NOT NULL) OR (e2.number>e1.number))
							ORDER BY e2.number IS NULL ASC,
								e2.number ASC,
								IFNULL(et2.title, e2.description) ASC
							LIMIT 1) newer_episode_file_id
						FROM user_version_followed uvf
						LEFT JOIN version v ON v.id=uvf.version_id
						LEFT JOIN episode e1 ON e1.id=uvf.last_seen_episode_id
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
					AND f.id NOT IN (
						SELECT ufss.file_id
						FROM user_file_seen_status ufss
						WHERE ufss.user_id=$user_id
						AND ufss.is_seen=1
					)
					AND s.type='".CATALOGUE_ITEM_TYPE."'
					AND f.is_lost=0
					AND ".get_internal_hentai_condition()."
			) t
			GROUP BY t.version_id
			ORDER BY t.origin ASC,
				t.last_viewed DESC";
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
	$final_query = get_internal_home_base_query($user)."
			GROUP BY v.id
			ORDER BY last_updated DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_home_last_finished($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
				AND completed_date IS NOT NULL
			GROUP BY v.id
			ORDER BY completed_date DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_home_random($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
				AND completed_date IS NOT NULL
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

function query_home_best_rated($user, $max_items) {
	$max_items = intval($max_items);
	$final_query = get_internal_home_base_query($user)."
			GROUP BY s.id
			ORDER BY s.score DESC
			LIMIT $max_items";
	return query($final_query);
}

function query_series_by_slug($slug, $include_hidden) {
	$slug = escape($slug);
	$final_query = "SELECT s.*, 
				YEAR(s.publish_date) year,
				GROUP_CONCAT(DISTINCT CONCAT(g.id,'|',g.type,'|',g.name) ORDER BY g.name SEPARATOR ' • ') genres,
				(SELECT COUNT(DISTINCT d.id) FROM division d WHERE d.series_id=s.id AND d.number_of_episodes>0) divisions
			FROM series s
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND slug='$slug'
				AND ".get_internal_hentai_condition()."
				AND ".($include_hidden ? '1' : '(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id)>0')."
			GROUP BY s.id";
	return query($final_query);
}

function query_series_by_file_id($file_id) {
	$file_id = escape($file_id);
	$final_query = "SELECT s.*
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
				IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title,
				d.number division_number,
				d.name division_name
			FROM episode e
				LEFT JOIN episode_title et ON e.id=et.episode_id AND et.version_id=$version_id
				LEFT JOIN division d ON e.division_id=d.id
			WHERE e.series_id=$series_id
			ORDER BY d.number IS NULL ASC,
				d.number ASC,
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
				s.name
			FROM related_series rs
				LEFT JOIN series s ON rs.related_series_id=s.id
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
			ORDER BY name IS NULL ASC,
				name ASC,
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

function query_search_filter($user, $text, $type, $subtype, $min_score, $max_score, $min_year, $max_year, $min_length, $max_length, $length_type, $ratings, $fansub_slug, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $genres_included_ids, $genres_excluded_ids, $statuses) {
	$text = str_replace(" ", "%", $text);
	$text = escape($text);
	$type = escape($type);
	$subtype = escape($subtype);
	$min_score = floatval($min_score);
	$max_score = floatval($max_score);
	$min_year = intval($min_year);
	$max_year = intval($max_year);
	$min_length = intval($min_length);
	$max_length = intval($max_length);
	$fansub_slug = escape($fansub_slug);
	//No need to escape $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $genres_included_ids, $genres_excluded_ids, $statuses: they come from code
	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='$type'
				AND (s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR s.studio LIKE '%$text%' OR s.author LIKE '%$text%' OR s.keywords LIKE '%$text%')
				AND (".($min_score==0 ? "s.score IS NULL OR " : '')."(s.score>=$min_score AND s.score<=$max_score))
				AND ".(count($ratings)>0 ? "s.rating IN ('".implode("', '",$ratings)."')" : "1")."
				AND (".($min_year==1950 ? "s.publish_date IS NULL OR " : '')."(YEAR(s.publish_date)>=$min_year AND YEAR(s.publish_date)<=$max_year))
				AND ".($show_blacklisted_fansubs ? '1' : get_internal_blacklisted_fansubs_condition($user))."
				AND ".($show_lost_content ? '1' : 'v.is_missing_episodes=0')."
				AND ".((SITE_IS_HENTAI || CATALOGUE_ITEM_TYPE=='liveaction') ? '1' : get_internal_demographics_condition($demographic_ids, $show_no_demographics))."
				AND ".get_internal_included_genres_condition($genres_included_ids)."
				AND ".get_internal_excluded_genres_condition($genres_excluded_ids)."
				AND ".get_internal_statuses_condition($statuses)."
				AND ".($subtype=='all' ? "1" : "subtype='$subtype'")."
				AND ".get_internal_length_condition($type, $length_type, $min_length, $max_length)."
				AND ".(!empty($fansub_slug) ? "v.id IN (SELECT DISTINCT sqvf.version_id FROM rel_version_fansub sqvf LEFT JOIN fansub sqf ON sqvf.fansub_id=sqf.id WHERE sqf.slug='$fansub_slug')" : "1")."
			GROUP BY s.id
			ORDER BY s.name ASC";
	return query($final_query);
}

function query_autocomplete($user, $text, $type) {
	$text = str_replace(" ", "%", $text);
	$text = escape($text);
	$type = escape($type);
	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='$type'
				AND (s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR s.studio LIKE '%$text%' OR s.author LIKE '%$text%' OR s.keywords LIKE '%$text%')
			GROUP BY s.id
			ORDER BY s.name LIKE '$text%' DESC, s.name ASC";
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
				s.show_episode_numbers,
				s.name series_name,
				s.type series_type,
				s.subtype series_subtype,
				IF(f.episode_id IS NULL,TRUE,FALSE) is_extra,
				f.length,
				e.number episode_number,
				e.linked_episode_id,
				IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title,
				f.extra_name,
				s.reader_type
			FROM file f
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
				LEFT JOIN rel_version_fansub vf ON vf.version_id=v.id
				LEFT JOIN fansub fa ON vf.fansub_id=fa.id
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
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
	$anon_id = intval($anon_id);
	$file_id = intval($file_id);
	$final_query = "SELECT *
			FROM view_session
			WHERE anon_id=$anon_id
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
?>

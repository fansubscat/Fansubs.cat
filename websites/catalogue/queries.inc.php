<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

// INTERNAL

function get_internal_hentai_condition() {
	if (SITE_IS_HENTAI) {
		return "s.rating='XXX'";
	} else {
		return "s.rating<>'XXX'";
	}
}

function get_internal_viewed_files_condition($user) {
	if (!empty($user)) {
		$viewed_files_condition = "ls.id NOT IN (SELECT ufp.file_id
								FROM user_file_progress ufp
								WHERE ufp.user_id=".intval($user['id']).")";
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

function get_internal_catalogue_base_query_portion($user) {
	return "SELECT s.*,
			(SELECT nv.id
				FROM version nv
				WHERE nv.files_updated=MAX(v.files_updated)
					AND nv.series_id=s.id
					AND nv.is_hidden=0
				LIMIT 1
			) version_id,
			GROUP_CONCAT(DISTINCT CONCAT(v.id, '___', f.name, '___', f.type, '___', f.id)
				ORDER BY v.id,
					f.name
				SEPARATOR '|'
			) fansub_info,
			GROUP_CONCAT(DISTINCT sg.genre_id) genres,
			GROUP_CONCAT(DISTINCT REPLACE(REPLACE(g.name, ' ', ' '), '-', '‑')
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
			AND ".get_internal_hentai_condition();
}

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
	$demographics_condition = "1";
	$no_demographics_condition = "0";
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
	return "1";
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
					GROUP BY s.id HAVING AVG(f.length)>=".intval($min_length)." AND AVG(f.length)<=".intval($max_length==100 ? 1000000 : $max_length)."
				)";
		} else if ($min_length!=0 || $max_length!=120) {
			return "0";
		}
	} else { //Anime and live action
		if ($length_type=='minutes') {
			return "s.id IN (SELECT DISTINCT s.id
					FROM series s
						LEFT JOIN version v ON s.id=v.series_id
						LEFT JOIN file f ON v.id=f.version_id
					WHERE s.type='$type'
					GROUP BY s.id HAVING AVG(f.length)>=".(intval($min_length)*60)." AND AVG(f.length)<=".(intval($max_length==120 ? 1000000 : $max_length)*60)."
				)";
		} else if ($min_length!=0 || $max_length!=100) {
			return "0";
		}
	}
	return "1";
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

function query_series_by_id($series_id) {
	$series_id = intval($series_id);
	$final_query = "SELECT *
			FROM series
			WHERE id=$series_id";
	return query($final_query);
}

function query_number_of_versions_by_series_id($series_id) {
	$series_id = intval($series_id);
	$final_query = "SELECT COUNT(*) cnt
			FROM version
			WHERE series_id=$series_id
				AND is_hidden=0";
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

function query_series_data_for_preview_image_by_slug($slug) {
	$slug = escape($slug);
	$final_query = "SELECT s.*,
				YEAR(s.publish_date) year,
				GROUP_CONCAT(DISTINCT g.name
					ORDER BY g.name
					SEPARATOR ', '
					) genres,
				(SELECT COUNT(DISTINCT d.id)
					FROM division d
					WHERE d.series_id=s.id
					AND d.number_of_episodes>0
				) divisions
			FROM series s
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."' AND slug='$slug'
			GROUP BY s.id";
	return query($final_query);
}

function query_version_data_for_preview_image_by_series_id($series_id) {
	$series_id = intval($series_id);
	$final_query = "SELECT v.*,
				GROUP_CONCAT(DISTINCT f.name
					ORDER BY f.name
					SEPARATOR ' + '
				) fansub_name
			FROM version v
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN fansub f ON vf.fansub_id=f.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE v.is_hidden=0
				AND v.series_id=$series_id
			GROUP BY v.id
			ORDER BY v.status DESC,
				v.created DESC";
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
				AND g.name<>'Hentai'
				AND EXISTS(SELECT s.id
					FROM rel_series_genre sg
						LEFT JOIN series s ON sg.series_id=s.id
					WHERE (SELECT COUNT(*)
						FROM version v
						WHERE v.series_id=s.id
							AND v.is_hidden=0
						)>0
						AND sg.genre_id=g.id
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

function query_home_recommended_items($user, $force_recommended_ids_list, $max_items) {
	$max_items = intval($max_items);
	if (count($force_recommended_ids_list)>0) {
		$recommendations_subquery = implode(',', $force_recommended_list); //No need to escape, comes from DB
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
				GROUP_CONCAT(DISTINCT CONCAT(t.version_id, '___', t.fansub_name, '___', t.fansub_type, '___', t.fansub_id)
					ORDER BY t.fansub_name
					SEPARATOR '|'
				) fansub_info
			FROM (SELECT f.id file_id,
					f.version_id,
					IF(s.type='manga' AND v.show_divisions=1 AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
						IF(d.name IS NULL,
							CONCAT('Vol. ', REPLACE(TRIM(d.number)+0,'.',',')),
							d.name
						),
						NULL
					) division_name,
					IF(v.show_episode_numbers=1,
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
					IF(v.show_divisions=1 AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1 AND d.name IS NOT NULL,
						d.name,
						s.name
					) series_name,
					s.slug series_slug,
					fa.name fansub_name,
					fa.type fansub_type,
					fa.id fansub_id,
					ufp.progress/f.length progress_percent,
					ufp.last_viewed last_viewed,
					1 origin
				FROM user_file_progress ufp
					LEFT JOIN file f ON ufp.file_id=f.id
					LEFT JOIN version v ON f.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
					LEFT JOIN rel_version_fansub vf ON f.version_id=vf.version_id
					LEFT JOIN fansub fa ON vf.fansub_id=fa.id
					LEFT JOIN episode e ON f.episode_id=e.id
					LEFT JOIN division d ON e.division_id=d.id
					LEFT JOIN episode_title et ON et.episode_id=e.id AND et.version_id=v.id
				WHERE ufp.user_id=$user_id
					AND s.type='".CATALOGUE_ITEM_TYPE."'
					AND f.is_lost=0
					AND ufp.is_seen=0
					AND ".get_internal_hentai_condition()."
				UNION
				SELECT f.id file_id,
					f.version_id,
					IF(s.type='manga' AND v.show_divisions=1 AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
						IF(d.name IS NULL,
							CONCAT('Vol. ', REPLACE(TRIM(d.number)+0,'.',',')),
							d.name
						),
						NULL
					) division_name,
					IF(v.show_episode_numbers=1,
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
					IF(v.show_divisions=1 AND (SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1 AND d.name IS NOT NULL,
						d.name,
						s.name
					) series_name,
					s.slug series_slug,
					fa.name fansub_name,
					fa.type fansub_type,
					fa.id fansub_id,
					0 progress_percent,
					CURRENT_TIMESTAMP last_viewed,
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

function query_search_filter($user, $text, $type, $subtype, $min_score, $max_score, $min_year, $max_year, $min_length, $max_length, $length_type, $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $genres_included_ids, $genres_excluded_ids, $statuses) {
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
	//No need to escape $ratings, $show_blacklisted_fansubs, $show_lost_content, $show_no_demographics, $demographic_ids, $genres_included_ids, $genres_excluded_ids, $statuses: they come from code
	$final_query = get_internal_catalogue_base_query_portion($user)."
				AND s.type='$type'
				AND (s.name LIKE '%$text%' OR s.alternate_names LIKE '%$text%' OR s.studio LIKE '%$text%' OR s.author LIKE '%$text%' OR s.keywords LIKE '%$text%')
				AND (".($min_score==0 ? "s.score IS NULL OR " : '')."(s.score>=$min_score AND s.score<=$max_score))
				AND ".(count($ratings)>0 ? "s.rating IN ('".implode("', '",$ratings)."')" : "1")."
				AND (".($min_year==1950 ? "s.publish_date IS NULL OR " : '')."(YEAR(s.publish_date)>=$min_year AND YEAR(s.publish_date)<=$max_year))
				AND ".($show_blacklisted_fansubs ? '1' : get_internal_blacklisted_fansubs_condition($user))."
				AND ".($show_lost_content ? '1' : 'v.is_missing_episodes=0')."
				AND ".get_internal_demographics_condition($demographic_ids, $show_no_demographics)."
				AND ".get_internal_included_genres_condition($genres_included_ids)."
				AND ".get_internal_excluded_genres_condition($genres_excluded_ids)."
				AND ".get_internal_statuses_condition($statuses)."
				AND ".($subtype=='all' ? "1" : "subtype='$subtype'")."
				AND ".get_internal_length_condition($type, $length_type, $min_length, $max_length)."
			GROUP BY s.id
			ORDER BY s.name ASC";
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
<?php
require_once(__DIR__.'/../common/common.inc.php');

function get_internal_hentai_condition() {
	if (SITE_IS_HENTAI_OR_OLD_HENTAI) {
		return "s.rating='XXX'";
	} else {
		return "s.rating<>'XXX'";
	}
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

// INSERT

function query_insert_view_session_completed($view_id, $file_id, $type, $user_id, $anon_id, $length, $source, $ip, $user_agent) {
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
			VALUES ('$view_id', $file_id, '$type', $user_id, $anon_id, $length, $length, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 0, '$source', '$ip', '$user_agent')";
	return query($final_query);
}

function query_save_view_completed($file_id, $type, $date, $length) {
	$file_id = intval($file_id);
	$type = escape($type);
	$date = escape($date);
	$length = intval($length);
	$final_query = "REPLACE INTO views
			SELECT $file_id,
				'$date',
				'$type',
				IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
				IFNULL((SELECT views+1 FROM views WHERE file_id=$file_id AND day='$date'),1),
				IFNULL((SELECT total_length+$length FROM views WHERE file_id=$file_id AND day='$date'),$length)";
	return query($final_query);
}

function query_get_all_manga_files() {
	$final_query = "SELECT f.*,
				v.slug,
				IF(f.extra_name IS NULL, FALSE, TRUE) is_extra
			FROM file f
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='manga'
				AND f.is_lost=0
			ORDER BY v.title ASC,
				f.original_filename ASC";
	return query($final_query);
}

function query_get_unconverted_links($file_id) {
	$file_id_condition = (!empty($file_id) ? 'f.id>='.intval($file_id) : '1');
	$final_query = "SELECT l.*,
				s.type,
				v.storage_folder,
				v.storage_processing,
				IF(f.extra_name IS NULL, FALSE, TRUE) is_extra
			FROM link l
				LEFT JOIN file f ON l.file_id=f.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE url NOT LIKE 'storage://%'
				AND $file_id_condition
				AND NOT EXISTS (SELECT * FROM link l2 WHERE l2.file_id=l.file_id AND l2.url LIKE 'storage://%')
			ORDER BY v.title ASC,
				f.id ASC";
	return query($final_query);
}

function query_get_converted_links($file_id) {
	$file_id_condition = (!empty($file_id) ? 'f.id>='.intval($file_id) : '1');
	$final_query = "SELECT l.*,
				s.type,
				v.storage_folder,
				v.storage_processing,
				IF(f.extra_name IS NULL, FALSE, TRUE) is_extra,
				f.length
			FROM link l
				LEFT JOIN file f ON l.file_id=f.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE url LIKE 'storage://%'
				AND $file_id_condition
			ORDER BY v.title ASC,
				f.id ASC";
	return query($final_query);
}

function query_insert_link($file_id, $url, $original_url, $resolution) {
	$file_id=intval($file_id);
	$url=escape($url);
	$original_url=escape($original_url);
	$resolution=escape($resolution);
	$final_query = "INSERT INTO link (file_id, url, resolution, created, created_by, updated, updated_by)
			SELECT $file_id,
				'$url',
				'$resolution',
				CURRENT_TIMESTAMP,
				'API',
				CURRENT_TIMESTAMP,
				'API'
			FROM link
			WHERE EXISTS (SELECT url
					FROM link
					WHERE url='$original_url'
					AND file_id=$file_id
				)
			LIMIT 1";
	return query($final_query);
}

function query_update_file_length($file_id, $duration) {
	$file_id=intval($file_id);
	$duration=intval($duration);
	$final_query = "UPDATE file
			SET length=$duration
			WHERE id=$file_id";
	return query($final_query);
}

// SELECT

function query_fansub_slug_from_ping_token($token) {
	$token = escape($token);
	$final_query = "SELECT slug
			FROM fansub
			WHERE ping_token='$token'";
	return query($final_query);
}

function query_fansubs_for_api_response() {
	$final_query = "SELECT id,
				slug,
				name,
				url,
				IF(f.status=1,1,0) is_active,
				is_historical,
				archive_url
			FROM fansub f
			UNION
			SELECT NULL id,
				'fansubs-cat' slug,
				'".escape(MAIN_SITE_NAME)."' name,
				NULL,
				0,
				0,
				NULL
			ORDER BY name ASC";
	return query($final_query);
}

function query_news_for_api_response($offset, $query, $fansub_slugs) {
	$offset = intval($offset);
	$query = escape_for_like($query);
	$fansubs_condition = "1";
	
	if (count($fansub_slugs)>0) {
		foreach ($fansub_slugs as &$fansub_slug){
			$fansub_slug = escape($fansub_slug);
		}
		$fansubs_condition = "IFNULL(f.slug,'fansubs-cat') IN ('" . implode("', '", $fansub_slugs) . "')";
	}

	$final_query = "SELECT n.*,
				IFNULL(f.slug,'fansubs-cat') fansub_slug,
				f.name fansub_name
			FROM news n
				LEFT JOIN fansub f ON n.fansub_id=f.id
			WHERE (n.title LIKE '%$query%' OR n.contents LIKE '%$query%')
				AND $fansubs_condition
			ORDER BY n.date DESC
			LIMIT 25
			OFFSET $offset";
	return query($final_query);
}

function query_popular_manga($offset, $max_items) {
	$offset = intval($offset);
	$max_items = intval($max_items);
	$final_query = "SELECT a.*, 
				GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres,
				(SELECT COUNT(*) FROM version v WHERE v.series_id=a.id AND v.status=2) versions_in_progress
			FROM (
					SELECT
						SUM(vi.views) views,
						fi.version_id,
						s.*,
						dv.slug default_version_slug,
						dv.title default_version_title,
						dv.synopsis default_version_synopsis
					FROM series s
						LEFT JOIN version dv ON s.default_version_id=dv.id
						LEFT JOIN episode c ON c.series_id=s.id
						LEFT JOIN file fi ON fi.episode_id=c.id
						LEFT JOIN views vi ON vi.file_id=fi.id
					WHERE s.type='manga'
						AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0
						AND fi.episode_id IS NOT NULL
						AND ".get_internal_hentai_condition()."
						AND vi.day>='".date("Y-m-d", strtotime(date('Y-m-d')."-14 days"))."'
					GROUP BY fi.version_id, fi.episode_id
			) a
				LEFT JOIN rel_series_genre sg ON a.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			GROUP BY a.id
			ORDER BY MAX(a.views) DESC,
				a.default_version_title ASC
			LIMIT $max_items
			OFFSET $offset";
	return query($final_query);
}

function query_recent_manga($offset, $max_items) {
	$offset=intval($offset);
	$max_items=intval($max_items);
	$final_query = "SELECT s.*,
				dv.slug default_version_slug,
				dv.title default_version_title,
				dv.synopsis default_version_synopsis,
				GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres,
				(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.status=2) versions_in_progress
			FROM series s
				LEFT JOIN version dv ON s.default_version_id=dv.id
				LEFT JOIN version v ON s.id=v.series_id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='manga'
				AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0
				AND ".get_internal_hentai_condition()."
			GROUP BY s.id
			ORDER BY MAX(v.files_updated) DESC
			LIMIT $max_items
			OFFSET $offset";
	return query($final_query);
}

function query_search_manga($offset, $max_items, $query, $type, $statuses, $demographies, $genres_include, $genres_exclude, $themes_include, $themes_exclude) {
	$offset=intval($offset);
	$max_items=intval($max_items);
	$query = escape_for_like($query);
	$type_condition = "1";
	$statuses_condition = "1";
	$demographies_condition = "1";

	if ($type!='all') {
		$type_condition = "s.subtype='".escape($type)."'";
	}
	
	if (count($statuses)>0) {
		foreach ($statuses as &$status){
			$status = intval($status);
		}
		$statuses_condition = "v.status IN (" . implode(", ", $statuses) . ")";
	}
	
	if (count($demographies)>0) {
		foreach ($demographies as &$demography){
			$demography = intval($demography);
		}
		$demographies_condition = "s.id IN (SELECT sg.series_id
							FROM rel_series_genre sg
							WHERE sg.genre_id IN (" . implode(", ", $demographies) . ")
						)";
	}

	$final_query = "SELECT s.*,
				dv.slug default_version_slug,
				dv.title default_version_title,
				dv.synopsis default_version_synopsis,
				GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres,
				(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.status=2) versions_in_progress
			FROM series s
				LEFT JOIN version dv ON s.default_version_id=dv.id
				LEFT JOIN version v ON s.id=v.series_id
				LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='manga'
				AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0
				AND ".get_internal_hentai_condition()."
				AND (s.name LIKE '%$query%' OR s.alternate_names LIKE '%$query%' OR EXISTS(SELECT v.id FROM version v WHERE v.series_id=s.id AND v.title LIKE '%$query%') OR s.author LIKE '%$query%' OR s.keywords LIKE '%$query%')
				AND $type_condition
				AND $statuses_condition
				AND $demographies_condition
				AND ".get_internal_included_genres_condition($genres_include)."
				AND ".get_internal_included_genres_condition($themes_include)."
				AND ".get_internal_excluded_genres_condition($genres_exclude)."
				AND ".get_internal_excluded_genres_condition($themes_exclude)."
			GROUP BY s.id
			ORDER BY dv.title ASC
			LIMIT $max_items
			OFFSET $offset";
	return query($final_query);
}

function query_get_manga_details_by_slug($slug) {
	$slug = escape($slug);
	$final_query = "SELECT s.*,
				dv.slug default_version_slug,
				dv.title default_version_title,
				dv.synopsis default_version_synopsis,
				GROUP_CONCAT(DISTINCT g.name SEPARATOR ', ') genres,
				(SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.status=2) versions_in_progress
			FROM series s
				LEFT JOIN version dv ON s.default_version_id=dv.id
				LEFT JOIN rel_series_genre sg ON s.id=sg.series_id
				LEFT JOIN genre g ON sg.genre_id = g.id
			WHERE s.type='manga'
				AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0
				AND ".get_internal_hentai_condition()."
				AND dv.slug LIKE '$slug/%'";
	return query($final_query);
}

function query_get_manga_chapters_by_slug($slug) {
	$slug = escape($slug);
	$final_query = "SELECT s.subtype,
			dv.slug default_version_slug,
			dv.title default_version_title,
			dv.synopsis default_version_synopsis,
			v.show_episode_numbers,
			fi.id,
			fi.created,
			e.number,
			IF((SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1,
				IFNULL(vd.title, d.name),
				NULL
			) division_name,
			IF(s.subtype='oneshot',
				IF(s.comic_type='novel', '".lang('catalogue.query.light_novel')."', '".lang('catalogue.query.oneshot')."'),
				IF(v.show_episode_numbers=1 AND e.number IS NOT NULL,
					CONCAT('".lang('catalogue.query.chapter')."', REPLACE(TRIM(e.number)+0, '.', ','), IF(et.title IS NULL, '', CONCAT(': ', et.title))),
					CONCAT(IFNULL(et.title, e.description))
				)
			) episode_title,
			IF(fi.episode_id IS NULL, 1, 0) is_extra,
			fi.extra_name,
			(SELECT GROUP_CONCAT(f.name SEPARATOR ', ') FROM rel_version_fansub vf LEFT JOIN fansub f ON vf.fansub_id=f.id LEFT JOIN version v2 ON vf.version_id=v2.id WHERE vf.version_id=v.id) fansubs
			FROM series s
				LEFT JOIN version dv ON s.default_version_id=dv.id
				LEFT JOIN version v ON v.series_id=s.id
				LEFT JOIN file fi ON fi.version_id=v.id
				LEFT JOIN episode_title et ON et.version_id=v.id AND et.episode_id=fi.episode_id
				LEFT JOIN episode e ON fi.episode_id=e.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version_division vd ON vd.division_id=d.id AND vd.version_id=v.id
			WHERE v.is_hidden=0
				AND s.type='manga'
				AND dv.slug LIKE '$slug/%'
				AND fi.is_lost=0
				AND (SELECT COUNT(*) FROM version v WHERE v.series_id=s.id AND v.is_hidden=0)>0
				AND ".get_internal_hentai_condition()."
			ORDER BY fi.episode_id IS NULL ASC,
				d.number DESC,
				e.number IS NULL DESC,
				e.number DESC,
				episode_title DESC,
				fi.extra_name DESC,
				fi.created DESC";
	return query($final_query);
}

function query_get_manga_chapter_pages($file_id) {
	$file_id = intval($file_id);
	$final_query = "SELECT f.*
			FROM file f
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE v.is_hidden=0
				AND ".get_internal_hentai_condition()."
				AND f.id=$file_id";
	return query($final_query);
}

function query_get_view_session_from_anon_id($file_id, $anon_id) {
	$file_id = intval($file_id);
	$anon_id = escape($anon_id);
	$final_query = "SELECT *
			FROM view_session
			WHERE file_id=$file_id
				AND anon_id='$anon_id'";
	return query($final_query);
}

function query_get_version_by_forum_topic_id($forum_topic_id) {
	$forum_topic_id = intval($forum_topic_id);
	$final_query = "SELECT *
			FROM version
			WHERE forum_topic_id=$forum_topic_id";
	return query($final_query);
}

function query_get_user_by_forum_user_id($forum_user_id) {
	$forum_user_id = intval($forum_user_id);
	$final_query = "SELECT *
			FROM user
			WHERE forum_user_id=$forum_user_id";
	return query($final_query);
}

function query_get_comment_by_forum_post_id($forum_post_id) {
	$forum_post_id = intval($forum_post_id);
	$final_query = "SELECT *
			FROM comment
			WHERE forum_post_id=$forum_post_id";
	return query($final_query);
}

function query_insert_comment_with_forum_post_id($user_id, $fansub_id, $version_id, $forum_post_id, $text, $has_spoilers) {
	$version_id = intval($version_id);
	$forum_post_id = intval($forum_post_id);
	$text = escape($text);
	$has_spoilers = intval($has_spoilers);
	
	if (!empty($fansub_id)) {
		$user_id = 'NULL';
		$type = 'fansub';
		$fansub_id = intval($fansub_id);
		$subquery = "NULL";
	} else {
		$user_id = intval($user_id);
		$type = 'user';
		$fansub_id = 'NULL';
		$subquery = "(SELECT last_seen_episode_id FROM user_version_followed WHERE user_id=$user_id AND version_id=$version_id AND last_seen_episode_id<>-1)";
	}
	
	$final_query = "INSERT INTO comment
				(version_id, user_id, type, fansub_id, reply_to_comment_id, last_replied, text, last_seen_episode_id, has_spoilers, forum_post_id, created, updated)
			VALUES ($version_id, $user_id, '$type', $fansub_id, NULL, NULL, '$text', $subquery, $has_spoilers, $forum_post_id, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)";
	return query($final_query);
}

function query_update_comment_by_forum_post_id($forum_post_id, $text, $has_spoilers) {
	$forum_post_id = intval($forum_post_id);
	$has_spoilers = intval($has_spoilers);
	$text = escape($text);
	$final_query = "UPDATE comment
			SET text='$text', has_spoilers=$has_spoilers, updated=CURRENT_TIMESTAMP
			WHERE forum_post_id=$forum_post_id";
	return query($final_query);
}

function query_delete_comment_by_forum_post_id($forum_post_id) {
	$forum_post_id = intval($forum_post_id);
	$final_query = "DELETE FROM comment
			WHERE forum_post_id=$forum_post_id";
	return query($final_query);
}
?>

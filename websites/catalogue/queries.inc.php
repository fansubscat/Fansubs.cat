<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

// INTERNAL

function get_internal_hentai_condition() {
	if (SITE_IS_HENTAI) {
		return "s.rating='XXX'";
	} else {
		return "(s.rating IS NULL OR s.rating<>'XXX')";
	}
}

// SELECT

function query_total_number_of_series() {
	$final_query = "SELECT FLOOR((COUNT(*)-1)/$number)*$number cnt
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
	$series_id_escaped = escape($series_id);
	$final_query = "SELECT *
			FROM series
			WHERE id=$series_id_escaped";
	return query($final_query);
}

function query_manga_division_data_from_file_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id_escaped = escape($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				s.slug,
				IF(s.subtype='oneshot', NULL, d.number) division_number
			FROM file f
				LEFT JOIN episode e ON f.episode_id=e.id
				LEFT JOIN division d ON e.division_id=d.id
				LEFT JOIN version v ON f.version_id=v.id
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='manga'
				AND f.id=$old_piwigo_id_escaped";
	return query($final_query);
}

function query_manga_division_data_from_division_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id_escaped = escape($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				s.slug,
				IF(s.type='oneshot', NULL, d.number) division_number
			FROM division d
				LEFT JOIN series s ON d.series_id=s.id
			WHERE s.type='manga'
				AND d.id=$old_piwigo_id_escaped";
	return query($final_query);
}

function query_manga_series_data_from_series_with_old_piwigo_id($old_piwigo_id) {
	$old_piwigo_id_escaped = escape($old_piwigo_id);
	$final_query = "SELECT s.subtype,
				s.slug
			FROM series s
			WHERE s.type='manga'
				AND s.id=$old_piwigo_id_escaped";
	return query($final_query);
}

function query_series_data_for_preview_image_by_slug($slug) {
	$slug_escaped = escape($slug);
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
			WHERE s.type='".CATALOGUE_ITEM_TYPE."' AND slug='$slug_escaped'
			GROUP BY s.id";
	return query($final_query);
}

function query_version_data_for_preview_image_by_series_id($series_id) {
	$series_id_escaped = escape($series_id);
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
				AND v.series_id=$series_id_escaped
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

function query_most_popular_series_from_date($since_date) {
	$since_date_escaped = escape($since_date);
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
						AND vi.day>='$since_date_escaped'
					GROUP BY f.version_id, f.episode_id
					) a
				GROUP BY a.episode_id
				) b
			GROUP BY b.series_id
			ORDER BY max_views DESC,
				b.series_id DESC";
	return query($final_query);
}

function query_version_ids_for_fools_day() {
	$final_query = "SELECT v.id
			FROM version v
				LEFT JOIN series s ON v.series_id=s.id
			WHERE s.type='".CATALOGUE_ITEM_TYPE."'
				AND ".get_internal_hentai_condition()."
				AND v.status IN (1,3)
				AND s.score IS NOT NULL
				AND v.is_missing_episodes=0
			ORDER BY s.score ASC
			LIMIT 10";
	return query($final_query);
}

function query_version_ids_for_sant_jordi() {
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
			LIMIT 10";
	return query($final_query);
}

function query_version_ids_for_tots_sants() {
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
				LIMIT 10";
	return query($final_query);
}

// INSERT

function query_insert_to_user_series_list($user_id, $series_id) {
	$user_id_escaped = escape($user_id);
	$series_id_escaped = escape($series_id);
	$final_query = "REPLACE INTO user_series_list (user_id, series_id)
			VALUES ($user_id_escaped, $series_id_escaped)";
	return query($final_query);
}

// DELETE

function query_delete_from_user_series_list($user_id, $series_id) {
	$user_id_escaped = escape($user_id);
	$series_id_escaped = escape($series_id);
	$final_query = "DELETE FROM user_series_list
			WHERE user_id=$user_id_escaped AND series_id=$series_id_escaped";
	return query($final_query);
}
?>

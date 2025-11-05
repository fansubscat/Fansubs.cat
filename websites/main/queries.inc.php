<?php
require_once(__DIR__.'/../common/db.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

// INTERNAL

function get_internal_blacklisted_fansubs_condition_main($user) {
	if (!empty($user)) {
		$blacklisted_fansubs_condition = "f.id IN (
							SELECT ufbl.fansub_id
							FROM user_fansub_blacklist ufbl
							WHERE ufbl.user_id=".intval($user['id'])."
							)";
	} else {
		$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids(); //Already escaped
		if (count($cookie_blacklisted_fansub_ids)>0) {
			$blacklisted_fansubs_condition = "f.id IN (".implode(',',$cookie_blacklisted_fansub_ids).")";
		} else {
			$blacklisted_fansubs_condition = "0";
		}
	}
	return $blacklisted_fansubs_condition;
}

// SELECT

function query_fansubs($user, $status) {
	$status = intval($status);
	$final_query = "SELECT *
			FROM (SELECT f.*,
					IF(".get_internal_blacklisted_fansubs_condition_main($user).", 1, 0) is_blacklisted,
					(SELECT COUNT(DISTINCT v.series_id)
						FROM rel_version_fansub vf
						LEFT JOIN version v ON vf.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						WHERE vf.fansub_id=f.id
							AND v.is_hidden=0
							AND s.type='anime'
							AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
					) total_anime,
					(SELECT COUNT(DISTINCT v.series_id)
						FROM rel_version_fansub vf
						LEFT JOIN version v ON vf.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						WHERE vf.fansub_id=f.id
							AND v.is_hidden=0
							AND s.type='manga'
							AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
					) total_manga,
					(SELECT COUNT(DISTINCT v.series_id)
						FROM rel_version_fansub vf
						LEFT JOIN version v ON vf.version_id=v.id
						LEFT JOIN series s ON v.series_id=s.id
						WHERE vf.fansub_id=f.id
							AND v.is_hidden=0
							AND s.type='liveaction'
							AND s.rating".(SITE_IS_HENTAI ? '=' : '<>')."'XXX'
					) total_liveaction,
					(SELECT COUNT(*)
						FROM news n
						WHERE n.fansub_id=f.id
							AND ".(SITE_IS_HENTAI ? "(f.hentai_category=2 OR (f.hentai_category=1 AND (n.title LIKE '%hentai%' OR n.contents LIKE '%hentai%' OR n.title LIKE '%yaoi%' OR n.contents LIKE '%yaoi%' OR n.title LIKE '%yuri%' OR n.contents LIKE '%yuri%')))" : "(f.hentai_category=0 OR (f.hentai_category=1 AND n.title NOT LIKE '%hentai%' AND n.contents NOT LIKE '%hentai%' AND n.title NOT LIKE '%yaoi%' AND n.contents NOT LIKE '%yaoi%' AND n.title NOT LIKE '%yuri%' AND n.contents NOT LIKE '%yuri%'))")."
					) total_news
				FROM fansub f
				WHERE f.status=$status
			) sq
			WHERE (total_anime>0 OR total_manga>0 OR total_liveaction>0 OR total_news>0)
			ORDER BY sq.name ASC";
	return query($final_query);
}

function query_external_links_by_category($category) {
	$category = escape($category);
	$final_query = "SELECT *
			FROM external_link l
			WHERE l.category='$category'
			ORDER BY l.name ASC";
	return query($final_query);
}
?>

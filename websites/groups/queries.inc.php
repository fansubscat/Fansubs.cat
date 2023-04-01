<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

// INTERNAL

function get_internal_blacklisted_fansubs_condition($user) {
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
			$blacklisted_fansubs_condition = "1";
		}
	}
	return $blacklisted_fansubs_condition;
}

// SELECT

function query_fansubs($user, $status) {
	$status = intval($status);
	$final_query = "SELECT f.*,
				IF(".get_internal_blacklisted_fansubs_condition($user).",TRUE,FALSE) is_blacklisted,
				(SELECT COUNT(DISTINCT v.series_id)
					FROM rel_version_fansub vf
					LEFT JOIN version v ON vf.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
					WHERE vf.fansub_id=f.id
						AND v.is_hidden=0
						AND s.type='anime'
				) total_anime,
				(SELECT COUNT(DISTINCT v.series_id)
					FROM rel_version_fansub vf
					LEFT JOIN version v ON vf.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
					WHERE vf.fansub_id=f.id
						AND v.is_hidden=0
						AND s.type='manga'
				) total_manga,
				(SELECT COUNT(DISTINCT v.series_id)
					FROM rel_version_fansub vf
					LEFT JOIN version v ON vf.version_id=v.id
					LEFT JOIN series s ON v.series_id=s.id
					WHERE vf.fansub_id=f.id
						AND v.is_hidden=0
						AND s.type='liveaction'
				) total_liveaction,
				(SELECT COUNT(*)
					FROM news n
					WHERE n.fansub_id=f.id
				) total_news
			FROM fansub f
			WHERE f.status=$status
			ORDER BY f.name ASC";
	return query($final_query);
}
?>

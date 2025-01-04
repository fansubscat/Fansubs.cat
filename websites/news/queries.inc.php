<?php
require_once(__DIR__.'/../common/db.inc.php');
require_once(__DIR__.'/../common/common.inc.php');

// INTERNAL

function get_internal_blacklisted_fansubs_condition_news($user) {
	if (!empty($user)) {
		$blacklisted_fansubs_condition = "n.fansub_id NOT IN (
							SELECT ufbl.fansub_id
							FROM user_fansub_blacklist ufbl
							WHERE ufbl.user_id=".intval($user['id'])."
							)";
	} else {
		$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids(); //Already escaped
		if (count($cookie_blacklisted_fansub_ids)>0) {
			$blacklisted_fansubs_condition = "n.fansub_id NOT IN (".implode(',',$cookie_blacklisted_fansub_ids).")";
		} else {
			$blacklisted_fansubs_condition = "1";
		}
	}
	return $blacklisted_fansubs_condition;
}

// SELECT

function query_latest_news($user, $text, $page, $page_size, $fansub_slug, $show_blacklisted_fansubs, $show_own_news, $show_only_own_news, $min_month, $max_month) {
	//We assume that everything except $text needs no escaping
	//Page starts at 1, not 0
	$text = escape_for_like($text);
	$min_month = escape($min_month);
	$max_month = escape($max_month);
	$fansub_slug = escape($fansub_slug);

	$final_query = "SELECT n.*, f.name fansub_name, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.url fansub_url, f.archive_url
			FROM news n
				LEFT JOIN fansub f ON n.fansub_id=f.id
			WHERE ".($text!==NULL ? "(n.title LIKE '%$text%' OR n.contents LIKE '%$text%')" : "1");
	if ($show_only_own_news) {
		$final_query .= "
				AND n.fansub_id IS NULL";
	} else if ($show_own_news) {
		$final_query .= "
				AND (".($show_blacklisted_fansubs ? "1" : get_internal_blacklisted_fansubs_condition_news($user))." OR n.fansub_id IS NULL)";
	} else {
		$final_query .= "
				AND ".($show_blacklisted_fansubs ? "1" : get_internal_blacklisted_fansubs_condition_news($user));
	}
	$final_query .= "
				AND ".(!empty($fansub_slug) ? "f.slug='$fansub_slug'" : "1")."
				AND n.date>='$min_month-01 00:00:00' AND n.date<='$max_month-31 23:59:59'
				AND ".(SITE_IS_HENTAI ? "(n.fansub_id IS NULL OR f.hentai_category=2 OR (f.hentai_category=1 AND (n.title LIKE '%hentai%' OR n.contents LIKE '%hentai%' OR n.title LIKE '%yaoi%' OR n.contents LIKE '%yaoi%' OR n.title LIKE '%yuri%' OR n.contents LIKE '%yuri%')))" : "(n.fansub_id IS NULL OR (f.hentai_category=0 OR (f.hentai_category=1 AND n.title NOT LIKE '%hentai%' AND n.contents NOT LIKE '%hentai%' AND n.title NOT LIKE '%yaoi%' AND n.contents NOT LIKE '%yaoi%' AND n.title NOT LIKE '%yuri%' AND n.contents NOT LIKE '%yuri%')))")."
			ORDER BY n.date DESC
			LIMIT $page_size
			OFFSET ".($page-1)*20;
	return query($final_query);
}

function query_all_fansubs_with_news($user) {
	$final_query = "SELECT DISTINCT f.*
			FROM news n
				LEFT JOIN fansub f ON n.fansub_id=f.id
			WHERE n.fansub_id IS NOT NULL
				AND ".get_internal_blacklisted_fansubs_condition_news($user)."
				AND ".(SITE_IS_HENTAI ? "(f.hentai_category=2 OR (f.hentai_category=1 AND (n.title LIKE '%hentai%' OR n.contents LIKE '%hentai%' OR n.title LIKE '%yaoi%' OR n.contents LIKE '%yaoi%' OR n.title LIKE '%yuri%' OR n.contents LIKE '%yuri%')))" : "(n.fansub_id IS NULL OR (f.hentai_category=0 OR (f.hentai_category=1 AND n.title NOT LIKE '%hentai%' AND n.contents NOT LIKE '%hentai%' AND n.title NOT LIKE '%yaoi%' AND n.contents NOT LIKE '%yaoi%' AND n.title NOT LIKE '%yuri%' AND n.contents NOT LIKE '%yuri%')))")."
			ORDER BY f.name ASC";
	return query($final_query);
}
?>

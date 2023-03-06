<?php
require_once("../common.fansubs.cat/db.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

// SELECT

function query_latest_news($user, $search_query, $page, $page_size) {
	//We assume that everything except $search_query needs no escaping
	//Page starts at 1, not 0
	if (!empty($user)) {
		$blacklist_condition="f.id NOT IN (SELECT ufbl.fansub_id FROM user_fansub_blacklist ufbl WHERE ufbl.user_id=${user['id']})";
	} else {
		$cookie_blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
		if (count($cookie_blacklisted_fansub_ids)>0) {
			$blacklist_condition="f.id NOT IN (".implode(',',$cookie_blacklisted_fansub_ids).")";
		} else {
			$blacklist_condition="1";
		}
	}

	if ($search_query!==NULL) {
		$search_condition = "(n.title LIKE '%".escape($search_query)."%' OR n.contents LIKE '%".escape($search_query)."%')";
	}
	else{
		$search_condition = "1";
	}

	$final_query = "SELECT n.*, f.name fansub_name, IFNULL(f.slug,'fansubs-cat') fansub_slug, f.url fansub_url, f.archive_url
			FROM news n
				LEFT JOIN fansub f ON n.fansub_id=f.id
			WHERE $blacklist_condition AND $search_condition
			ORDER BY n.date DESC
			LIMIT $page_size
			OFFSET ".($page-1)*20;
	return query($final_query);
}
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

function save_settings(){
	global $user;
	//Check if we have all the data
	if (empty($user)) {
		http_response_code(400);
		return array('result' => 'ko', 'code' => 1);
	}

	//Transfer to variables
	if (!empty($_POST['only_read_behavior'])) {
		//Only setting read behavior: get all data from the user
		$show_cancelled_projects = $user['show_cancelled_projects'];
		$show_lost_projects = $user['show_lost_projects'];
		$hide_hentai_access = $user['hide_hentai_access'];
		$previous_chapters_read_behavior = ($_POST['previous_chapters_read_behavior']==1 ? 1 : 2);
		$manga_reader_type = $user['manga_reader_type'];
		$blacklisted_fansub_ids = $user['blacklisted_fansub_ids'];
	} else {
		$show_cancelled_projects = ($_POST['show_cancelled_projects']==1 ? 1 : 0);
		$show_lost_projects = ($_POST['show_lost_projects']==1 ? 1 : 0);
		$hide_hentai_access = ($_POST['hide_hentai_access']==1 ? 1 : 0);
		$previous_chapters_read_behavior = ($_POST['previous_chapters_read_behavior']==1 ? 1 : 2);
		$manga_reader_type = ($_POST['manga_reader_type']>=0 && $_POST['manga_reader_type']<=2 ? $_POST['manga_reader_type'] : 0);
		$blacklisted_fansub_ids = !empty($_POST['blacklisted_fansub_ids']) ? explode(',',$_POST['blacklisted_fansub_ids']) : array();
	}

	//Update DB
	query_update_user_settings($user['id'], $show_cancelled_projects, $show_lost_projects, $hide_hentai_access, $manga_reader_type, $previous_chapters_read_behavior);
	query_delete_user_blacklist($user['id']);
	foreach ($blacklisted_fansub_ids as $blacklisted_fansub_id) {
		query_insert_user_blacklist($user['id'], intval($blacklisted_fansub_id));
	}

	return array('result' => 'ok');
}

echo json_encode(save_settings());
?>

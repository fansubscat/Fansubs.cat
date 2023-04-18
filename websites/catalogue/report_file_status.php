<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("common.inc.php");
require_once("queries.inc.php");

function report_file_status(){
	global $user;

	if (!empty($_POST['view_id'])) {
		$result = query_view_session_by_id($_POST['view_id']);
		if ($row = mysqli_fetch_assoc($result)) {
			$is_casted = $row['is_casted'];
			$progress = $_POST['progress'];
			if (!empty($_POST['is_casted'])) {
				$is_casted = 1;
				$progress = 0; //We can not determine it reliably
			}
			query_update_view_session_progress($_POST['view_id'], $progress, $is_casted, $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);

			if (CATALOGUE_ITEM_TYPE=='manga') {
				//3 pages or 5%, whatever is shorter: minimum of 1 page
				$min_progress = max(1,min(intval($row['length']*0.05), 3));
			} else {
				//1 minute or 5%, whatever is shorter: minimum of 1 second
				$min_progress = max(1,min(intval($row['length']*0.05), 60));
			}

			if (!empty($user) && $progress>=$min_progress) {
				query_insert_or_update_user_position_for_file_id($user['id'], $row['file_id'], $_POST['position']);
			}
		}
	}

	return array('result' => 'ok');
}

echo json_encode(report_file_status());
?>

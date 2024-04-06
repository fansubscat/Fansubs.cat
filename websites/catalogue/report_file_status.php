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
				$progress = $row['length']; //We can not determine it reliably, assume it's fully seen
			}
			query_update_view_session_progress($_POST['view_id'], $progress, $is_casted, get_view_source_type($_SERVER['HTTP_USER_AGENT'], $is_casted), $_SERVER['REMOTE_ADDR'], $_SERVER['HTTP_USER_AGENT']);

			if (!empty($user)) {
				//This adds the file to the user "currently watching" section. Allows peeking at the file a bit without it counting:
				if ($row['type']=='manga') {
					//3 pages or 5%, whatever is shorter: minimum of 2 pages (1 if length is 1)
					$min_progress = max(min(2,$row['length']),min(intval($row['length']*0.05), 3));
				} else {
					//1 minute or 5%, whatever is shorter: minimum of 10 seconds
					$min_progress = max(10,min(intval($row['length']*0.05), 60));
				}
				if ($progress>=$min_progress) {
					query_insert_or_update_user_version_followed_by_file_id($user['id'], $row['file_id']);
					query_insert_or_update_user_position_for_file_id($user['id'], $row['file_id'], $_POST['position']);
				}
			}

			//Now we count it as viewed, if applicable
			//80% of the file length: minimum of 1 page or 1 second
			$completed_progress = max(1,intval($row['length']*0.8));
			if ($progress>=$completed_progress && empty($row['view_counted'])) {
				query_update_view_session_view_counted($row['id']);
				//We check the number of affected rows to avoid concurrency issues that would cause a single view_session to be counted twice
				if (get_previous_query_num_affected_rows()>0) {
					query_save_view($row['file_id'], $row['type'], explode(' ', $row['updated'])[0], $row['length']);
				}
			}
		}
	}

	return array('result' => 'ok');
}

echo json_encode(report_file_status());
?>

<?php
require_once(__DIR__.'/queries.inc.php');

function create_shared_play_session(){
	if (!empty($_POST['view_id']) && !empty($_POST['state']) && isset($_POST['position'])) {
		$result = query_view_session_by_id($_POST['view_id']);
		if ($row = mysqli_fetch_assoc($result)) {
			$session_id = get_nanoid(8, '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');
			query_insert_shared_play_session($session_id, $row['file_id'], $_POST['position'], $row['length'], $_POST['state']);
			query_update_view_session_shared_play_session_id($_POST['view_id'], $session_id);
			$data = array(
				'session_id' => $session_id,
			);
			return array('result' => 'ok', 'data' => $data);
		}
	}

	return array('result' => 'ko');
}

echo json_encode(create_shared_play_session());
?>

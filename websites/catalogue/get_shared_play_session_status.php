<?php
require_once(__DIR__.'/queries.inc.php');

function get_shared_play_session_status(){
	if (!empty($_GET['session_id']) && !empty($_GET['file_id'])) {
		$result = query_shared_play_session_by_id($_GET['session_id']);
		if ($row = mysqli_fetch_assoc($result)) {
			if ($row['file_id']!=$_GET['file_id']) {
				return array('result' => 'ko', 'code' => 1);
			}
			//calculate from last update vs current time if playing, assume stopped if paused
			if ($row['state']=='playing') {
				$position = $row['position']+(date('U')-$row['updated_timestamp']);
			} else {
				$position = $row['position'];
			}
			
			if ($position > $row['length']) {
				$position = $row['length'];
				
			}
			$data = array(
				'position' => $position,
				'state' => $row['state'],
			);
			return array('result' => 'ok', 'data' => $data);
		}
	}

	return array('result' => 'ko', 'code' => 2);
}

echo json_encode(get_shared_play_session_status());
?>

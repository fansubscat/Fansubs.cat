<?php
ob_start();
require_once("db.inc.php");

$link_id = (!empty($_GET['link_id']) ? intval($_GET['link_id']) : 0);
$play_id = (!empty($_GET['play_id']) ? escape($_GET['play_id']) : '');

if ($link_id>0 && !empty($_GET['action']) && !empty($play_id)) {
	$result = query("SELECT l.*, e.duration FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id");
	if ($row = mysqli_fetch_assoc($result)) {
		switch($_GET['action']) {
			case 'close':
				$exists = query("SELECT * FROM play_session WHERE play_id='$play_id'");
				if (mysqli_fetch_assoc($exists)){
					//We set a minimum and maximum play time.
					//If the episode has a duration of X, to be counted as a real view, it must be inside this range:
					//	Minimum: X/2 (values lower are not counted as real views)
					//	Maximum: X (values higher are truncated to X)
					//If the episode DOES NOT have a duration defined, to be counted as a real view, it must be inside this range:
					//In real life, this should only happen for extra content links (usually waaay shorter than 10 minutes).
					//	Minimum: 30 seconds (values lower are not counted as real views)
					//	Maximum: 10 minutes (values higher are truncated to 10 minutes)
					$min_time = !empty($row['duration']) ? ($row['duration']*60/2) : 30;
					$max_time = !empty($row['duration']) ? ($row['duration']*60) : (10*60);

					if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent']) && $_GET['time_spent']>=$min_time) {
						if ($_GET['time_spent']>$max_time) {
							$time_spent = $max_time; // Left open for too long, adjust to max
						} else {
							$time_spent = escape($_GET['time_spent']);
						}
						query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT views+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT time_spent+$time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),$time_spent)");
						query("INSERT INTO view_log (link_id, date) VALUES ($link_id, CURRENT_TIMESTAMP)");
					}
					//Else, discard and not even report it: opened and closed in too little time (less than min)
					query("DELETE FROM play_session WHERE play_id='$play_id'");
				}
				//If it doesn't exist, just discard this request...
				break;
			case 'open':
				//Replace in the remote case we get a collision...
				query("REPLACE INTO play_session VALUES ('$play_id', $link_id, 0, CURRENT_TIMESTAMP)");
				query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0)");
				break;
			case 'notify':
				//Same logic but only for max time...
				$max_time = !empty($row['duration']) ? ($row['duration']*60) : (10*60);

				if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent'])){
					if ($_GET['time_spent']>$max_time) {
						$time_spent = $max_time; // Left open for too long, adjust to max
					} else {
						$time_spent = escape($_GET['time_spent']);
					}
					query("UPDATE play_session SET link_id=$link_id, time_spent=$time_spent, last_update=CURRENT_TIMESTAMP WHERE play_id='$play_id'");
				}
				//Else, invalid request, discard it
				break;
			default:
				break;
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>

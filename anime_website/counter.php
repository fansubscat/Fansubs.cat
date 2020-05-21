<?php
ob_start();
require_once("db.inc.php");

if (!empty($_GET['link_id']) && is_numeric($_GET['link_id']) && !empty($_GET['action'])) {
	$link_id = escape($_GET['link_id']);

	$result = query("SELECT l.*, e.duration FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id");
	if ($row = mysqli_fetch_assoc($result)) {
		if ($_GET['action']=='close') {
			//We set a minimum and maximum play time.
			//If the episode has a duration of X, to be counted as a real view, it must be inside this range:
			//	Minimum: X/2 (values lower are not counted as real views)
			//	Maximum: X (values higher are truncated to X)
			//If the episode DOES NOT have a duration defined, to be counted as a real view, it must be inside this range:
			//In real life, this should only happen for extra content links (usually waaay shorter than an hour).
			//	Minimum: 30 seconds (values lower are not counted as real views)
			//	Maximum: 1 hour (values higher are truncated to 1 hour)
			$min_time = !empty($row['duration']) ? ($row['duration']*60/2) : 30;
			$max_time = !empty($row['duration']) ? ($row['duration']*60) : (3*60*60);

			if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent']) && $_GET['time_spent']>=$min_time) {
				if ($_GET['time_spent']>$max_time) {
					$time_spent = $max_time; // Left open for too long, adjust to max
				} else {
					$time_spent = escape($_GET['time_spent']);
				}
				query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT views+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT time_spent+$time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),$time_spent)");
			}
			//Else, discard and not even report it: opened and closed in too little time (less than min)
		} else {
			query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0)");
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>

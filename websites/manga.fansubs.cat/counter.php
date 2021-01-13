<?php
ob_start();
require_once("db.inc.php");

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);
$read_id = (!empty($_GET['read_id']) ? escape($_GET['read_id']) : '');

if ($file_id>0 && !empty($_GET['action']) && !empty($read_id)) {
	$result = query("SELECT f.* FROM file f WHERE f.id=$file_id");
	if ($row = mysqli_fetch_assoc($result)) {
		switch($_GET['action']) {
			case 'close':
				$exists = query("SELECT * FROM read_session WHERE read_id='$read_id'");
				if (mysqli_fetch_assoc($exists)){
					//We set a minimum and maximum read time and pages.
					$min_time = $row['number_of_pages'] * 5;
					$max_time = $row['number_of_pages'] * 60;
					$min_pages = intval($row['number_of_pages']/2);
					$max_pages = $row['number_of_pages'];

					if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent']) && $_GET['time_spent']>=$min_time && !empty($_GET['pages_read']) && is_numeric($_GET['pages_read']) && $_GET['pages_read']>=$min_pages) {
						if ($_GET['time_spent']>$max_time) {
							$time_spent = $max_time; // Left open for too long, adjust to max
						} else {
							$time_spent = escape($_GET['time_spent']);
						}
						if ($_GET['pages_read']>$max_pages) {
							$pages_read = $max_pages; //Should not happen normally
						} else {
							$pages_read = escape($_GET['pages_read']);
						}
						query("REPLACE INTO manga_views SELECT $file_id, '".date('Y-m-d')."', IFNULL((SELECT clicks FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT views+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT time_spent+$time_spent FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),$time_spent), IFNULL((SELECT pages_read+$pages_read FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),$pages_read)");
						query("INSERT INTO manga_view_log (file_id, date) VALUES ($file_id, CURRENT_TIMESTAMP)");
					}
					//Else, discard and not even report it: opened and closed in too little time (less than min)
					query("DELETE FROM read_session WHERE read_id='$read_id'");
				}
				//If it doesn't exist, just discard this request...
				break;
			case 'open':
				//Replace in the remote case we get a collision...
				query("REPLACE INTO read_session VALUES ('$read_id', $file_id, 0, 1, CURRENT_TIMESTAMP)");
				query("REPLACE INTO manga_views SELECT $file_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT pages_read FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0)");
				break;
			case 'notify':
				//Same logic but only for max time/pages...
				$max_time = $row['number_of_pages'] * 60;
				$max_pages = $row['number_of_pages'];

				if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent']) && !empty($_GET['pages_read']) && is_numeric($_GET['pages_read'])){
					if ($_GET['time_spent']>$max_time) {
						$time_spent = $max_time; // Left open for too long, adjust to max
					} else {
						$time_spent = escape($_GET['time_spent']);
					}
					if ($_GET['pages_read']>$max_pages) {
						$pages_read = $max_pages; //Should not happen normally
					} else {
						$pages_read = escape($_GET['pages_read']);
					}
					query("UPDATE read_session SET file_id=$file_id, time_spent=$time_spent, pages_read=$pages_read, last_update=CURRENT_TIMESTAMP WHERE read_id='$read_id'");
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

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
				$pages_read = 0;
				if (!empty($_GET['pages_read']) && is_numeric($_GET['pages_read'])){
					$pages_read = escape($_GET['pages_read']);
				}
				query("INSERT INTO read_session VALUES ('$read_id', $file_id, $pages_read, (SELECT number_of_pages total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', 1, 0, 0) ON DUPLICATE KEY UPDATE pages_read=$pages_read, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', reader_closed=1");
				break;
			case 'open':
				query("REPLACE INTO manga_views SELECT $file_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT pages_read FROM manga_views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0)");
				query("INSERT INTO read_session VALUES ('$read_id', $file_id, 0, (SELECT number_of_pages total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', 0, 0, 0) ON DUPLICATE KEY UPDATE last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."'");
				break;
			case 'notify':
				$pages_read = 0;
				if (!empty($_GET['pages_read']) && is_numeric($_GET['pages_read'])){
					$pages_read = escape($_GET['pages_read']);
				}
				query("INSERT INTO read_session VALUES ('$read_id', $file_id, $pages_read, (SELECT number_of_pages total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', 0, 0, 0) ON DUPLICATE KEY UPDATE pages_read=$pages_read, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."'");
				break;
			default:
				break;
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>

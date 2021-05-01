<?php
ob_start();
require_once("db.inc.php");

$link_id = (!empty($_GET['link_id']) ? intval($_GET['link_id']) : 0);
$play_id = (!empty($_GET['play_id']) ? escape($_GET['play_id']) : '');
$method = (!empty($_GET['method']) ? $_GET['method'] : 'time');

if ($link_id>0 && !empty($_GET['action']) && !empty($play_id)) {
	$result = query("SELECT l.*, e.duration FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id");
	if ($row = mysqli_fetch_assoc($result)) {
		switch($_GET['action']) {
			case 'close':
				$time_spent = 0;
				if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent'])){
					$time_spent = escape($_GET['time_spent']);
				}
				query("INSERT INTO play_session VALUES ('$play_id', '".($method=='storage' ? 'size' : 'time')."', $link_id, $time_spent, (SELECT IFNULL((SELECT IF(e.id IS NULL,60,e.duration*60) total_time FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id),0)), 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 1, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', time_spent=$time_spent, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', player_closed=1, log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				break;
			case 'open':
				query("REPLACE INTO views SELECT $link_id, '".date('Y-m-d')."', IFNULL((SELECT clicks+1 FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM views WHERE link_id=$link_id AND day='".date('Y-m-d')."'),0)");
				query("INSERT INTO play_session VALUES ('$play_id', '".($method=='storage' ? 'size' : 'time')."', $link_id, 0, (SELECT IFNULL((SELECT IF(e.id IS NULL,60,e.duration*60) total_time FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id),0)), 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, NULL) ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."'");
				break;
			case 'notify':
				$time_spent = 0;
				if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent'])){
					$time_spent = escape($_GET['time_spent']);
				}
				query("INSERT INTO play_session VALUES ('$play_id', '".($method=='storage' ? 'size' : 'time')."', $link_id, $time_spent, (SELECT IFNULL((SELECT IF(e.id IS NULL,60,e.duration*60) total_time FROM link l LEFT JOIN episode e ON l.episode_id=e.id WHERE l.id=$link_id),0)), 0, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', time_spent=$time_spent, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				break;
			default:
				break;
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>

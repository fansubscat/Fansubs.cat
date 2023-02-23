<?php
ob_start();
require_once("../common.fansubs.cat/user_init.inc.php");

$file_id = (!empty($_GET['file_id']) ? intval($_GET['file_id']) : 0);
$view_id = (!empty($_GET['view_id']) ? escape($_GET['view_id']) : '');
$method = (!empty($_GET['method']) ? $_GET['method'] : 'time');

if ($file_id>0 && !empty($_GET['action']) && !empty($view_id)) {
	$result = query("SELECT f.* FROM file f WHERE f.id=$file_id");
	if ($row = mysqli_fetch_assoc($result)) {
		switch($_GET['action']) {
			case 'close':
				if ($cat_config['items_type']=='manga') {
					$pages_read = "NULL";
					if (!empty($_GET['pages_read']) && is_numeric($_GET['pages_read'])){
						$pages_read = escape($_GET['pages_read']);
					}
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', 'pages', $file_id, NULL, NULL, NULL, NULL, $pages_read, (SELECT length total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 1, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='pages',pages_read=$pages_read, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', is_viewer_closed=1, log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				} else {
					$time_spent = 0;
					if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent'])){
						$time_spent = escape($_GET['time_spent']);
					}
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', '".($method=='storage' ? 'size' : 'time')."', $file_id, $time_spent, (SELECT f.length total_time FROM file f WHERE f.id=$file_id), NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 1, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', time_spent=$time_spent, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', is_viewer_closed=1, log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				}
				break;
			case 'open':
				if ($cat_config['items_type']=='manga') {
					query("REPLACE INTO views SELECT $file_id, '".date('Y-m-d')."', '${cat_config['items_type']}', IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), NULL, IFNULL((SELECT pages_read FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0)");
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', 'pages', $file_id, NULL, NULL, NULL, NULL, 0, (SELECT length total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, '') ON DUPLICATE KEY UPDATE method='pages',last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."'");
				} else {
					query("REPLACE INTO views SELECT $file_id, '".date('Y-m-d')."', '${cat_config['items_type']}', IFNULL((SELECT clicks+1 FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),1), IFNULL((SELECT views FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), IFNULL((SELECT time_spent FROM views WHERE file_id=$file_id AND day='".date('Y-m-d')."'),0), NULL");
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', '".($method=='storage' ? 'size' : 'time')."', $file_id, 0, (SELECT f.length total_time FROM file f WHERE f.id=$file_id), NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, '') ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."'");
				}
				break;
			case 'notify':
				if ($cat_config['items_type']=='manga') {
					$pages_read = "NULL";
					if (!empty($_GET['pages_read']) && is_numeric($_GET['pages_read'])){
						$pages_read = escape($_GET['pages_read']);
					}
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', 'pages', $file_id, NULL, NULL, NULL, NULL, $pages_read, (SELECT length total_pages FROM file WHERE id=$file_id), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='pages',pages_read=$pages_read, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				} else {
					$time_spent = 0;
					if (!empty($_GET['time_spent']) && is_numeric($_GET['time_spent'])){
						$time_spent = escape($_GET['time_spent']);
					}
					query("INSERT INTO view_session (id, method, file_id, time_spent, total_time, bytes_read, total_bytes, pages_read, total_pages, created, last_update, ip, user_agent, user_agent_read, is_viewer_closed, is_view_counted, is_archived, log) VALUES ('$view_id', '".($method=='storage' ? 'size' : 'time')."', $file_id, $time_spent, (SELECT f.length total_time FROM file f WHERE f.id=$file_id), NULL, NULL, NULL, NULL, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '".escape($_SERVER['REMOTE_ADDR'])."', '".escape($_SERVER['HTTP_USER_AGENT'])."', NULL, 0, 0, 0, '".(!empty($_POST['log']) ? escape($_POST['log']) : '')."') ON DUPLICATE KEY UPDATE method='".($method=='storage' ? 'size' : 'time')."', time_spent=$time_spent, last_update=CURRENT_TIMESTAMP, ip='".escape($_SERVER['REMOTE_ADDR'])."', user_agent='".escape($_SERVER['HTTP_USER_AGENT'])."', log='".(!empty($_POST['log']) ? escape($_POST['log']) : '')."'");
				}
				break;
			default:
				break;
		}
	}
}

ob_flush();
mysqli_close($db_connection);
?>

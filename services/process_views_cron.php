<?php
require_once('db.inc.php');

$result = query("SELECT ps.play_id, ps.link_id, ps.method, ps.time_spent, ps.total_time, ps.bytes_read, ps.total_bytes, UNIX_TIMESTAMP(ps.created) created, UNIX_TIMESTAMP(ps.last_update) last_update, ps.last_update last_update_date, ps.ip, IF(ps.user_agent_read<>0 AND ps.user_agent_read IS NOT NULL, ps.user_agent_read, ps.user_agent) user_agent, ps.view_counted FROM play_session ps WHERE archived=0 AND IF(ps.method='time',ps.total_time>0,ps.total_bytes>0)");

$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	if ($row['view_counted']==0) {
		if ($row['method']=='time') {
			$valid_view = ($row['time_spent']/$row['total_time']*100)>50;
		} else { //size
			$valid_view = ($row['bytes_read']/$row['total_bytes']*100)>65;
		}
		if ($valid_view) {
			query("UPDATE play_session SET view_counted=1 WHERE play_id='".escape($row['play_id'])."'");
			query("REPLACE INTO views SELECT ".$row['link_id'].", '".date('Y-m-d', $row['last_update'])."', IFNULL((SELECT clicks FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),0), IFNULL((SELECT views+1 FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),1), IFNULL((SELECT time_spent+".$row['total_time']." FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),".$row['total_time'].")");
			query("INSERT INTO view_log (link_id, date, ip, user_agent) VALUES (".$row['link_id'].", '".$row['last_update_date']."', '".escape($row['ip'])."', '".escape($row['user_agent'])."')");
			$count++;
		}
	}
	if ($row['created']<(date('U')-3600*24*3)){ //3 days
		query("UPDATE play_session SET archived=1 WHERE play_id='".escape($row['play_id'])."'");
	}
}
mysqli_free_result($result);

$result = query("SELECT rs.read_id, rs.file_id, rs.time_spent, rs.pages_read, UNIX_TIMESTAMP(rs.last_update) last_update, rs.last_update last_update_date,fi.number_of_pages, rs.ip, rs.user_agent FROM read_session rs LEFT JOIN file fi ON rs.file_id=fi.id WHERE last_update<=DATE_SUB(NOW(), INTERVAL 2 HOUR)");

$countr = 0;
while ($row = mysqli_fetch_assoc($result)) {
	//Treat
	// -- ADAPTATION FROM counter.php close case --
	$min_time = $row['number_of_pages']*3;
	$min_pages = intval(round($row['number_of_pages']/2));

	if (!empty($row['time_spent']) && is_numeric($row['time_spent']) && $row['time_spent']>=$min_time && $row['pages_read']>=$min_pages) {
		//No need to check max as it's already done in counter.php
		query("REPLACE INTO manga_views SELECT ".$row['file_id'].", '".date('Y-m-d', $row['last_update'])."', IFNULL((SELECT clicks FROM manga_views WHERE file_id=".$row['file_id']." AND day='".date('Y-m-d', $row['last_update'])."'),0), IFNULL((SELECT views+1 FROM manga_views WHERE file_id=".$row['file_id']." AND day='".date('Y-m-d', $row['last_update'])."'),1), IFNULL((SELECT time_spent+".$row['time_spent']." FROM manga_views WHERE file_id=".$row['file_id']." AND day='".date('Y-m-d', $row['last_update'])."'),".$row['time_spent']."), IFNULL((SELECT pages_read+".$row['pages_read']." FROM manga_views WHERE file_id=".$row['file_id']." AND day='".date('Y-m-d', $row['last_update'])."'),".$row['pages_read']."), IFNULL((SELECT api_views FROM manga_views WHERE file_id=".$row['file_id']." AND day='".date('Y-m-d', $row['last_update'])."'),0)");
		query("INSERT INTO manga_view_log (file_id, date, ip, user_agent) VALUES (".$row['file_id'].", '".$row['last_update_date']."', '".escape($row['ip'])."', '".escape($row['user_agent'])."')");
		$countr++;
	}
	//Else, discard and not even report it: opened and closed in too little time (less than min)
	// -- END ADAPTATION FROM counter.php close case --
	query("DELETE FROM read_session WHERE read_id='".escape($row['read_id'])."'");
}
mysqli_free_result($result);

echo "All done!\n";
?>

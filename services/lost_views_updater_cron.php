<?php
require_once('db.inc.php');

log_action('lost-views-updater-started', "S'ha iniciat la importació de visualitzacions i lectures perdudes");

$result = query("SELECT ps.play_id, ps.link_id, ps.time_spent, UNIX_TIMESTAMP(ps.last_update) last_update, ps.last_update last_update_date,e.duration, ps.ip, ps.user_agent FROM play_session ps LEFT JOIN link l ON ps.link_id=l.id LEFT JOIN episode e ON l.episode_id=e.id WHERE last_update<=DATE_SUB(NOW(), INTERVAL 2 HOUR)");

$count = 0;
while ($row = mysqli_fetch_assoc($result)) {
	//Treat
	// -- ADAPTATION FROM counter.php close case --
	$min_time = !empty($row['duration']) ? ($row['duration']*60/2) : 30;

	if (!empty($row['time_spent']) && is_numeric($row['time_spent']) && $row['time_spent']>=$min_time) {
		//No need to check max as it's already done in counter.php
		query("REPLACE INTO views SELECT ".$row['link_id'].", '".date('Y-m-d', $row['last_update'])."', IFNULL((SELECT clicks FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),0), IFNULL((SELECT views+1 FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),1), IFNULL((SELECT time_spent+".$row['time_spent']." FROM views WHERE link_id=".$row['link_id']." AND day='".date('Y-m-d', $row['last_update'])."'),".$row['time_spent'].")");
		query("INSERT INTO view_log (link_id, date, ip, user_agent) VALUES (".$row['link_id'].", '".$row['last_update_date']."', '".escape($row['ip'])."', '".escape($row['user_agent'])."')");
		$count++;
	}
	//Else, discard and not even report it: opened and closed in too little time (less than min)
	// -- END ADAPTATION FROM counter.php close case --
	query("DELETE FROM play_session WHERE play_id='".escape($row['play_id'])."'");
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

log_action('lost-views-updater-finished', "S'ha completat la importació de visualitzacions i lectures perdudes (recuperades: $count visualitzacions, $countr lectures)");

echo "All done!\n";
?>

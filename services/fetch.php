<?php
require_once('db.inc.php');
require_once('functions.inc.php');

if (isset($argv) && isset($argv[1])){
	$fansub_to_fetch = $argv[1];
}
if (isset($argv) && isset($argv[2])){
	$force = $argv[2];
}

$lock_pointer = fopen($lock_file, "w+");

//We acquire a file lock to prevent two invocations at the same time.
//This could happen if a fansub requests a refresh via token just when a new periodic refresh is starting
if (flock($lock_pointer, LOCK_EX)) {
	$result = mysqli_query($db_connection, "SELECT f.*, (SELECT MAX(date) FROM news n WHERE n.fetcher_id=f.id) last_fetched_item_date FROM fetchers f LEFT JOIN fansubs fa ON f.fansub_id=fa.id WHERE ".(isset($fansub_to_fetch) ? "fansub_id='$fansub_to_fetch'":"f.fetch_type='periodic'")." ORDER BY fetch_type DESC, fa.name ASC, f.url ASC") or die(mysqli_error($db_connection));
	while ($row = mysqli_fetch_assoc($result)){
		echo "Fetching fetcher ".$row['fansub_id']."/".$row['id']."\n";
		fetch_fansub_fetcher($db_connection, $row['fansub_id'],$row['id'],$row['method'],$row['url'],(((isset($force) && $force) || $row['last_fetched_item_date']==NULL) ? '2000-01-01 00:00:00' : $row['last_fetched_item_date']));
		sleep(2);
	}

	mysqli_free_result($result);
	flock($lock_pointer, LOCK_UN);
} else {
	die("Couldn't get the lock!");
}

fclose($lock_pointer);

mysqli_close($db_connection);
?>

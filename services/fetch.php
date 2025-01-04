<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/functions.inc.php');

if (isset($argv) && isset($argv[1])){
	$fansub_to_fetch = $argv[1];
}
if (isset($argv) && isset($argv[2])){
	$force = $argv[2];
}

$lock_pointer = fopen(LOCK_FILE, "w+");

//We acquire a file lock to prevent two invocations at the same time.
//This could happen if a fansub requests a refresh via token just when a new periodic refresh is starting
if (flock($lock_pointer, LOCK_EX)) {
	log_action('fetch-news-started', 'S’ha iniciat l’obtenció de notícies');
	$result = query("SELECT f.*, fa.id fansub_id, fa.slug fansub_slug, (SELECT MAX(date) FROM news n WHERE n.news_fetcher_id=f.id) last_fetched_item_date FROM news_fetcher f LEFT JOIN fansub fa ON f.fansub_id=fa.id WHERE ".(isset($fansub_to_fetch) ? "fa.slug='$fansub_to_fetch' AND f.fetch_type NOT LIKE 'onetime%'":"f.fetch_type='periodic' OR (f.fetch_type LIKE 'onetime%' AND f.last_fetch_date IS NULL)")." ORDER BY fetch_type DESC, fa.name ASC, f.url ASC");
	while ($row = mysqli_fetch_assoc($result)){
		echo "Fetching fetcher ".$row['fansub_slug']."/".$row['id']."\n";
		fetch_fansub_fetcher($row['fansub_id'], $row['fansub_slug'], $row['id'], $row['method'], $row['url'], (((isset($force) && $force) || $row['last_fetched_item_date']==NULL) ? '2000-01-01 00:00:00' : $row['last_fetched_item_date']));
		sleep(2);
	}
	log_action('fetch-news-finished', 'S’ha completat l’obtenció de notícies');
	flock($lock_pointer, LOCK_UN);
} else {
	die("Couldn't get the lock!");
}

fclose($lock_pointer);
?>

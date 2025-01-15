<?php
require_once(__DIR__.'/db.inc.php');

define('DRY_RUN', TRUE);

$result = query("SELECT * FROM series WHERE type<>'liveaction' AND external_id IS NOT NULL ORDER BY name ASC");

while ($series = mysqli_fetch_assoc($result)) {
	sleep(1); //Respect Jikan API limits
	$url = 'https://api.jikan.moe/v4/'.$series['type'].'/'.$series['external_id'];
	$response = json_decode(file_get_contents($url));
	
	if (!$response || !$response->data){
		echo "Error updating ".$series['name'];
	} else {
		$title = $response->data->title;
		if ($series['name']!=$title) {
			echo $series['name']." -> ".$title."\n";
			if (!defined('DRY_RUN')) {
				query("UPDATE series SET name='".escape($title)."' WHERE id=".$series['id']);
			}
		}
	}
}

$result = query("SELECT d.*, s.type FROM division d LEFT JOIN series s ON d.series_id=s.id WHERE s.type='anime' AND d.external_id IS NOT NULL ORDER BY s.name ASC, d.number ASC");

while ($series = mysqli_fetch_assoc($result)) {
	sleep(1); //Respect Jikan API limits
	$url = 'https://api.jikan.moe/v4/'.$series['type'].'/'.$series['external_id'];
	$response = json_decode(file_get_contents($url));
	
	if (!$response || empty($response->data)){
		echo "Error updating ".$series['name'];
	} else {
		$title = $response->data->title;
		if ($series['name']!=$title) {
			echo $series['name']." -> ".$title."\n";
			if (!defined('DRY_RUN')) {
				query("UPDATE division SET name='".escape($title)."' WHERE id=".$series['id']);
			}
		}
	}
}

echo "All done!\n";
?>

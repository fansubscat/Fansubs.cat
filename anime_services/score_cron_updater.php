<?php
require_once('db.inc.php');

$result = query("SELECT * FROM series WHERE myanimelist_id IS NOT NULL ORDER BY name");

while ($series = mysqli_fetch_assoc($result)) {
	echo "Updating score for series ".$series['name']."\n";
	$url = 'https://api.jikan.moe/v3/anime/'.$series['myanimelist_id'];
	$response = json_decode(file_get_contents($url));
	
	if (!$response){
		echo "Update failed for series ".$series['name']."\n";
		log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació de la sèrie '".$series['name']."'");
	} else {
		$score = $response->score;
		if ($score && is_numeric($score)) {
			if ($series['score']!=$score) {
				echo "Previous score: ".$series['score']." / New score: $score\n";
				query("UPDATE series SET score=".escape($score)." WHERE id=".$series['id']);
			}
		} else {
			echo "Update failed for series ".$series['name']."\n";
			log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació de la sèrie '".$series['name']."'");
		}
	}
	sleep(4);
}
log_action('cron-scores-updated', "S'ha actualitzat la puntuació de les sèries");
echo "All done!\n";

mysqli_free_result($result);
?>

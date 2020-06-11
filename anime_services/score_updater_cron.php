<?php
require_once('db.inc.php');

log_action('cron-scores-started', "S'ha iniciat l'actualització de la puntuació de les sèries");

$result = query("SELECT * FROM series ORDER BY name ASC");

while ($series = mysqli_fetch_assoc($result)) {
	echo "Updating score for series ".$series['name']."\n";

	$resultss = query("SELECT * FROM season WHERE series_id=".$series['id']." AND myanimelist_id IS NOT NULL ORDER BY number ASC");

	$seasoncount = 0;
	$seasonscoresum = 0;
	$error = FALSE;
	while ($season = mysqli_fetch_assoc($resultss)) {
		sleep(4); //4s for Jikan request limits
		$url = 'https://api.jikan.moe/v3/anime/'.$season['myanimelist_id'];
		$response = json_decode(file_get_contents($url));
		
		if (!$response){
			$error = TRUE;
			break;
		} else {
			$score = $response->score;
			if ($score && is_numeric($score)) {
				$seasonscoresum+=$score;
			} else { //Skip this season: no score
				echo "Series ".$series['name']." has season ".$season['myanimelist_id']." with no score.\n";
				continue;
			}
		}
		$seasoncount++;
	}

	if ($error) {
		echo "Update failed for series ".$series['name']."\n";
		log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació de la sèrie '".$series['name']."'");
	} else {
		if ($seasoncount>0) { //if it's zero, we ignore it... no myanimelist, probably
			$new_score=round($seasonscoresum/$seasoncount, 2);
			if ($series['score']!=$new_score) {
				echo "Previous score: ".$series['score']." / New score: $new_score\n";
				log_action('cron-score-updated', "La puntuació de la sèrie '".$series['name']."' ha canviat de ".$series['score'].' a '.$new_score);
				query("UPDATE series SET score=".escape($new_score)." WHERE id=".$series['id']);
			}
		}
	}
}
log_action('cron-scores-finished', "S'ha completat l'actualització de la puntuació de les sèries");
echo "All done!\n";

mysqli_free_result($result);
?>

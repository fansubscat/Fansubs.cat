<?php
require_once('db.inc.php');

log_action('cron-scores-started', "S'ha iniciat l'actualització de la puntuació de l'anime i el manga");

$result = query("SELECT * FROM series WHERE type<>'liveaction' ORDER BY name ASC");

while ($series = mysqli_fetch_assoc($result)) {
	echo "Updating score for series ".$series['name']."\n";

	$resultss = query("SELECT DISTINCT sq.external_id FROM (SELECT external_id FROM division WHERE series_id=".$series['id']." AND external_id IS NOT NULL UNION SELECT a.external_id FROM series a WHERE a.id=".$series['id']." AND a.external_id IS NOT NULL) sq");

	$divisioncount = 0;
	$divisionscoresum = 0;
	$error = FALSE;
	while ($division = mysqli_fetch_assoc($resultss)) {
		sleep(10); //At least 4s for Jikan request limits
		$url = 'https://api.jikan.moe/v4/'.$series['type'].'/'.$division['external_id'];
		$response = json_decode(file_get_contents($url));
		
		if (!$response || !$response->data){
			$error = TRUE;
			break;
		} else {
			$score = $response->data->score;
			if ($score && is_numeric($score)) {
				$divisionscoresum+=$score;
			} else { //Skip this division: no score
				echo "Series ".$series['name']." has division ".$division['external_id']." with no score.\n";
				continue;
			}
		}
		$divisioncount++;
	}

	if ($error) {
		echo "Update failed for series ".$series['name']."\n";
		log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació de la sèrie '".$series['name']."'");
	} else {
		if ($divisioncount>0) { //if it's zero, we ignore it... no myanimelist, probably
			$new_score=round($divisionscoresum/$divisioncount, 2);
			if ($series['score']!=$new_score) {
				echo "Previous score: ".$series['score']." / New score: $new_score\n";
				log_action('cron-score-updated', "La puntuació de la sèrie '".$series['name']."' ha canviat de ".$series['score'].' a '.$new_score);
				query("UPDATE series SET score=".escape($new_score)." WHERE id=".$series['id']);
			}
		}
	}
}
mysqli_free_result($result);

log_action('cron-scores-finished', "S'ha completat l'actualització de la puntuació de l'anime i el manga");
echo "All done!\n";
?>

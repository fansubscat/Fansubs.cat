<?php
require_once('db.inc.php');

log_action('cron-scores-started', "S'ha iniciat l'actualització de la puntuació de l'anime i el manga");

$result = query("SELECT * FROM series ORDER BY name ASC");

while ($series = mysqli_fetch_assoc($result)) {
	echo "Updating score for anime ".$series['name']."\n";

	$resultss = query("SELECT DISTINCT sq.myanimelist_id FROM (SELECT myanimelist_id FROM season WHERE series_id=".$series['id']." AND myanimelist_id IS NOT NULL UNION SELECT myanimelist_id FROM series a WHERE a.id=".$series['id']." AND myanimelist_id IS NOT NULL) sq");

	$seasoncount = 0;
	$seasonscoresum = 0;
	$error = FALSE;
	while ($season = mysqli_fetch_assoc($resultss)) {
		sleep(10); //At least 4s for Jikan request limits
		$url = 'https://api.jikan.moe/v4/anime/'.$season['myanimelist_id'];
		$response = json_decode(file_get_contents($url));
		
		if (!$response || !$response->data){
			$error = TRUE;
			break;
		} else {
			$score = $response->data->score;
			if ($score && is_numeric($score)) {
				$seasonscoresum+=$score;
			} else { //Skip this season: no score
				echo "Anime ".$series['name']." has season ".$season['myanimelist_id']." with no score.\n";
				continue;
			}
		}
		$seasoncount++;
	}

	if ($error) {
		echo "Update failed for series ".$series['name']."\n";
		log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació de l'anime '".$series['name']."'");
	} else {
		if ($seasoncount>0) { //if it's zero, we ignore it... no myanimelist, probably
			$new_score=round($seasonscoresum/$seasoncount, 2);
			if ($series['score']!=$new_score) {
				echo "Previous score: ".$series['score']." / New score: $new_score\n";
				log_action('cron-score-updated', "La puntuació de l'anime '".$series['name']."' ha canviat de ".$series['score'].' a '.$new_score);
				query("UPDATE series SET score=".escape($new_score)." WHERE id=".$series['id']);
			}
		}
	}
}
mysqli_free_result($result);

$result = query("SELECT * FROM manga ORDER BY name ASC");

while ($manga = mysqli_fetch_assoc($result)) {
	echo "Updating score for manga ".$manga['name']."\n";

	$resultss = query("SELECT DISTINCT sq.myanimelist_id FROM (SELECT myanimelist_id FROM volume WHERE manga_id=".$manga['id']." AND myanimelist_id IS NOT NULL UNION SELECT myanimelist_id FROM manga m WHERE m.id=".$manga['id']." AND myanimelist_id IS NOT NULL) sq");

	$seasoncount = 0;
	$seasonscoresum = 0;
	$error = FALSE;
	while ($season = mysqli_fetch_assoc($resultss)) {
		sleep(10); //At least 4s for Jikan request limits
		$url = 'https://api.jikan.moe/v4/manga/'.$season['myanimelist_id'];
		$response = json_decode(file_get_contents($url));
		
		if (!$response || !$response->data){
			$error = TRUE;
			break;
		} else {
			$score = $response->data->score;
			if ($score && is_numeric($score)) {
				$seasonscoresum+=$score;
			} else { //Skip this season: no score
				echo "Manga ".$manga['name']." has season ".$season['myanimelist_id']." with no score.\n";
				continue;
			}
		}
		$seasoncount++;
	}

	if ($error) {
		echo "Update failed for manga ".$manga['name']."\n";
		log_action('cron-score-failed', "No s'ha pogut actualitzar la puntuació del manga '".$manga['name']."'");
	} else {
		if ($seasoncount>0) { //if it's zero, we ignore it... no myanimelist, probably
			$new_score=round($seasonscoresum/$seasoncount, 2);
			if ($manga['score']!=$new_score) {
				echo "Previous score: ".$manga['score']." / New score: $new_score\n";
				log_action('cron-score-updated', "La puntuació del manga '".$manga['name']."' ha canviat de ".$manga['score'].' a '.$new_score);
				query("UPDATE manga SET score=".escape($new_score)." WHERE id=".$manga['id']);
			}
		}
	}
}
mysqli_free_result($result);

log_action('cron-scores-finished', "S'ha completat l'actualització de la puntuació de l'anime i el manga");
echo "All done!\n";
?>

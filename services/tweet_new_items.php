<?php
require_once('db.inc.php');
require_once('common.inc.php');
require_once("libs/codebird.php");

//Connect to the anime database too
$db_connection_anime = mysqli_connect($db_host_anime,$db_user_anime,$db_passwd_anime, $db_name_anime) or die('Could not connect to anime database');
unset($db_host_anime, $db_name_anime, $db_user_anime, $db_passwd_anime);
mysqli_set_charset($db_connection_anime, 'utf8mb4') or crash(mysqli_error($db_connection_anime));

//Connect to the manga database too
$db_connection_manga = mysqli_connect($db_host_manga,$db_user_manga,$db_passwd_manga, $db_name_manga) or die('Could not connect to manga database');
unset($db_host_manga, $db_name_manga, $db_user_manga, $db_passwd_manga);
mysqli_set_charset($db_connection_manga, 'utf8mb4') or crash(mysqli_error($db_connection_manga));

$last_tweeted_manga_id=(int)file_get_contents('last_tweeted_manga_id.txt');
$last_tweeted_anime_id=(int)file_get_contents('last_tweeted_anime_id.txt');

$new_manga_tweets = array(
	'Tenim un nou manga a manga.fansubs.cat: «%1$s»!',
	'Hi ha disponible un nou manga a manga.fansubs.cat: «%1$s»!',
	'Ja podeu llegir el nou manga «%1$s» a manga.fansubs.cat!',
	'Hem afegit un nou manga a manga.fansubs.cat: «%1$s»!',
	'Nou manga: «%1$s»! Seguiu-lo a manga.fansubs.cat!'
);

$new_chapter_tweets = array(
	'Ja hi ha disponible «%1$s - %2$s» al web de manga.fansubs.cat!',
	'S\'ha afegit «%1$s - %2$s» al web de manga.fansubs.cat!',
	'Ja podeu llegir «%1$s - %2$s» al web de manga.fansubs.cat!'
);

$new_anime_tweets = array(
	'Tenim un nou anime subtitulat per %2$s a anime.fansubs.cat: «%1$s»!',
	'Hi ha disponible un nou anime subtitulat per %2$s a anime.fansubs.cat: «%1$s»!',
	'Ja podeu mirar l\'anime «%1$s» subtitulat per %2$s a anime.fansubs.cat!',
	'Hem afegit un nou anime subtitulat per %2$s a anime.fansubs.cat: «%1$s»!',
	'Nou anime: «%1$s», subtitulat per %2$s! Seguiu-lo a anime.fansubs.cat!'
);

$new_episode_number_tweets = array(
	'Ja hi ha disponible el capítol %4$d de «%1$s» (%3$s), «%2$s». El trobareu a anime.fansubs.cat!',
	'Hem afegit el capítol %4$d de «%1$s» (%3$s), «%2$s». Mireu-lo al web d\'anime.fansubs.cat!',
	'Ja podeu mirar el capítol %4$d de «%1$s» (%3$s), «%2$s». El teniu al web d\'anime.fansubs.cat!'
);

$new_episode_number_no_name_tweets = array(
	'Ja hi ha disponible el capítol %4$d de «%1$s» (%3$s). El trobareu a anime.fansubs.cat!',
	'Hem afegit el capítol %4$d de «%1$s» (%3$s). Mireu-lo al web d\'anime.fansubs.cat!',
	'Ja podeu mirar el capítol %4$d de «%1$s» (%3$s). El teniu al web d\'anime.fansubs.cat!'
);

$new_episode_no_number_tweets = array(
	'Ja hi ha disponible un nou capítol de «%1$s» (%3$s) a anime.fansubs.cat: «%2$s».',
	'Hem afegit un nou capítol de «%1$s» (%3$s) al web d\'anime.fansubs.cat: «%2$s».',
	'Ja podeu mirar un nou capítol de «%1$s» (%3$s) a anime.fansubs.cat: «%2$s».'
);

$new_episodes_tweets = array(
	'Ja hi ha disponibles %2$d capítols nous de «%1$s» (%3$s) al web d\'anime.fansubs.cat!',
	'Hem afegit %2$d capítols nous de «%1$s» (%3$s) al web d\'anime.fansubs.cat!',
	'Ja podeu mirar %2$d capítols nous de «%1$s» (%3$s) al web d\'anime.fansubs.cat!'
);

$tweets = array();

$result = mysqli_query($db_connection_manga, "SELECT * FROM piwigo_categories c WHERE id>$last_tweeted_manga_id ORDER BY id_uppercat IS NULL DESC, id ASC") or die(mysqli_error($db_connection_manga));
$new_mangas = array();
while ($row = mysqli_fetch_assoc($result)){
	if (empty($row['id_uppercat'])) {
		array_push($new_mangas, $row['id']);
		$random = array_rand($new_manga_tweets, 1);
		$tweet = sprintf($new_manga_tweets[$random], $row['name'])."\nhttps://manga.fansubs.cat/index/category/".$row['id']."-".str_replace('-','_',slugify($row['name']));
		array_push($tweets, $tweet);
	} else { // Get only the last branches of the tree
		$cntres = mysqli_query($db_connection_manga, "SELECT COUNT(*) cnt FROM piwigo_categories c WHERE id_uppercat=".$row['id']) or die(mysqli_error($db_connection_manga));
		$cntrow = mysqli_fetch_assoc($cntres);
		mysqli_free_result($cntres);
		if ($cntrow['cnt']==0) {
			$parentrow = array();
			$parentrow['id_uppercat']=$row['id_uppercat'];
			do {
				$parentres = mysqli_query($db_connection_manga, "SELECT * FROM piwigo_categories c WHERE id=".$parentrow['id_uppercat']) or die(mysqli_error($db_connection_manga));
				$parentrow = mysqli_fetch_assoc($parentres);
				mysqli_free_result($parentres);
			} while (!empty($parentrow['id_uppercat']));

			if (!in_array($parentrow['id'], $new_mangas)) { //Ignore if already reported as new manga
				$random = array_rand($new_chapter_tweets, 1);
				$tweet = sprintf($new_chapter_tweets[$random], $parentrow['name'], $row['name'])."\nhttps://manga.fansubs.cat/index/category/".$row['id']."-".str_replace('-','_',slugify($row['name']));
				array_push($tweets, $tweet);
			}
		}
	}
	file_put_contents('last_tweeted_manga_id.txt', $row['id']);
}

mysqli_free_result($result);

$result = mysqli_query($db_connection_anime, "SELECT s.name, s.type, s.slug, MAX(l.id) id, l.version_id, COUNT(l.version_id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle SEPARATOR ' + ') fansub_handles, e.number, et.title, s.show_episode_numbers, NOT EXISTS(SELECT l2.id FROM link l2 WHERE l2.id<=$last_tweeted_anime_id AND l2.version_id=l.version_id AND l2.url IS NOT NULL) new_series
FROM link l
LEFT JOIN version v ON l.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title et ON l.episode_id=et.episode_id AND et.version_id=l.version_id
LEFT JOIN episode e ON l.episode_id=e.id
WHERE l.id>$last_tweeted_anime_id AND l.url IS NOT NULL AND l.episode_id IS NOT NULL GROUP BY l.version_id ORDER BY MAX(l.id) ASC") or die(mysqli_error($db_connection_anime));
while ($row = mysqli_fetch_assoc($result)){
	if ($row['new_series']==1) {
		$random = array_rand($new_anime_tweets, 1);
		$tweet = sprintf($new_anime_tweets[$random], $row['name'], $row['fansub_handles'])."\nhttps://anime.fansubs.cat/".($row['type']=='series' ? 'series' : 'films')."/".$row['slug']."?version=".$row['version_id'];
		array_push($tweets, $tweet);
	} else if ($row['cnt']>1){ //Multiple episodes
		$random = array_rand($new_episodes_tweets, 1);
		$tweet = sprintf($new_episodes_tweets[$random], $row['name'], $row['cnt'], $row['fansub_handles'])."\nhttps://anime.fansubs.cat/".($row['type']=='series' ? 'series' : 'films')."/".$row['slug']."?version=".$row['version_id'];
		array_push($tweets, $tweet);
	} else { //Single episode
		if ($row['show_episode_numbers']==1) {
			if (!empty($row['title'])) {
				$random = array_rand($new_episode_number_tweets, 1);
				$tweet = sprintf($new_episode_number_tweets[$random], $row['name'], $row['title'], $row['fansub_handles'], $row['number'])."\nhttps://anime.fansubs.cat/".($row['type']=='series' ? 'series' : 'films')."/".$row['slug']."?version=".$row['version_id'];
				array_push($tweets, $tweet);
			} else {
				$random = array_rand($new_episode_number_no_name_tweets, 1);
				$tweet = sprintf($new_episode_number_tweets[$random], $row['name'], '', $row['fansub_handles'], $row['number'])."\nhttps://anime.fansubs.cat/".($row['type']=='series' ? 'series' : 'films')."/".$row['slug']."?version=".$row['version_id'];
				array_push($tweets, $tweet);
			}
		} else {
			$random = array_rand($new_episode_no_number_tweets, 1);
			$tweet = sprintf($new_episode_no_number_tweets[$random], $row['name'], $row['title'], $row['fansub_handles'])."\nhttps://anime.fansubs.cat/".($row['type']=='series' ? 'series' : 'films')."/".$row['slug']."?version=".$row['version_id'];
			array_push($tweets, $tweet);
		}
	}
	file_put_contents('last_tweeted_anime_id.txt', $row['id']);
}

foreach ($tweets as $tweet) {
	\Codebird\Codebird::setConsumerKey($twitter_consumer_key, $twitter_consumer_secret);
	$cb = \Codebird\Codebird::getInstance();
	$cb->setToken($twitter_access_token, $twitter_access_token_secret);

	$params = array(
		'status' => $tweet //Assuming that it will not exceed 280 characters... maybe assuming too much, heh
	);
	$cb->statuses_update($params);
}

mysqli_free_result($result);

mysqli_close($db_connection_anime);
mysqli_close($db_connection_manga);
mysqli_close($db_connection);
?>

<?php
require_once('db.inc.php');
require_once("vendor/autoload.php");

//TODO Comment this out when entering production fansubs.online
define('DRY_RUN', TRUE);

use Abraham\TwitterOAuth\TwitterOAuth;

function publish_to_x($message){
	if (defined('DRY_RUN')) {
		echo "-----------------\nPost this to X:\n$message\n";
		return;
	}
	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
	$connection->setApiVersion('2');
	$content = $connection->post("tweets", ["text" => $message], TRUE);
}

function publish_to_mastodon($message){
	if (defined('DRY_RUN')) {
		echo "-----------------\nPost this to Mastodon:\n$message\n";
		return;
	}

	$post_data = array(
		'status' => $message,
		'language' => 'ca'
	);
	$context = stream_context_create(array(
		'http' => array(
		        'method' => 'POST',
		        'header' => "Authorization: Bearer ".MASTODON_ACCESS_TOKEN."\r\nContent-Type: application/json\r\n",
		        'content' => json_encode($post_data)
		)
	));
	@file_get_contents(MASTODON_HOST.'/api/v1/statuses', FALSE, $context);
}

function publish_to_discord($text, $title, $description, $url, $image){
	foreach (DISCORD_WEBHOOKS as $webhook) {
		if (defined('DRY_RUN')) {
			echo "-----------------\nPost this to Discord:\n$text\n";
			continue;
		}
		$post_data = array(
			'content' => "$text",
			'embeds' => array(
			        array(
			                'title' => $title,
			                'description' => strip_tags((strlen($description) > 256) ? substr($description,0,253).'...' : $description),
			                'url' => $url,
			                'image' => array(
						'url' => $image
					),
			                'color' => (strpos($url, 'https://manga')===0 ? 16027660 : (strpos($url, 'https://imatgereal')===0 ? 11348265 : 3901635))
			        )
			)
		);
		$context = stream_context_create(array(
			'http' => array(
			        'method' => 'POST',
			        'header' => "Content-Type: application/json\r\n",
			        'content' => json_encode($post_data)
			)
		));
		@file_get_contents($webhook, FALSE, $context);
	}
}

function publish_to_telegram($message){
	foreach (TELEGRAM_CONFIG as $config) {
		if (defined('DRY_RUN')) {
			echo "-----------------\nPost this to Telegram:\n$message\n";
			continue;
		}
		@file_get_contents("https://api.telegram.org/bot".$config['TELEGRAM_BOT_API_KEY']."/sendMessage?chat_id=".$config['TELEGRAM_BOT_CHANNEL_CHAT_ID']."&text=".urlencode($message)."&parse_mode=Markdown", FALSE, $context);
	}
}

//Copied from catalogue's common.inc.php
function get_episode_title($series_subtype, $show_episode_numbers, $episode_number, $linked_episode_id, $title, $series_name, $extra_name, $is_extra) {
	if ($is_extra) {
		return $extra_name;
	}

	if ($show_episode_numbers && !empty($episode_number) && empty($linked_episode_id)) {
		if (!empty($title)){
			return 'Capítol '.str_replace('.',',',floatval($episode_number)).': '.$title;
		}
		else {
			return 'Capítol '.str_replace('.',',',floatval($episode_number));
		}
	} else {
		if (!empty($title)){
			return $title;
		} else if ($series_subtype=='oneshot' || $series_subtype=='movie') {
			return $series_name;
		} else {
			return 'Capítol desconegut';
		}
	}
}

function get_shortened_tweet($tweet){
	//Check that it will not exceed 280 characters... and ellipsize if needed
	//280: max tweet limit
	//-23: shortened link
	// -1: line feed
	if (mb_strlen($tweet)>(280-23-1)){
		return mb_substr($tweet, 0, (280-23-1-3-1)).'...';
	} else {
		return $tweet;
	}
}

function get_shortened_toot($toot){
	//Check that it will not exceed 500 characters... and ellipsize if needed
	//500: max toot limit
	//-23: shortened link
	// -5: 2xline feed + "➡️ "
	if (mb_strlen($toot)>(500-23-5)){
		return mb_substr($toot, 0, (500-23-5-3-1)).'...';
	} else {
		return $toot;
	}
}

function exists_more_than_one_version($series_id){
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id AND is_hidden=0");
	$row = mysqli_fetch_assoc($result);
	return ($row['cnt']>1);
}

function get_prepared_message($message, $post_header, $type_emoji, $series_name, $available_episodes, $fansub_names, $completed_status) {
	$message = str_replace('%%POST_HEADER%%', $post_header, $message);
	$message = str_replace('%%TYPE_EMOJI%%', $type_emoji, $message);
	$message = str_replace('%%SERIES_NAME%%', $series_name, $message);
	$message = str_replace('%%AVAILABLE_EPISODES%%', $available_episodes, $message);
	$message = str_replace('%%FANSUB_NAMES%%', $fansub_names, $message);
	$message = str_replace('%%COMPLETED_STATUS%%', $completed_status, $message);
	return $message;
}

function get_manga_header($type, $comic_type, $number_of_elements) {
	if ($type=='new') {
		switch ($comic_type) {
			case 'manhwa':
				return 'Nou manhwa editat!';
			case 'manhua':
				return 'Nou manhua editat!';
			case 'novel':
				return 'Nova novel·la lleugera editada!';
			case 'manga':
			default:
				return 'Nou manga editat!';
		}
	} else if ($number_of_elements==1) {
		switch ($comic_type) {
			case 'manhwa':
				return 'Nou capítol de manhwa editat:';
			case 'manhua':
				return 'Nou capítol de manhua editat:';
			case 'novel':
				return 'Nou capítol de novel·la lleugera editat:';
			case 'manga':
			default:
				return 'Nou capítol de manga editat:';
		}
	} else {
		switch ($comic_type) {
			case 'manhwa':
				return 'Nous capítols de manhwa editats:';
			case 'manhua':
				return 'Nous capítols de manhua editats:';
			case 'novel':
				return 'Nous capítols de novel·la lleugera editats:';
			case 'manga':
			default:
				return 'Nous capítols de manga editats:';
		}
	}
}

function get_anime_header($type, $fansub_type, $number_of_elements) {
	if ($type=='new') {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nou anime doblat!';
			case 'fansub':
			default:
				return 'Nou anime subtitulat!';
		}
	} else if ($number_of_elements==1) {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nou capítol d’anime doblat:';
			case 'fansub':
			default:
				return 'Nou capítol d’anime subtitulat:';
		}
	} else {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nous capítols d’anime doblats:';
			case 'fansub':
			default:
				return 'Nous capítols d’anime subtitulats:';
		}
	}
}

function get_liveaction_header($type, $fansub_type, $number_of_elements) {
	if ($type=='new') {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nou contingut d’acció real doblat!';
			case 'fansub':
			default:
				return 'Nou contingut d’acció real subtitulat!';
		}
	} else if ($number_of_elements==1) {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nou capítol d’acció real doblat:';
			case 'fansub':
			default:
				return 'Nou capítol d’acció real subtitulat:';
		}
	} else {
		switch ($fansub_type) {
			case 'fandub':
				return 'Nous capítols d’acció real doblats:';
			case 'fansub':
			default:
				return 'Nous capítols d’acció real subtitulats:';
		}
	}
}

$last_posted_manga_id=(int)file_get_contents('last_posted_manga_id.txt');
$last_posted_anime_id=(int)file_get_contents('last_posted_anime_id.txt');
$last_posted_liveaction_id=(int)file_get_contents('last_posted_liveaction_id.txt');

$message_x = "%%POST_HEADER%%\n%%TYPE_EMOJI%% %%SERIES_NAME%%\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_mastodon = "%%POST_HEADER%%\n\n%%TYPE_EMOJI%% %%SERIES_NAME%%\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_discord = "**%%POST_HEADER%%**\n\n%%TYPE_EMOJI%% **%%SERIES_NAME%%**\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_telegram = "**%%POST_HEADER%%**\n\n%%TYPE_EMOJI%% **%%SERIES_NAME%%**\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";

$has_posted_something = FALSE;

$result = query("SELECT s.name, s.synopsis, s.rating, v.status, v.series_id, s.subtype, s.comic_type, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, c.number, IF(ct.title IS NOT NULL, ct.title, IF(c.number IS NULL,c.description,ct.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_manga_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title ct ON fi.episode_id=ct.episode_id AND ct.version_id=fi.version_id
LEFT JOIN episode c ON fi.episode_id=c.id
LEFT JOIN division vo ON vo.id=c.division_id
WHERE s.type='manga' AND s.rating<>'XXX' AND fi.id>$last_posted_manga_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : "");
	try{
		$header = get_manga_header($row['new_series']==1 ? 'new' : 'existing', $row['comic_type'], $row['cnt']);
		if ($row['new_series']==1) {
			$episode = ($row['cnt']>1 ? $row['cnt'].' capítols disponibles' : (($row['subtype']=='oneshot' && $row['status']==1) ? 'One-shot' : '1 capítol disponible'));
		} else if ($row['cnt']==1) {
			$episode = get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			$episode = $row['cnt'].' capítols nous';
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url);
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url);
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_discord($prepared_message, $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], $url, "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg');
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url);
		file_put_contents('last_posted_manga_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}

$result = query("SELECT IFNULL(se.name,s.name) name, s.synopsis, s.rating, v.status, v.series_id, s.subtype, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, GROUP_CONCAT(DISTINCT f.type SEPARATOR '|') fansub_type, e.number, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_anime_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
LEFT JOIN episode e ON fi.episode_id=e.id
LEFT JOIN division se ON se.id=e.division_id
WHERE s.type='anime' AND s.rating<>'XXX' AND fi.id>$last_posted_anime_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : "");
	try{
		$header = get_anime_header($row['new_series']==1 ? 'new' : 'existing', $row['fansub_type'], $row['cnt']);
		if ($row['new_series']==1) {
			if ($row['subtype']=='movie') {
				$episode = ($row['cnt']>1 ? $row['cnt'].' films disponibles' : ($row['status']==1 ? 'Film' : '1 film disponible'));
			} else {
				$episode = ($row['cnt']>1 ? $row['cnt'].' capítols disponibles' : '1 capítol disponible');
			}
		} else if ($row['cnt']==1) {
			$episode = get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			if ($row['subtype']=='movie') {
				$episode = $row['cnt'].' films nous';
			} else {
				$episode = $row['cnt'].' capítols nous';
			}
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url);
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url);
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_discord($prepared_message, $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], $url, "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg');
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url);
		file_put_contents('last_posted_anime_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}

$result = query("SELECT IFNULL(se.name,s.name) name, s.synopsis, s.rating, v.status, v.series_id, s.subtype, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, GROUP_CONCAT(DISTINCT f.type SEPARATOR '|') fansub_type, e.number, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_liveaction_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
LEFT JOIN episode e ON fi.episode_id=e.id
LEFT JOIN division se ON se.id=e.division_id
WHERE s.type='liveaction' AND s.rating<>'XXX' AND fi.id>$last_posted_liveaction_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : "");
	try{
		$header = get_liveaction_header($row['new_series']==1 ? 'new' : 'existing', $row['fansub_type'], $row['cnt']);
		if ($row['new_series']==1) {
			if ($row['subtype']=='movie') {
				$episode = ($row['cnt']>1 ? $row['cnt'].' films disponibles' : ($row['status']==1 ? 'Film' : '1 film disponible'));
			} else {
				$episode = ($row['cnt']>1 ? $row['cnt'].' capítols disponibles' : '1 capítol disponible');
			}
		} else if ($row['cnt']==1) {
			$episode = get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			if ($row['subtype']=='movie') {
				$episode = $row['cnt'].' films nous';
			} else {
				$episode = $row['cnt'].' capítols nous';
			}
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url);
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url);
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_discord($prepared_message, $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], $url, "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg');
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n✅ Projecte completat" : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url);
		file_put_contents('last_posted_liveaction_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}
?>

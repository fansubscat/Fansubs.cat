<?php
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/vendor/autoload.php');

//define('DRY_RUN', TRUE);

use Abraham\TwitterOAuth\TwitterOAuth;
use cjrasmussen\BlueskyApi\BlueskyApi;

function publish_to_x($message, $is_hentai){
	if (defined('DRY_RUN')) {
		echo "-----------------\nPost this to X:\n$message\n";
		return;
	}
	if ($is_hentai) {
		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY_HENTAI, TWITTER_CONSUMER_SECRET_HENTAI, TWITTER_ACCESS_TOKEN_HENTAI, TWITTER_ACCESS_TOKEN_SECRET_HENTAI);
	} else {
		$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
	}
	$connection->setApiVersion('2');
	$content = $connection->post("tweets", ["text" => $message], TRUE);
}

function publish_to_mastodon($message, $is_hentai){
	if (defined('DRY_RUN')) {
		echo "-----------------\nPost this to Mastodon:\n$message\n";
		return;
	}

	$post_data = array(
		'status' => $message,
		'language' => SITE_LANGUAGE
	);
	if ($is_hentai) {
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: Bearer ".MASTODON_ACCESS_TOKEN_HENTAI."\r\nContent-Type: application/json\r\n",
				'content' => json_encode($post_data)
			)
		));
		@file_get_contents(MASTODON_HOST_HENTAI.'/api/v1/statuses', FALSE, $context);
	} else {
		$context = stream_context_create(array(
			'http' => array(
				'method' => 'POST',
				'header' => "Authorization: Bearer ".MASTODON_ACCESS_TOKEN."\r\nContent-Type: application/json\r\n",
				'content' => json_encode($post_data)
			)
		));
	}
		@file_get_contents(MASTODON_HOST.'/api/v1/statuses', FALSE, $context);
}

function publish_to_discord($text, $title, $description, $url, $image, $is_hentai){
	if ($is_hentai) {
		$webhooks = DISCORD_WEBHOOKS_HENTAI;
	} else {
		$webhooks = DISCORD_WEBHOOKS;
	}
	foreach ($webhooks as $webhook) {
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
			                'color' => (strpos($url, 'https://'.MANGA_SUBDOMAIN)===0 ? 16027660 : (strpos($url, 'https://'.LIVEACTION_SUBDOMAIN)===0 ? 11348265 : 3901635))
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

function publish_to_telegram($message, $is_hentai){
	if ($is_hentai) {
		$configs = TELEGRAM_CONFIG_HENTAI;
	} else {
		$configs = TELEGRAM_CONFIG;
	}
	foreach ($configs as $config) {
		if (defined('DRY_RUN')) {
			echo "-----------------\nPost this to Telegram:\n$message\n";
			continue;
		}
		@file_get_contents("https://api.telegram.org/bot".$config['TELEGRAM_BOT_API_KEY']."/sendMessage?chat_id=".$config['TELEGRAM_BOT_CHANNEL_CHAT_ID']."&text=".str_replace('_','\\_', urlencode($message))."&parse_mode=Markdown");
	}
}

function publish_to_bluesky($message, $version_id, $embed_title, $embed_description, $url, $is_hentai){
	if (defined('DRY_RUN')) {
		echo "-----------------\nPost this to BlueSky:\n$message\n";
		return;
	}
	if ($is_hentai) {
		$bluesky = new BlueskyApi(BLUESKY_HANDLE_HENTAI, BLUESKY_APP_PASSWORD_HENTAI);
	} else {
		$bluesky = new BlueskyApi(BLUESKY_HANDLE, BLUESKY_APP_PASSWORD);
	}
	$image_body = @file_get_contents(STATIC_DIRECTORY.'/social/version_'.$version_id.'.jpg');
	$response = $bluesky->request('POST', 'com.atproto.repo.uploadBlob', [], $image_body, 'image/jpeg');
	$image_blob = $response->blob;
	$args = [
		'collection' => 'app.bsky.feed.post',
		'repo' => $bluesky->getAccountDid(),
		'record' => [
			'text' => $message,
			'facets' => get_bluesky_facets($is_hentai, $message),
			'langs' => [SITE_LANGUAGE],
			'createdAt' => date('c'),
			'$type' => 'app.bsky.feed.post',
			'embed' => [
				'$type' => 'app.bsky.embed.external',
				'external' => [
					'uri' => $url,
					'title' => $embed_title,
					'description' => $embed_description,
					'thumb' => $image_blob,
				],
			],
		],
	];
	$data = $bluesky->request('POST', 'com.atproto.repo.createRecord', $args);
}

function publish_to_community($message, $is_hentai) {
	if (!DISABLE_COMMUNITY) {
		if (!$is_hentai) { //Hide hentai for now
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://' . COMMUNITY_SUBDOMAIN . '.' . MAIN_DOMAIN . '/api/add_chat_message');
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("X-Fansubscat-Api-Token: " . INTERNAL_SERVICES_TOKEN));
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, 
				  json_encode(array(
				  	'message' => $message,
				  	)));
			curl_exec($curl);
			curl_close($curl);
		}
	}
}

//Copied from catalogue's common.inc.php
function get_episode_title($series_subtype, $show_episode_numbers, $episode_number, $linked_episode_id, $title, $series_name, $extra_name, $is_extra) {
	if ($is_extra) {
		return $extra_name;
	}

	if ($show_episode_numbers && !empty($episode_number) && empty($linked_episode_id)) {
		if (!empty($title)){
			return lang('service.post.episode_prefix').str_replace('.',',',floatval($episode_number)).': '.$title;
		}
		else {
			return lang('service.post.episode_prefix').str_replace('.',',',floatval($episode_number));
		}
	} else {
		if (!empty($title)){
			return $title;
		} else {
			return $series_name;
		}
	}
}

function parse_mentions($text_bytes) {
    $spans = [];
    // regex based on: https://atproto.com/specs/handle#handle-identifier-syntax
    $mention_regex = '/[$|\W](@([a-zA-Z0-9]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]([a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)/';

    if (preg_match_all($mention_regex, $text_bytes, $matches, PREG_OFFSET_CAPTURE)) {
        foreach ($matches[1] as $match) {
            $spans[] = [
                "start" => $match[1],
                "end" => $match[1] + strlen($match[0]),
                "handle" => substr($match[0], 1)
            ];
        }
    }

    return $spans;
}

function get_bluesky_facets($is_hentai, $post) {
	if ($is_hentai) {
		$bluesky = new BlueskyApi(BLUESKY_HANDLE_HENTAI, BLUESKY_APP_PASSWORD_HENTAI);
	} else {
		$bluesky = new BlueskyApi(BLUESKY_HANDLE, BLUESKY_APP_PASSWORD);
	}
	
	$mentions = parse_mentions($post);
	$facets=array();
	foreach ($mentions as $mention) {
		$args = [
			'handle' => $mention['handle'],
		];
		$response = $bluesky->request('GET', 'com.atproto.identity.resolveHandle', $args);
		if ($response!=NULL) {
			$facet = [
				'index' => [
					'byteStart' => $mention['start'],
					'byteEnd' => $mention['end'],
				],
				'features' => [
					[
						'$type' => 'app.bsky.richtext.facet#mention',
						'did' => $response->did,
					],
				],
			];
			array_push($facets, $facet);
		}
	}
	return $facets;
}

function get_shortened_bluesky_post($post){
	//Check that it will not exceed 300 characters... and ellipsize if needed
	if (mb_strlen($post)>300){
		return mb_substr($post, 0, 300-3).'...';
	} else {
		return $post;
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

function get_prepared_message($message, $post_header, $type_emoji, $series_name, $available_episodes, $fansub_names, $completed_status) {
	$message = str_replace('%%POST_HEADER%%', $post_header, $message);
	$message = str_replace('%%TYPE_EMOJI%%', $type_emoji, $message);
	$message = str_replace('%%SERIES_NAME%%', $series_name, $message);
	$message = str_replace('%%AVAILABLE_EPISODES%%', $available_episodes, $message);
	$message = str_replace('%%FANSUB_NAMES%%', $fansub_names, $message);
	$message = str_replace('%%COMPLETED_STATUS%%', $completed_status, $message);
	return $message;
}

function get_manga_header($type, $comic_type, $number_of_elements, $is_hentai) {
	if ($type=='new') {
		switch ($comic_type) {
			case 'manhwa':
				return $is_hentai ? lang('service.post.new_manhwa.hentai') : lang('service.post.new_manhwa');
			case 'manhua':
				return $is_hentai ? lang('service.post.new_manhua.hentai') : lang('service.post.new_manhua');
			case 'novel':
				return $is_hentai ? lang('service.post.new_novel.hentai') : lang('service.post.new_novel');
			case 'manga':
			default:
				return $is_hentai ? lang('service.post.new_manga.hentai') : lang('service.post.new_manga');
		}
	} else if ($number_of_elements==1) {
		switch ($comic_type) {
			case 'manhwa':
				return $is_hentai ? lang('service.post.new_manhwa_ep.hentai') : lang('service.post.new_manhwa_ep');
			case 'manhua':
				return $is_hentai ? lang('service.post.new_manhua_ep.hentai') : lang('service.post.new_manhua_ep');
			case 'novel':
				return $is_hentai ? lang('service.post.new_novel_ep.hentai') : lang('service.post.new_novel_ep');
			case 'manga':
			default:
				return $is_hentai ? lang('service.post.new_manga_ep.hentai') : lang('service.post.new_manga_ep');
		}
	} else {
		switch ($comic_type) {
			case 'manhwa':
				return $is_hentai ? lang('service.post.new_manhwa_eps.hentai') : lang('service.post.new_manhwa_eps');
			case 'manhua':
				return $is_hentai ? lang('service.post.new_manhua_eps.hentai') : lang('service.post.new_manhua_eps');
			case 'novel':
				return $is_hentai ? lang('service.post.new_novel_eps.hentai') : lang('service.post.new_novel_eps');
			case 'manga':
			default:
				return $is_hentai ? lang('service.post.new_manga_eps.hentai') : lang('service.post.new_manga_eps');
		}
	}
}

function get_anime_header($type, $fansub_type, $number_of_elements, $is_hentai) {
	if ($type=='new') {
		switch ($fansub_type) {
			case 'fandub':
				return $is_hentai ? lang('service.post.new_anime_dub.hentai') : lang('service.post.new_anime_dub');
			case 'fansub':
			default:
				return $is_hentai ? lang('service.post.new_anime_sub.hentai') : lang('service.post.new_anime_sub');
		}
	} else if ($number_of_elements==1) {
		switch ($fansub_type) {
			case 'fandub':
				return $is_hentai ? lang('service.post.new_anime_dub_ep.hentai') : lang('service.post.new_anime_dub_ep');
			case 'fansub':
			default:
				return $is_hentai ? lang('service.post.new_anime_sub_ep.hentai') : lang('service.post.new_anime_sub_ep');
		}
	} else {
		switch ($fansub_type) {
			case 'fandub':
				return $is_hentai ? lang('service.post.new_anime_dub_eps.hentai') : lang('service.post.new_anime_dub_eps');
			case 'fansub':
			default:
				return $is_hentai ? lang('service.post.new_anime_sub_eps.hentai') : lang('service.post.new_anime_sub_eps');
		}
	}
}

function get_liveaction_header($type, $fansub_type, $number_of_elements, $is_hentai) {
	if ($type=='new') {
		switch ($fansub_type) {
			case 'fandub':
				return lang('service.post.new_liveaction_dub');
			case 'fansub':
			default:
				return lang('service.post.new_liveaction_sub');
		}
	} else if ($number_of_elements==1) {
		switch ($fansub_type) {
			case 'fandub':
				return lang('service.post.new_liveaction_dub_ep');
			case 'fansub':
			default:
				return lang('service.post.new_liveaction_sub_ep');
		}
	} else {
		switch ($fansub_type) {
			case 'fandub':
				return lang('service.post.new_liveaction_dub_eps');
			case 'fansub':
			default:
				return lang('service.post.new_liveaction_sub_eps');
		}
	}
}

$last_posted_manga_id=(int)file_get_contents('/srv/fansubscat/temporary/last_posted_manga_id.txt');
$last_posted_anime_id=(int)file_get_contents('/srv/fansubscat/temporary/last_posted_anime_id.txt');
$last_posted_liveaction_id=(int)file_get_contents('/srv/fansubscat/temporary/last_posted_liveaction_id.txt');

$message_x = "%%POST_HEADER%%\n%%TYPE_EMOJI%% %%SERIES_NAME%%\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_mastodon = "%%POST_HEADER%%\n\n%%TYPE_EMOJI%% %%SERIES_NAME%%\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_discord = "**%%POST_HEADER%%**\n\n%%TYPE_EMOJI%% **%%SERIES_NAME%%**\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_telegram = "*%%POST_HEADER%%*\n\n%%TYPE_EMOJI%% *%%SERIES_NAME%%*\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_bluesky = "%%POST_HEADER%%\n%%TYPE_EMOJI%% %%SERIES_NAME%%\n🔖 %%AVAILABLE_EPISODES%%\n👥 %%FANSUB_NAMES%%%%COMPLETED_STATUS%%";
$message_community = "%%POST_HEADER%% [url=%%URL%%][b]%%SERIES_NAME%% (%%FANSUB_NAMES%%)[/b][/url] - %%AVAILABLE_EPISODES%%%%COMPLETED_STATUS%%";

$has_posted_something = FALSE;

$result = query("SELECT v.title name, 
			IF((SELECT COUNT(*) FROM division dsq WHERE dsq.series_id=s.id AND dsq.number_of_episodes>0)>1, IFNULL(vvo.title, vo.name), NULL) division_name,
			v.synopsis, 
			s.rating, 
			v.status, 
			v.id version_id, 
			s.subtype, 
			s.comic_type, 
			v.slug, 
			MAX(fi.id) id, 
			fi.version_id, 
			COUNT(DISTINCT fi.id) cnt,
			GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,
			GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, 
			GROUP_CONCAT(DISTINCT f.bluesky_handle ORDER BY f.name SEPARATOR ' + ') fansub_bluesky_handles, 
			GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_names, 
			c.number, 
			IFNULL(ct.title, c.description) title, 
			v.show_episode_numbers, 
			NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_manga_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
		FROM file fi
			LEFT JOIN version v ON fi.version_id=v.id
			LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
			LEFT JOIN fansub f ON vf.fansub_id=f.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN episode_title ct ON fi.episode_id=ct.episode_id AND ct.version_id=fi.version_id
			LEFT JOIN episode c ON fi.episode_id=c.id
			LEFT JOIN division vo ON vo.id=c.division_id
			LEFT JOIN version_division vvo ON vvo.division_id=vo.id AND vvo.version_id=v.id
		WHERE s.type='manga' 
			AND fi.id>$last_posted_manga_id 
			AND fi.is_lost=0 
			AND fi.episode_id IS NOT NULL 
		GROUP BY fi.version_id 
		ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://".MANGA_SUBDOMAIN.".".($row['rating']=='XXX' ? HENTAI_DOMAIN : MAIN_DOMAIN)."/".$row['slug'];
	try{
		$header = get_manga_header($row['new_series']==1 ? 'new' : 'existing', $row['comic_type'], $row['cnt'], $row['rating']=='XXX');
		if ($row['new_series']==1) {
			$episode = ($row['cnt']>1 ? sprintf(lang('service.post.n_episodes_available'), $row['cnt']) : (($row['subtype']=='oneshot' && $row['status']==1) ? lang('service.post.oneshot') : lang('service.post.1_episode_available')));
		} else if ($row['cnt']==1) {
			if (!empty($row['division_name'])) {
				$episode = $row['division_name'].' - ';
			}
			$episode .= get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			$episode = sprintf(lang('service.post.n_new_episodes'), $row['cnt']);
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_discord($prepared_message, $row['name']." | ".($row['rating']=='XXX' ? lang('catalogue.page_title.manga.hentai').' | '.HENTAI_SITE_NAME : lang('catalogue.page_title.manga').' | '.MAIN_SITE_NAME), $row['synopsis'], $url, "https://".STATIC_SUBDOMAIN.".".MAIN_DOMAIN."/social/version_".$row['version_id'].'.jpg', $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_bluesky,
			$header,
			'📙',
			$row['name'],
			$episode,
			$row['fansub_bluesky_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_bluesky(get_shortened_bluesky_post($prepared_message), $row['version_id'], $row['name']." | ".($row['rating']=='XXX' ? lang('catalogue.page_title.manga.hentai').' | '.HENTAI_SITE_NAME : lang('catalogue.page_title.manga').' | '.MAIN_SITE_NAME), $row['synopsis'], $url, $row['rating']=='XXX');
		$community_message = str_replace('%%URL%%', $url, get_prepared_message(
				$message_community,
				$header,
				'',
				$row['name'],
				$episode,
				$row['fansub_names'],
				$row['status']==1 ? " - [b]".lang('service.post.project_completed').'[/b]' : ''
			));
		publish_to_community($community_message, $row['rating']=='XXX');
		file_put_contents('/srv/fansubscat/temporary/last_posted_manga_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}

$result = query("SELECT IFNULL(vse.title, se.name) name, 
			v.synopsis, 
			s.rating, 
			v.status, 
			v.id version_id, 
			s.subtype, 
			v.slug, 
			MAX(fi.id) id, 
			fi.version_id, 
			COUNT(DISTINCT fi.id) cnt,
			GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,
			GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, 
			GROUP_CONCAT(DISTINCT f.bluesky_handle ORDER BY f.name SEPARATOR ' + ') fansub_bluesky_handles,
			GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_names, 
			GROUP_CONCAT(DISTINCT f.type ORDER BY f.name SEPARATOR '|') fansub_type, 
			e.number, 
			IFNULL(et.title, e.description) title,
			v.show_episode_numbers, 
			NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_anime_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
		FROM file fi
			LEFT JOIN version v ON fi.version_id=v.id
			LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
			LEFT JOIN fansub f ON vf.fansub_id=f.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
			LEFT JOIN episode e ON fi.episode_id=e.id
			LEFT JOIN division se ON se.id=e.division_id
			LEFT JOIN version_division vse ON vse.division_id=se.id AND vse.version_id=v.id
		WHERE s.type='anime' 
			AND fi.id>$last_posted_anime_id 
			AND fi.is_lost=0 
			AND fi.episode_id IS NOT NULL 
		GROUP BY fi.version_id 
		ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://".ANIME_SUBDOMAIN.".".($row['rating']=='XXX' ? HENTAI_DOMAIN : MAIN_DOMAIN)."/".$row['slug'];
	try{
		$header = get_anime_header($row['new_series']==1 ? 'new' : 'existing', $row['fansub_type'], $row['cnt'], $row['rating']=='XXX');
		if ($row['new_series']==1) {
			if ($row['subtype']=='movie') {
				$episode = ($row['cnt']>1 ? sprintf(lang('service.post.n_movies_available'), $row['cnt']) : ($row['status']==1 ? lang('service.post.movie') : lang('service.post.1_movie_available')));
			} else {
				$episode = ($row['cnt']>1 ? sprintf(lang('service.post.n_episodes_available'), $row['cnt']) : lang('service.post.1_episode_available'));
			}
		} else if ($row['cnt']==1) {
			$episode = get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			if ($row['subtype']=='movie') {
				$episode = sprintf(lang('service.post.n_new_movies'), $row['cnt']);
			} else {
				$episode = sprintf(lang('service.post.n_new_episodes'), $row['cnt']);
			}
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_discord($prepared_message, $row['name']." | ".($row['rating']=='XXX' ? lang('catalogue.page_title.anime.hentai').' | '.HENTAI_SITE_NAME : lang('catalogue.page_title.anime').' | '.MAIN_SITE_NAME), $row['synopsis'], $url, "https://".STATIC_SUBDOMAIN.".".MAIN_DOMAIN."/social/version_".$row['version_id'].'.jpg', $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url, $row['rating']=='XXX');
		$prepared_message = get_prepared_message(
			$message_bluesky,
			$header,
			'🎞',
			$row['name'],
			$episode,
			$row['fansub_bluesky_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_bluesky(get_shortened_bluesky_post($prepared_message), $row['version_id'], $row['name']." | ".($row['rating']=='XXX' ? lang('catalogue.page_title.anime.hentai').' | '.HENTAI_SITE_NAME : lang('catalogue.page_title.anime').' | '.MAIN_SITE_NAME), $row['synopsis'], $url, $row['rating']=='XXX');
		$community_message = str_replace('%%URL%%', $url, get_prepared_message(
				$message_community,
				$header,
				'',
				$row['name'],
				$episode,
				$row['fansub_names'],
				$row['status']==1 ? " - [b]".lang('service.post.project_completed').'[/b]' : ''
			));
		publish_to_community($community_message, $row['rating']=='XXX');
		file_put_contents('/srv/fansubscat/temporary/last_posted_anime_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}

$result = query("SELECT IFNULL(vse.title,se.name) name, 
			v.synopsis, 
			s.rating, 
			v.status, 
			v.id version_id, 
			s.subtype, 
			v.slug, 
			MAX(fi.id) id, 
			fi.version_id, 
			COUNT(DISTINCT fi.id) cnt,
			GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,
			GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, 
			GROUP_CONCAT(DISTINCT f.bluesky_handle ORDER BY f.name SEPARATOR ' + ') fansub_bluesky_handles,
			GROUP_CONCAT(DISTINCT f.name ORDER BY f.name SEPARATOR ' + ') fansub_names, 
			GROUP_CONCAT(DISTINCT f.type ORDER BY f.name SEPARATOR '|') fansub_type, 
			e.number, 
			IFNULL(et.title, e.description) title,
			v.show_episode_numbers, 
			NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_posted_liveaction_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
		FROM file fi
			LEFT JOIN version v ON fi.version_id=v.id
			LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
			LEFT JOIN fansub f ON vf.fansub_id=f.id
			LEFT JOIN series s ON v.series_id=s.id
			LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
			LEFT JOIN episode e ON fi.episode_id=e.id
			LEFT JOIN division se ON se.id=e.division_id
			LEFT JOIN version_division vse ON vse.division_id=se.id AND vse.version_id=v.id
		WHERE s.type='liveaction' 
			AND fi.id>$last_posted_liveaction_id 
			AND fi.is_lost=0 
			AND fi.episode_id IS NOT NULL 
		GROUP BY fi.version_id 
		ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$url = "https://".LIVEACTION_SUBDOMAIN.".".MAIN_DOMAIN."/".$row['slug'];
	try{
		$header = get_liveaction_header($row['new_series']==1 ? 'new' : 'existing', $row['fansub_type'], $row['cnt'], FALSE);
		if ($row['new_series']==1) {
			if ($row['subtype']=='movie') {
				$episode = ($row['cnt']>1 ? sprintf(lang('service.post.n_movies_available'), $row['cnt']) : ($row['status']==1 ? lang('service.post.movie') : lang('service.post.1_movie_available')));
			} else {
				$episode = ($row['cnt']>1 ? sprintf(lang('service.post.n_episodes_available'), $row['cnt']) : lang('service.post.1_episode_available'));
			}
		} else if ($row['cnt']==1) {
			$episode = get_episode_title($row['subtype'], $row['show_episode_numbers'], $row['number'], NULL, $row['title'], $row['name'], NULL, FALSE);
		} else {
			if ($row['subtype']=='movie') {
				$episode = sprintf(lang('service.post.n_new_movies'), $row['cnt']);
			} else {
				$episode = sprintf(lang('service.post.n_new_episodes'), $row['cnt']);
			}
		}
		$prepared_message = get_prepared_message(
			$message_x,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_x(get_shortened_tweet($prepared_message)."\n".$url, FALSE);
		$prepared_message = get_prepared_message(
			$message_mastodon,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_mastodon_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_mastodon(get_shortened_toot($prepared_message)."\n\n➡️ ".$url, FALSE);
		$prepared_message = get_prepared_message(
			$message_discord,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_discord($prepared_message, $row['name']." | ".lang('catalogue.page_title.liveaction').' | '.MAIN_SITE_NAME, $row['synopsis'], $url, "https://".STATIC_SUBDOMAIN.".".MAIN_DOMAIN."/social/version_".$row['version_id'].'.jpg', FALSE);
		$prepared_message = get_prepared_message(
			$message_telegram,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_names'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_telegram($prepared_message."\n\n➡️ ".$url, FALSE);
		$prepared_message = get_prepared_message(
			$message_bluesky,
			$header,
			'🎥',
			$row['name'],
			$episode,
			$row['fansub_bluesky_handles'],
			$row['status']==1 ? "\n".lang('service.post.project_completed') : ''
		);
		publish_to_bluesky(get_shortened_bluesky_post($prepared_message), $row['version_id'], $row['name']." | ".lang('catalogue.page_title.liveaction').' | '.MAIN_SITE_NAME, $row['synopsis'], $url, FALSE);
		$community_message = str_replace('%%URL%%', $url, get_prepared_message(
				$message_community,
				$header,
				'',
				$row['name'],
				$episode,
				$row['fansub_names'],
				$row['status']==1 ? " - [b]".lang('service.post.project_completed').'[/b]' : ''
			));
		publish_to_community($community_message, FALSE);
		file_put_contents('/srv/fansubscat/temporary/last_posted_liveaction_id.txt', $row['id']);
	} catch(Exception $e) {
		die('Error occurred: '.$e->getMessage()."\n");
	}
}
?>

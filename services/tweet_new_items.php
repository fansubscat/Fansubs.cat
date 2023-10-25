<?php
require_once('db.inc.php');
require_once("vendor/autoload.php");

//TODO Comment this out when entering production fansubs.online
define('DRY_RUN', TRUE);

use Abraham\TwitterOAuth\TwitterOAuth;

function publish_tweet($tweet){
	if (defined('DRY_RUN')) {
		echo "Post this to X: $tweet\n";
		return;
	}
	$connection = new TwitterOAuth(TWITTER_CONSUMER_KEY, TWITTER_CONSUMER_SECRET, TWITTER_ACCESS_TOKEN, TWITTER_ACCESS_TOKEN_SECRET);
	$connection->setApiVersion('2');
	$content = $connection->post("tweets", ["text" => $tweet], TRUE);
}

function publish_toot($toot){
	if (defined('DRY_RUN')) {
		echo "Post this to Mastodon: $toot\n";
		return;
	}

	$post_data = array(
		'status' => $toot,
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

function publish_to_discord($text, $title, $description, $url, $image, $rating){
	foreach (DISCORD_WEBHOOKS as $webhook) {
		if (defined('DRY_RUN')) {
			echo "Post this to Discord: ".strip_tags((strlen($description) > 256) ? substr($description,0,253).'...' : $description)."\n";
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

function publish_to_telegram($text, $title, $description, $url, $image, $rating){
	foreach (TELEGRAM_CONFIG as $config) {
		if (defined('DRY_RUN')) {
			echo "Post this to Telegram: $text\n";
			continue;
		}
		@file_get_contents("https://api.telegram.org/bot".$config['TELEGRAM_BOT_API_KEY']."/sendMessage?chat_id=".$config['TELEGRAM_BOT_CHANNEL_CHAT_ID']."&text=".urlencode($text."\n\n$url")."&parse_mode=Markdown", FALSE, $context);
	}
}

function get_comic_type($comic_type){
	switch ($comic_type) {
		case 'manga':
			return 'manga';
		case 'manhwa':
			return 'manhwa';
		case 'manhua':
			return 'manhua';
		default:
			return 'còmic';
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
	// -4: line feed + "➡️ "
	if (mb_strlen($toot)>(500-23-4)){
		return mb_substr($toot, 0, (500-23-4-3-1)).'...';
	} else {
		return $toot;
	}
}

function exists_more_than_one_version($series_id){
	$result = query("SELECT COUNT(*) cnt FROM version WHERE series_id=$series_id AND is_hidden=0");
	$row = mysqli_fetch_assoc($result);
	return ($row['cnt']>1);
}

$last_tweeted_manga_id=(int)file_get_contents('last_tweeted_manga_id.txt');
$last_tweeted_anime_id=(int)file_get_contents('last_tweeted_anime_id.txt');
$last_tweeted_liveaction_id=(int)file_get_contents('last_tweeted_liveaction_id.txt');

//All these arrays are here in order to generate different strings each time and are classified by type: anime/manga, with numbers/no numbrs, etc.
//The first element in the inner array is the Twitter string, and the second element is the Discord string.
$new_manga_tweets = array(
	array(
		'Tenim un nou %COMIC_TYPE% editat per %2$s a manga.fansubs.cat: «%1$s»!',
		':new: Tenim un **nou %COMIC_TYPE%** editat per %2$s: **%1$s**!',
		'📙🆕 Tenim un *nou %COMIC_TYPE%* editat per %2$s: *%1$s*!'
	),
	array(
		'Hi ha disponible un nou %COMIC_TYPE% editat per %2$s a manga.fansubs.cat: «%1$s»!',
		':new: Hi ha disponible un **nou %COMIC_TYPE%** editat per %2$s: **%1$s**!',
		'📙🆕 Hi ha disponible un *nou %COMIC_TYPE%* editat per %2$s: *%1$s*!'
	),
	array(
		'Ja podeu llegir el nou %COMIC_TYPE% «%1$s» editat per %2$s a manga.fansubs.cat!',
		':new: Ja podeu llegir el **nou %COMIC_TYPE%** **%1$s** editat per %2$s!',
		'📙🆕 Ja podeu llegir el *nou %COMIC_TYPE%* *%1$s* editat per %2$s!'
	),
	array(
		'Hem afegit un nou %COMIC_TYPE% editat per %2$s a manga.fansubs.cat: «%1$s»!',
		':new: Hem afegit un **nou %COMIC_TYPE%** editat per %2$s: **%1$s**!',
		'📙🆕 Hem afegit un *nou %COMIC_TYPE%* editat per %2$s: *%1$s*!'
	),
	array(
		'Nou %COMIC_TYPE%: «%1$s», editat per %2$s! Seguiu-lo a manga.fansubs.cat!',
		':new: **Nou %COMIC_TYPE%:** **%1$s**, editat per %2$s! Seguiu-lo a Fansubs.cat!',
		'📙🆕 *Nou %COMIC_TYPE%:* *%1$s*, editat per %2$s! Seguiu-lo a Fansubs.cat!'
	)
);

$new_chapter_number_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s), «%2$s», al web de manga.fansubs.cat!',
		':orange_book: Ja hi ha disponible el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s), «%2$s»!',
		'📙 Ja hi ha disponible el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s), «%2$s»!'
	),
	array(
		'S’ha afegit el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s), «%2$s», al web de manga.fansubs.cat!',
		':orange_book: S’ha afegit el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s), «%2$s»!',
		'📙 S’ha afegit el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s), «%2$s»!'
	),
	array(
		'Ja podeu llegir el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s), «%2$s», al web de manga.fansubs.cat!',
		':orange_book: Ja podeu llegir el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s), «%2$s»!',
		'📙 Ja podeu llegir el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s), «%2$s»!'
	)
);

$new_chapter_number_no_name_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Ja hi ha disponible el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Ja hi ha disponible el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	),
	array(
		'Hem afegit el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Hem afegit el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Hem afegit el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	),
	array(
		'Ja podeu llegir el capítol %4$d del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Ja podeu llegir el **capítol %4$d** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Ja podeu llegir el *capítol %4$d* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	)
);

$new_chapter_no_number_tweets = array(
	array(
		'Ja hi ha disponible un nou capítol del %COMIC_TYPE% «%1$s» (editat per %3$s) a manga.fansubs.cat: «%2$s».',
		':orange_book: Ja hi ha disponible un **nou capítol** del %COMIC_TYPE% **%1$s** (editat per %3$s): «%2$s».',
		'📙 Ja hi ha disponible un *nou capítol* del %COMIC_TYPE% *%1$s* (editat per %3$s): «%2$s».'
	),
	array(
		'Hem afegit un nou capítol del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat: «%2$s».',
		':orange_book: Hem afegit un **nou capítol** del %COMIC_TYPE% **%1$s** (editat per %3$s): «%2$s».',
		'📙 Hem afegit un *nou capítol* del %COMIC_TYPE% *%1$s* (editat per %3$s): «%2$s».'
	),
	array(
		'Ja podeu llegir un nou capítol del %COMIC_TYPE% «%1$s» (editat per %3$s) a manga.fansubs.cat: «%2$s».',
		':orange_book: Ja podeu llegir un **nou capítol** del %COMIC_TYPE% **%1$s** (editat per %3$s): «%2$s».',
		'📙 Ja podeu llegir un *nou capítol* del %COMIC_TYPE% *%1$s* (editat per %3$s): «%2$s».'
	)
);

$new_chapters_tweets = array(
	array(
		'Ja hi ha disponibles %2$d capítols nous del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Ja hi ha disponibles **%2$d capítols nous** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Ja hi ha disponibles *%2$d capítols nous* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	),
	array(
		'Hem afegit %2$d capítols nous del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Hem afegit **%2$d capítols nous** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Hem afegit *%2$d capítols nous* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	),
	array(
		'Ja podeu llegir %2$d capítols nous del %COMIC_TYPE% «%1$s» (editat per %3$s) al web de manga.fansubs.cat!',
		':orange_book: Ja podeu llegir **%2$d capítols nous** del %COMIC_TYPE% **%1$s** (editat per %3$s)!',
		'📙 Ja podeu llegir *%2$d capítols nous* del %COMIC_TYPE% *%1$s* (editat per %3$s)!'
	)
);

$new_anime_tweets = array(
	array(
		'Tenim un nou anime %TYPE% per %2$s a anime.fansubs.cat: «%1$s»!',
		':new: Tenim un **nou anime** %TYPE% per %2$s a anime.fansubs.cat: **%1$s**!',
		'🎞🆕 Tenim un *nou anime* %TYPE% per %2$s a anime.fansubs.cat: *%1$s*!'
	),
	array(
		'Hi ha disponible un nou anime %TYPE% per %2$s a anime.fansubs.cat: «%1$s»!',
		':new: Hi ha disponible un **nou anime** %TYPE% per %2$s: **%1$s**!',
		'🎞🆕 Hi ha disponible un *nou anime* %TYPE% per %2$s: *%1$s*!'
	),
	array(
		'Ja podeu mirar l’anime «%1$s» %TYPE% per %2$s a anime.fansubs.cat!',
		':new: Ja podeu mirar **l’anime** **%1$s** %TYPE% per %2$s!',
		'🎞🆕 Ja podeu mirar *l’anime* *%1$s* %TYPE% per %2$s!'
	),
	array(
		'Hem afegit un nou anime %TYPE% per %2$s a anime.fansubs.cat: «%1$s»!',
		':new: Hem afegit un **nou anime** %TYPE% per %2$s: **%1$s**!',
		'🎞🆕 Hem afegit un *nou anime* %TYPE% per %2$s: *%1$s*!'
	),
	array(
		'Nou anime: «%1$s», %TYPE% per %2$s! Seguiu-lo a anime.fansubs.cat!',
		':new: **Nou anime:** **%1$s**, %TYPE% per %2$s! Seguiu-lo a Fansubs.cat!',
		'🎞🆕 *Nou anime:* *%1$s*, %TYPE% per %2$s! Seguiu-lo a Fansubs.cat!'
	)
);

$new_episode_number_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s), «%2$s». El trobareu a anime.fansubs.cat!',
		':arrow_forward: Ja hi ha disponible el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎞 Ja hi ha disponible el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s), «%2$s».'
	),
	array(
		'Hem afegit el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s), «%2$s». Mireu-lo al web d’anime.fansubs.cat!',
		':arrow_forward: Hem afegit el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎞 Hem afegit el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s), «%2$s».'
	),
	array(
		'Ja podeu mirar el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s), «%2$s». El teniu al web d’anime.fansubs.cat!',
		':arrow_forward: Ja podeu mirar el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎞 Ja podeu mirar el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s), «%2$s».'
	)
);

$new_episode_number_no_name_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s). El trobareu a anime.fansubs.cat!',
		':arrow_forward: Ja hi ha disponible el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s).',
		'🎞 Ja hi ha disponible el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s).'
	),
	array(
		'Hem afegit el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s). Mireu-lo al web d’anime.fansubs.cat!',
		':arrow_forward: Hem afegit el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s).',
		'🎞 Hem afegit el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s).'
	),
	array(
		'Ja podeu mirar el capítol %4$d de l’anime «%1$s» (%TYPE% per %3$s). El teniu al web d’anime.fansubs.cat!',
		':arrow_forward: Ja podeu mirar el **capítol %4$d** de l’anime **%1$s** (%TYPE% per %3$s).',
		'🎞 Ja podeu mirar el *capítol %4$d* de l’anime *%1$s* (%TYPE% per %3$s).'
	)
);

$new_episode_no_number_tweets = array(
	array(
		'Ja hi ha disponible un nou capítol de l’anime «%1$s» (%TYPE% per %3$s) a anime.fansubs.cat: «%2$s».',
		':arrow_forward: Ja hi ha disponible un **nou capítol** de l’anime **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎞 Ja hi ha disponible un *nou capítol* de l’anime *%1$s* (%TYPE% per %3$s): «%2$s».'
	),
	array(
		'Hem afegit un nou capítol de l’anime «%1$s» (%TYPE% per %3$s) al web d’anime.fansubs.cat: «%2$s».',
		':arrow_forward: Hem afegit un **nou capítol** de l’anime **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎞 Hem afegit un *nou capítol* de l’anime *%1$s* (%TYPE% per %3$s): «%2$s».'
	),
	array(
		'Ja podeu mirar un nou capítol de l’anime «%1$s» (%TYPE% per %3$s) a anime.fansubs.cat: «%2$s».',
		':arrow_forward: Ja podeu mirar un **nou capítol** de l’anime **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎞 Ja podeu mirar un *nou capítol* de l’anime *%1$s* (%TYPE% per %3$s): «%2$s».'
	)
);

$new_episodes_tweets = array(
	array(
		'Ja hi ha disponibles %2$d capítols nous de l’anime «%1$s» (%TYPE% per %3$s) al web d’anime.fansubs.cat!',
		':arrow_forward: Ja hi ha disponibles **%2$d capítols nous** de l’anime **%1$s** (%TYPE% per %3$s)!',
		'🎞 Ja hi ha disponibles *%2$d capítols nous* de l’anime *%1$s* (%TYPE% per %3$s)!'
	),
	array(
		'Hem afegit %2$d capítols nous de l’anime «%1$s» (%TYPE% per %3$s) al web d’anime.fansubs.cat!',
		':arrow_forward: Hem afegit **%2$d capítols nous** de l’anime **%1$s** (%TYPE% per %3$s)!',
		'🎞 Hem afegit *%2$d capítols nous* de l’anime *%1$s* (%TYPE% per %3$s)!'
	),
	array(
		'Ja podeu mirar %2$d capítols nous de l’anime «%1$s» (%TYPE% per %3$s) al web d’anime.fansubs.cat!',
		':arrow_forward: Ja podeu mirar **%2$d capítols nous** de l’anime **%1$s** (%TYPE% per %3$s)!',
		'🎞 Ja podeu mirar *%2$d capítols nous* de l’anime *%1$s* (%TYPE% per %3$s)!'
	)
);

$new_liveaction_tweets = array(
	array(
		'Tenim un nou contingut d’imatge real %TYPE% per %2$s a imatgereal.fansubs.cat: «%1$s»!',
		':new: Tenim un **nou contingut d’imatge real** %TYPE% per %2$s a imatgereal.fansubs.cat: **%1$s**!',
		'🎥🆕 Tenim un *nou contingut d’imatge real* %TYPE% per %2$s a imatgereal.fansubs.cat: *%1$s*!'
	),
	array(
		'Hi ha disponible un nou contingut d’imatge real %TYPE% per %2$s a imatgereal.fansubs.cat: «%1$s»!',
		':new: Hi ha disponible un **nou contingut d’imatge real** %TYPE% per %2$s: **%1$s**!',
		'🎥🆕 Hi ha disponible un *nou contingut d’imatge real* %TYPE% per %2$s: *%1$s*!'
	),
	array(
		'Ja podeu mirar el contingut d’imatge real «%1$s» %TYPE% per %2$s a imatgereal.fansubs.cat!',
		':new: Ja podeu mirar **el contingut d’imatge real** **%1$s** %TYPE% per %2$s!',
		'🎥🆕 Ja podeu mirar *el contingut d’imatge real* *%1$s* %TYPE% per %2$s!'
	),
	array(
		'Hem afegit un nou contingut d’imatge real %TYPE% per %2$s a imatgereal.fansubs.cat: «%1$s»!',
		':new: Hem afegit un **nou contingut d’imatge real** %TYPE% per %2$s: **%1$s**!',
		'🎥🆕 Hem afegit un *nou contingut d’imatge real* %TYPE% per %2$s: *%1$s*!'
	),
	array(
		'Nou contingut d’imatge real: «%1$s», %TYPE% per %2$s! Seguiu-lo a imatgereal.fansubs.cat!',
		':new: **Nou contingut d’imatge real:** **%1$s**, %TYPE% per %2$s! Seguiu-lo a Fansubs.cat!',
		'🎥🆕 *Nou contingut d’imatge real:* *%1$s*, %TYPE% per %2$s! Seguiu-lo a Fansubs.cat!'
	)
);

$new_liveaction_episode_number_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s), «%2$s». El trobareu a imatgereal.fansubs.cat!',
		':arrow_forward: Ja hi ha disponible el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎥 Ja hi ha disponible el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s), «%2$s».'
	),
	array(
		'Hem afegit el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s), «%2$s». Mireu-lo al web d’accióreal.fansubs.cat!',
		':arrow_forward: Hem afegit el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎥 Hem afegit el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s), «%2$s».'
	),
	array(
		'Ja podeu mirar el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s), «%2$s». El teniu al web d’accióreal.fansubs.cat!',
		':arrow_forward: Ja podeu mirar el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s), «%2$s».',
		'🎥 Ja podeu mirar el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s), «%2$s».'
	)
);

$new_liveaction_episode_number_no_name_tweets = array(
	array(
		'Ja hi ha disponible el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s). El trobareu a imatgereal.fansubs.cat!',
		':arrow_forward: Ja hi ha disponible el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s).',
		'🎥 Ja hi ha disponible el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s).'
	),
	array(
		'Hem afegit el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s). Mireu-lo al web d’accióreal.fansubs.cat!',
		':arrow_forward: Hem afegit el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s).',
		'🎥 Hem afegit el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s).'
	),
	array(
		'Ja podeu mirar el capítol %4$d del contingut d’imatge real «%1$s» (%TYPE% per %3$s). El teniu al web d’accióreal.fansubs.cat!',
		':arrow_forward: Ja podeu mirar el **capítol %4$d** del contingut d’imatge real **%1$s** (%TYPE% per %3$s).',
		'🎥 Ja podeu mirar el *capítol %4$d* del contingut d’imatge real *%1$s* (%TYPE% per %3$s).'
	)
);

$new_liveaction_episode_no_number_tweets = array(
	array(
		'Ja hi ha disponible un nou capítol del contingut d’imatge real «%1$s» (%TYPE% per %3$s) a imatgereal.fansubs.cat: «%2$s».',
		':arrow_forward: Ja hi ha disponible un **nou capítol** del contingut d’imatge real **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎥 Ja hi ha disponible un *nou capítol* del contingut d’imatge real *%1$s* (%TYPE% per %3$s): «%2$s».'
	),
	array(
		'Hem afegit un nou capítol del contingut d’imatge real «%1$s» (%TYPE% per %3$s) al web d’accióreal.fansubs.cat: «%2$s».',
		':arrow_forward: Hem afegit un **nou capítol** del contingut d’imatge real **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎥 Hem afegit un *nou capítol* del contingut d’imatge real *%1$s* (%TYPE% per %3$s): «%2$s».'
	),
	array(
		'Ja podeu mirar un nou capítol del contingut d’imatge real «%1$s» (%TYPE% per %3$s) a imatgereal.fansubs.cat: «%2$s».',
		':arrow_forward: Ja podeu mirar un **nou capítol** del contingut d’imatge real **%1$s** (%TYPE% per %3$s): «%2$s».',
		'🎥 Ja podeu mirar un *nou capítol* del contingut d’imatge real *%1$s* (%TYPE% per %3$s): «%2$s».'
	)
);

$new_liveaction_episodes_tweets = array(
	array(
		'Ja hi ha disponibles %2$d capítols nous del contingut d’imatge real «%1$s» (%TYPE% per %3$s) al web d’accióreal.fansubs.cat!',
		':arrow_forward: Ja hi ha disponibles **%2$d capítols nous** del contingut d’imatge real **%1$s** (%TYPE% per %3$s)!',
		'🎥 Ja hi ha disponibles *%2$d capítols nous* del contingut d’imatge real *%1$s* (%TYPE% per %3$s)!'
	),
	array(
		'Hem afegit %2$d capítols nous del contingut d’imatge real «%1$s» (%TYPE% per %3$s) al web d’accióreal.fansubs.cat!',
		':arrow_forward: Hem afegit **%2$d capítols nous** del contingut d’imatge real **%1$s** (%TYPE% per %3$s)!',
		'🎥 Hem afegit *%2$d capítols nous* del contingut d’imatge real *%1$s* (%TYPE% per %3$s)!'
	),
	array(
		'Ja podeu mirar %2$d capítols nous del contingut d’imatge real «%1$s» (%TYPE% per %3$s) al web d’accióreal.fansubs.cat!',
		':arrow_forward: Ja podeu mirar **%2$d capítols nous** del contingut d’imatge real **%1$s** (%TYPE% per %3$s)!',
		'🎥 Ja podeu mirar *%2$d capítols nous* del contingut d’imatge real *%1$s* (%TYPE% per %3$s)!'
	)
);

$has_posted_something = FALSE;

$result = query("SELECT s.name, s.synopsis, s.rating, v.series_id, s.subtype, s.comic_type, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, c.number, IF(ct.title IS NOT NULL, ct.title, IF(c.number IS NULL,c.description,ct.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_tweeted_manga_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_manga
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title ct ON fi.episode_id=ct.episode_id AND ct.version_id=fi.version_id
LEFT JOIN episode c ON fi.episode_id=c.id
LEFT JOIN division vo ON vo.id=c.division_id
WHERE s.type='manga' AND fi.id>$last_tweeted_manga_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$comic_type = get_comic_type($row['comic_type']);
	if ($row['new_manga']==1) {
		$random = array_rand($new_manga_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_manga_tweets[$random][0]), $row['name'], $row['fansub_handles']))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_manga_tweets[$random][0]), $row['name'], $row['fansub_mastodon_handles']))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_manga_tweets[$random][1]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'],"https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_manga_tweets[$random][2]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'],"https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_manga_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else if ($row['cnt']>1){ //Multiple chapters
		$random = array_rand($new_chapters_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapters_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_handles']))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapters_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_mastodon_handles']))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapters_tweets[$random][1]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapters_tweets[$random][2]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_manga_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else { //Single chapter
		if ($row['show_episode_numbers']==1) {
			if (!empty($row['title']) && empty($row['number'])) {
				$random = array_rand($new_chapter_no_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][1]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][2]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_manga_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else if (!empty($row['title'])) { //and has a number (normal case)
				$random = array_rand($new_chapter_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_manga_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else {
				$random = array_rand($new_chapter_number_no_name_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_no_name_tweets[$random][1]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_number_no_name_tweets[$random][2]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_manga_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			}
		} else {
			$random = array_rand($new_chapter_no_number_tweets, 1);
			try{
				if ($row['rating']<>'XXX') {
					publish_tweet(get_shortened_tweet(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_toot(get_shortened_toot(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_to_discord(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					publish_to_telegram(sprintf(str_replace('%COMIC_TYPE%', $comic_type, $new_chapter_no_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Manga en català", $row['synopsis'], "https://manga.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				}
				file_put_contents('last_tweeted_manga_id.txt', $row['id']);
			} catch(Exception $e) {
				die('Error occurred: '.$e->getMessage()."\n");
			}
		}
	}
}

$result = query("SELECT IFNULL(se.name,s.name) name, s.synopsis, s.rating, v.series_id, s.subtype, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, GROUP_CONCAT(DISTINCT f.type SEPARATOR '|') fansub_type, e.number, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_tweeted_anime_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
LEFT JOIN episode e ON fi.episode_id=e.id
LEFT JOIN division se ON se.id=e.division_id
WHERE s.type='anime' AND fi.id>$last_tweeted_anime_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$type = 'subtitulat';
	if ($row['fansub_type']=='fandub') {
		$type = 'doblat';
	}
	if ($row['new_series']==1) {
		$random = array_rand($new_anime_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_anime_tweets[$random][0]), $row['name'], $row['fansub_handles']))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_anime_tweets[$random][0]), $row['name'], $row['fansub_mastodon_handles']))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_anime_tweets[$random][1]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_anime_tweets[$random][2]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_anime_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else if ($row['cnt']>1){ //Multiple episodes
		$random = array_rand($new_episodes_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_episodes_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_handles']))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_episodes_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_mastodon_handles']))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_episodes_tweets[$random][1]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_episodes_tweets[$random][2]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_anime_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else { //Single episode
		if ($row['show_episode_numbers']==1) {
			if (!empty($row['title']) && empty($row['number'])) {
				$random = array_rand($new_episode_no_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_anime_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else if (!empty($row['title'])) { //and has a number (normal case)
				$random = array_rand($new_episode_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_episode_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_episode_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_episode_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_episode_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_anime_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else {
				$random = array_rand($new_episode_number_no_name_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_episode_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_episode_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_episode_number_no_name_tweets[$random][1]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_episode_number_no_name_tweets[$random][2]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_anime_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			}
		} else {
			$random = array_rand($new_episode_no_number_tweets, 1);
			try{
				if ($row['rating']<>'XXX') {
					publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_episode_no_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Anime en català", $row['synopsis'], "https://anime.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				}
				file_put_contents('last_tweeted_anime_id.txt', $row['id']);
			} catch(Exception $e) {
				die('Error occurred: '.$e->getMessage()."\n");
			}
		}
	}
}

$result = query("SELECT IFNULL(se.name,s.name) name, s.synopsis, s.rating, v.series_id, s.subtype, s.slug, MAX(fi.id) id, fi.version_id, COUNT(DISTINCT fi.id) cnt,GROUP_CONCAT(DISTINCT f.twitter_handle ORDER BY f.name SEPARATOR ' + ') fansub_handles,GROUP_CONCAT(DISTINCT f.mastodon_handle ORDER BY f.name SEPARATOR ' + ') fansub_mastodon_handles, GROUP_CONCAT(DISTINCT f.name SEPARATOR ' + ') fansub_names, GROUP_CONCAT(DISTINCT f.type SEPARATOR '|') fansub_type, e.number, IF(et.title IS NOT NULL, et.title, IF(e.number IS NULL,e.description,et.title)) title, s.show_episode_numbers, NOT EXISTS(SELECT fi2.id FROM file fi2 WHERE fi2.id<=$last_tweeted_liveaction_id AND fi2.version_id=fi.version_id AND fi2.is_lost=0) new_series
FROM file fi
LEFT JOIN version v ON fi.version_id=v.id
LEFT JOIN rel_version_fansub vf ON v.id=vf.version_id
LEFT JOIN fansub f ON vf.fansub_id=f.id
LEFT JOIN series s ON v.series_id=s.id
LEFT JOIN episode_title et ON fi.episode_id=et.episode_id AND et.version_id=fi.version_id
LEFT JOIN episode e ON fi.episode_id=e.id
LEFT JOIN division se ON se.id=e.division_id
WHERE s.type='liveaction' AND fi.id>$last_tweeted_liveaction_id AND fi.is_lost=0 AND fi.episode_id IS NOT NULL GROUP BY fi.version_id ORDER BY MAX(fi.id) ASC");
//This is an IF, not a WHILE, because we want to generate one piece of news on each execution. If there are more elements, they will be spaced out between executions (every 12 minutes)
if (!$has_posted_something && $row = mysqli_fetch_assoc($result)){
	$has_posted_something = TRUE;
	$type = 'subtitulat';
	if ($row['fansub_type']=='fandub') {
		$type = 'doblat';
	}
	if ($row['new_series']==1) {
		$random = array_rand($new_liveaction_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_tweets[$random][0]), $row['name'], $row['fansub_handles']))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_tweets[$random][0]), $row['name'], $row['fansub_mastodon_handles']))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_tweets[$random][1]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_tweets[$random][2]), $row['name'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else if ($row['cnt']>1){ //Multiple episodes
		$random = array_rand($new_liveaction_episodes_tweets, 1);
		try{
			if ($row['rating']<>'XXX') {
				publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episodes_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_handles']))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episodes_tweets[$random][0]), $row['name'], $row['cnt'], $row['fansub_mastodon_handles']))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
				publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episodes_tweets[$random][1]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episodes_tweets[$random][2]), $row['name'], $row['cnt'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
			}
			file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
		} catch(Exception $e) {
			die('Error occurred: '.$e->getMessage()."\n");
		}
	} else { //Single episode
		if ($row['show_episode_numbers']==1) {
			if (!empty($row['title']) && empty($row['number'])) {
				$random = array_rand($new_liveaction_episode_no_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else if (!empty($row['title'])) { //and has a number (normal case)
				$random = array_rand($new_liveaction_episode_number_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			} else {
				$random = array_rand($new_liveaction_episode_number_no_name_tweets, 1);
				try{
					if ($row['rating']<>'XXX') {
						publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_handles'], str_replace('.',',',floatval($row['number']))))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_no_name_tweets[$random][0]), $row['name'], '', $row['fansub_mastodon_handles'], str_replace('.',',',floatval($row['number']))))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
						publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_no_name_tweets[$random][1]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
						publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_number_no_name_tweets[$random][2]), $row['name'], '', $row['fansub_names'], str_replace('.',',',floatval($row['number']))), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					}
					file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
				} catch(Exception $e) {
					die('Error occurred: '.$e->getMessage()."\n");
				}
			}
		} else {
			$random = array_rand($new_liveaction_episode_no_number_tweets, 1);
			try{
				if ($row['rating']<>'XXX') {
					publish_tweet(get_shortened_tweet(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_handles']))."\nhttps://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_toot(get_shortened_toot(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][0]), $row['name'], $row['title'], $row['fansub_mastodon_handles']))."\n➡️ https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""));
					publish_to_discord(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][1]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
					publish_to_telegram(sprintf(str_replace('%TYPE%', $type, $new_liveaction_episode_no_number_tweets[$random][2]), $row['name'], $row['title'], $row['fansub_names']), $row['name']." | Fansubs.cat - Imatge real en català", $row['synopsis'], "https://imatgereal.fansubs.cat/".$row['slug'].(exists_more_than_one_version($row['series_id']) ? "?v=".$row['version_id'] : ""), "https://static.fansubs.cat/social/series_".$row['series_id'].'.jpg', $row['rating']);
				}
				file_put_contents('last_tweeted_liveaction_id.txt', $row['id']);
			} catch(Exception $e) {
				die('Error occurred: '.$e->getMessage()."\n");
			}
		}
	}
}
?>

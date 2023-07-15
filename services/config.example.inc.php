<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//Social network tokens
$facebook_api_token='YOUR_APP_TOKEN_HERE';

//Firebase Cloud Messaging key
$firebase_api_key='YOUR_FIREBASE_TOKEN_HERE';

//Twitter keys
$twitter_consumer_key='YOUR_TWITTER_KEY_HERE';
$twitter_consumer_secret='YOUR_TWITTER_SECRET_HERE';
$twitter_access_token='YOUR_TWITTER_ACCESS_TOKEN_HERE';
$twitter_access_token_secret='YOUR_TWITTER_ACCESS_SECRET_HERE';

//Mastodon keys
$mastodon_host='YOUR_MASTODON_HOST_URL';
$mastodon_access_token='YOUR_MASTODON_ACCESS_TOKEN';

//Multiple Discord webhooks are possible
$discord_webhooks = array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE'
);

//Multiple Telegram channels are posible
//The first one (index 0) MUST be the one where monthly reports get sent
$telegram_config = array(
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHAT_ID' => 'YOUR_CHAT_ID_HERE'
		'TELEGRAM_BOT_CHANNEL_CHAT_ID', 'YOUR_CHANNEL_CHAT_ID_HERE'
	)
);

//Token to bypass admin authentication in twitter_image.php
$internal_token='YOUR_TOKEN';

//Files
$lock_file='/tmp/fansubscat_fetch_lock';

//Lock file for MEGA sync processes
$mega_lock_file='/tmp/mega_fetch.lock';

//Paths (no end slash)
$static_directory='/srv/websites/static.fansubs.cat';
?>

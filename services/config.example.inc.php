<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

//Database access
define('DB_HOST', 'YOUR_DB_HOST_HERE');
define('DB_NAME', 'YOUR_DB_NAME_HERE');
define('DB_USER', 'YOUR_DB_USER_HERE');
define('DB_PASSWORD', 'YOUR_DB_PASS_HERE');
define('DB_CHARSET', 'utf8mb4');

//Website URLs (no final slash)
define('STATIC_URL', 'https://static.fansubs.cat');
define('ADMIN_URL', 'https://admin.fansubs.cat');

//Internal path (no final slash)
define('STATIC_DIRECTORY', '/srv/websites/static.fansubs.cat');

//Firebase Cloud Messaging key
define('FIREBASE_API_KEY', 'YOUR_FIREBASE_TOKEN_HERE');

//Twitter keys
define('TWITTER_CONSUMER_KEY', 'YOUR_TWITTER_KEY_HERE');
define('TWITTER_CONSUMER_SECRET', 'YOUR_TWITTER_SECRET_HERE');
define('TWITTER_ACCESS_TOKEN', 'YOUR_TWITTER_ACCESS_TOKEN_HERE');
define('TWITTER_ACCESS_TOKEN_SECRET', 'YOUR_TWITTER_ACCESS_SECRET_HERE');
define('TWITTER_CONSUMER_KEY_HENTAI', 'YOUR_TWITTER_KEY_HERE');
define('TWITTER_CONSUMER_SECRET_HENTAI', 'YOUR_TWITTER_SECRET_HERE');
define('TWITTER_ACCESS_TOKEN_HENTAI', 'YOUR_TWITTER_ACCESS_TOKEN_HERE');
define('TWITTER_ACCESS_TOKEN_SECRET_HENTAI', 'YOUR_TWITTER_ACCESS_SECRET_HERE');

//Mastodon keys
define('MASTODON_HOST', 'YOUR_MASTODON_HOST_URL');
define('MASTODON_ACCESS_TOKEN', 'YOUR_MASTODON_ACCESS_TOKEN');
define('MASTODON_HOST_HENTAI', 'YOUR_MASTODON_HOST_URL');
define('MASTODON_ACCESS_TOKEN_HENTAI', 'YOUR_MASTODON_ACCESS_TOKEN');

//Multiple Discord webhooks are possible
define('DISCORD_WEBHOOKS', array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE'
));
define('DISCORD_WEBHOOKS_HENTAI', array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE'
));

//Multiple Telegram channels are possible
//The first one (index 0) MUST be the one where monthly reports get sent
define('TELEGRAM_CONFIG', array(
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHAT_ID' => 'YOUR_CHAT_ID_HERE'
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE'
	)
));
define('TELEGRAM_CONFIG_HENTAI', array(
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHAT_ID' => 'YOUR_CHAT_ID_HERE'
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE'
	)
));

//BlueSky keys
define('BLUESKY_HANDLE', 'YOUR_HANDLE_HERE');
define('BLUESKY_APP_PASSWORD', 'YOUR_APP_PASSWORD_HERE');
define('BLUESKY_HANDLE_HENTAI', 'YOUR_HANDLE_HERE');
define('BLUESKY_APP_PASSWORD_HENTAI', 'YOUR_APP_PASSWORD_HERE');

//Token to bypass admin authentication in twitter_image.php
define('INTERNAL_SERVICES_TOKEN', 'YOUR_TOKEN');

//Files
define('LOCK_FILE', '/tmp/fansubscat_fetch_lock');

//Lock file for MEGA sync processes
define('MEGA_LOCK_FILE', '/tmp/mega_fetch.lock');
?>

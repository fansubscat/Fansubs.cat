<?php
//This is an example config file. Edit it accordingly and rename it to "config.inc.php".
//If you not do this, the software will crash unexpectedly.

//We include the system defaults which should not be changed in normal conditions.
require_once(__DIR__.'/defaults.inc.php');

//Database access parameters
define('DB_HOST', 'dbhost');
define('DB_NAME', 'dbname');
define('DB_USER', 'dbuser');
define('DB_PASSWORD', 'dbpassword');

//Domains
//This software requires a domain for general content and another domain for explicit content
//If your site does not contain explicit content, just use a non-existant domain name in HENTAI_DOMAIN
define('MAIN_DOMAIN', 'maindomain.xyz');
define('HENTAI_DOMAIN', 'hentaidomain.xyz');

//Site parameters for the main domain:
define('MAIN_SITE_NAME', 'My custom Fansubs.cat site');
define('MAIN_SOCIAL_LINK_BLUESKY', 'https://bsky.app/profile/myhandle.bsky.social');
define('MAIN_SOCIAL_LINK_MASTODON', 'https://mastodon.social/@myhandle');
define('MAIN_SOCIAL_LINK_TELEGRAM', 'https://t.me/myhandle');
define('MAIN_SOCIAL_LINK_X', 'https://x.com/myhandle');

//Site parameters for the hentai domain:
define('HENTAI_SITE_NAME', 'My custom Fansubs.cat hentai site');
define('HENTAI_SOCIAL_LINK_BLUESKY', 'https://bsky.app/profile/myhentaihandle.bsky.social');
define('HENTAI_SOCIAL_LINK_MASTODON', 'https://mastodon.social/@myhentaihandle');
define('HENTAI_SOCIAL_LINK_TELEGRAM', 'https://t.me/myhentaihandle');
define('HENTAI_SOCIAL_LINK_X', 'https://x.com/myhentaihandle');

//Subdomains:
define('MAIN_SUBDOMAIN', 'www');
define('ADVENT_SUBDOMAIN', 'advent');
define('ANIME_SUBDOMAIN', 'anime');
define('MANGA_SUBDOMAIN', 'manga');
define('LIVEACTION_SUBDOMAIN', 'liveaction');
define('NEWS_SUBDOMAIN', 'news');
define('RESOURCES_SUBDOMAIN', 'resources');
define('USERS_SUBDOMAIN', 'users');
define('STATIC_SUBDOMAIN', 'static');
define('ADMIN_SUBDOMAIN', 'admin');
define('API_SUBDOMAIN', 'api');
define('STATUS_SUBDOMAIN', 'status');

//Used to check internal API calls only (bypass admin authentication in twitter_image.php)
define('INTERNAL_SERVICES_TOKEN', 'YOUR_TOKEN_HERE');

//Populate this variable if you want to display a message on all listing pages
define('GLOBAL_MESSAGE', '');

//Start year and date of your site
define('STARTING_YEAR', 2020);
define('STARTING_DATE', '2020-06-01');

//Start date of the news in the database
define('NEWS_STARTING_MONTH', '2020-06');

//Features that can be disabled
define('DISABLE_NEWS', TRUE);
define('DISABLE_LINKS', TRUE);
define('DISABLE_LIVE_ACTION', FALSE);
define('DISABLE_ADVENT', TRUE);
define('DISABLE_RESOURCES', TRUE);
define('DISABLE_FOOLS_DAY', TRUE);
define('DISABLE_SANT_JORDI_DAY', TRUE);
define('DISABLE_HALLOWEEN_DAYS', FALSE);
define('DISABLE_CHRISTMAS_DAYS', FALSE);
define('DISABLE_STATUS', FALSE);

//Default language of your site (ISO code)
//Files for it MUST exist in the following places:
// - common/languages/lang_<code>.json
// - websites/static/js/lang_<code>.js
// - websites/static/js/videojs-lang_<code>.js
//The system locale must also be installed on your system.
define('SITE_LANGUAGE', 'ca');
define('SITE_LOCALE', 'ca_AD.utf8');

//What to use to send e-mails
//The code assumes that you use a SMTP server with user/pass login and SMTPS support.
//If your e-mail provider differs, you might need to change the code in common.inc.php's send_mail() function accordingly.
define('SMTP_HOST', 'your.smtp.host');
define('SMTP_USERNAME', 'smtpusername');
define('SMTP_PASSWORD', 'smtppassword');
define('SMTP_PORT', 1234);
define('EMAIL_FROM_ADDRESS', 'your@address.xyz');
define('EMAIL_FROM_NAME', 'My custom Fansubs.cat site');

//These domains do not allow our e-mails, just block registrations
define('BLACKLISTED_EMAIL_DOMAINS', array(
	'example.com',
));

//Storages
define('STORAGES', array(
	'https://your.storageserver.xyz/',
));

//Link to the admin tutorial
define('ADMIN_TUTORIAL_URL', 'https://www.yourtutorial.xyz/tutorial');

//Password salt for hashes (used only for the admin site)
define('PASSWORD_SALT', 'YOUR_PASSWORD_SALT_HERE');

//API key for Multiavatar - used only in the admin section
define('MULTIAVATAR_API_KEY', 'YOUR_API_KEY');

//Firebase Cloud Messaging key (for sending push messages to the old Android app)
define('FIREBASE_API_KEY', 'YOUR_FIREBASE_TOKEN_HERE');

//Twitter keys (for posting new messages regarding published content)
//General content
define('TWITTER_CONSUMER_KEY', 'YOUR_TWITTER_KEY_HERE');
define('TWITTER_CONSUMER_SECRET', 'YOUR_TWITTER_SECRET_HERE');
define('TWITTER_ACCESS_TOKEN', 'YOUR_TWITTER_ACCESS_TOKEN_HERE');
define('TWITTER_ACCESS_TOKEN_SECRET', 'YOUR_TWITTER_ACCESS_SECRET_HERE');
//Explicit content
define('TWITTER_CONSUMER_KEY_HENTAI', 'YOUR_TWITTER_KEY_HERE');
define('TWITTER_CONSUMER_SECRET_HENTAI', 'YOUR_TWITTER_SECRET_HERE');
define('TWITTER_ACCESS_TOKEN_HENTAI', 'YOUR_TWITTER_ACCESS_TOKEN_HERE');
define('TWITTER_ACCESS_TOKEN_SECRET_HENTAI', 'YOUR_TWITTER_ACCESS_SECRET_HERE');

//BlueSky keys (for posting new messages regarding published content)
//General content
define('BLUESKY_HANDLE', 'YOUR_HANDLE_HERE');
define('BLUESKY_APP_PASSWORD', 'YOUR_APP_PASSWORD_HERE');
//Explicit content
define('BLUESKY_HANDLE_HENTAI', 'YOUR_HANDLE_HERE');
define('BLUESKY_APP_PASSWORD_HENTAI', 'YOUR_APP_PASSWORD_HERE');

//Mastodon keys (for posting new messages regarding published content)
//General content
define('MASTODON_HOST', 'YOUR_MASTODON_HOST_URL');
define('MASTODON_ACCESS_TOKEN', 'YOUR_MASTODON_ACCESS_TOKEN');
//Explicit content
define('MASTODON_HOST_HENTAI', 'YOUR_MASTODON_HOST_URL');
define('MASTODON_ACCESS_TOKEN_HENTAI', 'YOUR_MASTODON_ACCESS_TOKEN');

//Multiple Telegram channels are possible (for posting new messages regarding published content)
//The first one (index 0)'s chat id will only receive monthly reports (intended as an admin group)
//General content
define('TELEGRAM_CONFIG', array(
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHAT_ID' => 'YOUR_INTERNAL_CHAT_ID_HERE',
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE',
	),
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE',
	),
));
//Explicit content
define('TELEGRAM_CONFIG_HENTAI', array(
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHAT_ID' => 'YOUR_INTERNAL_CHAT_ID_HERE',
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE',
	),
	array(
		'TELEGRAM_BOT_API_KEY' => 'YOUR_API_KEY_HERE',
		'TELEGRAM_BOT_CHANNEL_CHAT_ID' => 'YOUR_CHANNEL_CHAT_ID_HERE',
	),
));

//Multiple Discord webhooks are possible (for posting new messages regarding published content)
//General content
define('DISCORD_WEBHOOKS', array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE',
));
//Explicit content
define('DISCORD_WEBHOOKS_HENTAI', array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE',
));

//Storage URL customization
function generate_storage_url($url) {
	//You can use your custom code here
	//If you don't know what this is, just leave it like it is by default.
	return $url;
}
?>

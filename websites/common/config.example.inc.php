<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

define('VERSION', '5.1.3');

//Database access
define('DB_HOST', 'YOUR_DB_HOST_HERE');
define('DB_NAME', 'YOUR_DB_NAME_HERE');
define('DB_USER', 'YOUR_DB_USER_HERE');
define('DB_PASSWORD', 'YOUR_DB_PASS_HERE');
define('DB_CHARSET', 'utf8mb4');

//Host treatment (needed for normal/hentai site)
define('MAIN_DOMAIN', 'fansubs.cat');
define('HENTAI_DOMAIN', 'hentai.cat');
if (str_ends_with(strtolower($_SERVER['HTTP_HOST']), HENTAI_DOMAIN)) {
	define('CURRENT_DOMAIN', HENTAI_DOMAIN);
	define('CURRENT_SITE_NAME', 'Hentai.cat');
	define('SITE_IS_HENTAI', TRUE);
	define('SOCIAL_LINK_BLUESKY', 'https://bsky.app/profile/hentaipuntcat.bsky.social');
	define('SOCIAL_LINK_MASTODON', 'https://mastodont.cat/@hentaipuntcat');
	define('SOCIAL_LINK_TELEGRAM', 'https://t.me/hentaipuntcat');
	define('SOCIAL_LINK_X', 'https://x.com/hentaipuntcat');
} else {
	define('CURRENT_DOMAIN', MAIN_DOMAIN);
	define('CURRENT_SITE_NAME', 'Fansubs.cat');
	define('SITE_IS_HENTAI', FALSE);
	define('SOCIAL_LINK_BLUESKY', 'https://bsky.app/profile/fansubscat.bsky.social');
	define('SOCIAL_LINK_MASTODON', 'https://mastodont.cat/@fansubscat');
	define('SOCIAL_LINK_TELEGRAM', 'https://t.me/fansubscat');
	define('SOCIAL_LINK_X', 'https://x.com/fansubscat');
}

//Website URLs (no final slash)
define('MAIN_URL', 'https://www.'.CURRENT_DOMAIN);
define('ADVENT_URL', 'https://advent.'.CURRENT_DOMAIN);
define('ANIME_URL', 'https://anime.'.CURRENT_DOMAIN);
define('MANGA_URL', 'https://manga.'.CURRENT_DOMAIN);
define('LIVEACTION_URL', 'https://imatgereal.'.CURRENT_DOMAIN);
define('NEWS_URL', 'https://noticies.'.CURRENT_DOMAIN);
define('RESOURCES_URL', 'https://recursos.'.CURRENT_DOMAIN);
define('USERS_URL', 'https://usuaris.'.CURRENT_DOMAIN);
define('STATIC_URL', 'https://static.'.CURRENT_DOMAIN);
define('ADMIN_URL', 'https://admin.'.CURRENT_DOMAIN);
define('API_URL', 'https://api.'.CURRENT_DOMAIN);

//Internal paths (no final slash)
define('SERVICES_DIRECTORY', '/srv/services/fansubs.cat');
define('STATIC_DIRECTORY', '/srv/websites/static.fansubs.cat');

//Cookie params
define('COOKIE_NAME', 'session_id');
define('COOKIE_DURATION', 60*60*24*365*10);
define('COOKIE_DOMAIN', '.'.CURRENT_DOMAIN);
define('ADMIN_COOKIE_NAME', 'admin_session_id');
define('ADMIN_COOKIE_DURATION', 60*60*24*30);
define('ADMIN_COOKIE_DOMAIN', '.'.CURRENT_DOMAIN);

//Used to check internal API calls only
define('INTERNAL_SERVICES_TOKEN', 'YOUR_TOKEN_HERE');

//Populate this variable if you want to display a message on all listing pages
define('GLOBAL_MESSAGE', '');

//What to use as sender when sending e-mails
define('EMAIL_ACCOUNT', 'info@fansubs.cat');

//These domains do not allow our e-mails, just block registrations
define('BLACKLISTED_EMAIL_DOMAINS', array('example.com'));

//Storages
define('STORAGES', array(
	'https://YOUR_STORAGE_SERVERS/'
));

//Link to the admin tutorial
define('ADMIN_TUTORIAL_URL', 'YOUR_TUTORIAL_URL');

//Token to bypass admin authentication in twitter_image.php
define('INTERNAL_TOKEN', 'YOUR_TOKEN');

//Lock file for MEGA sync processes (shared with background services)
define('MEGA_LOCK_FILE', '/tmp/mega_fetch.lock');

//Password salt for hashes (used only for the admin site)
define('PASSWORD_SALT', 'YOUR_PASSWORD_SALT');

//Storages (used only for the admin site), array of arrays
define('ADMIN_STORAGES', array(
//		array(
//			'hostname' => 'whatever.xyz',
//			'base_url' => 'https://www.whatever.xyz/files/',
//			'api_url' => 'https://whatever.api/endpoint/',
//			'api_username' => 'my_user',
//			'api_password' => 'my_password'
//		)
	)
);

//API key for Multiavatar - used only in the admin section
define('MULTIAVATAR_API_KEY', 'YOUR_API_KEY');

//Storage URL customization
function generate_storage_url($url) {
	//Your custom code
	return $url;
}

//Keep this at the end
require_once('config_per_site.inc.php');
?>

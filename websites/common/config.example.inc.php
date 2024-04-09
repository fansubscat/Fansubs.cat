<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

define('VERSION', '5.0.14');

//Database access
define('DB_HOST', 'YOUR_DB_HOST_HERE');
define('DB_NAME', 'YOUR_DB_NAME_HERE');
define('DB_USER', 'YOUR_DB_USER_HERE');
define('DB_PASSWORD', 'YOUR_DB_PASS_HERE');
define('DB_CHARSET', 'utf8mb4');

//Website URLs (no final slash)
define('MAIN_URL', 'https://www.fansubs.cat');
define('ADVENT_URL', 'https://advent.fansubs.cat');
define('ANIME_URL', 'https://anime.fansubs.cat');
define('MANGA_URL', 'https://manga.fansubs.cat');
define('LIVEACTION_URL', 'https://imatgereal.fansubs.cat');
define('NEWS_URL', 'https://noticies.fansubs.cat');
define('RESOURCES_URL', 'https://recursos.fansubs.cat');
define('USERS_URL', 'https://usuaris.fansubs.cat');
define('STATIC_URL', 'https://static.fansubs.cat');
define('ADMIN_URL', 'https://admin.fansubs.cat');
define('API_URL', 'https://api.fansubs.cat');
define('HENTAI_URL', 'https://hentai.fansubs.cat');
define('HENTAI_ANIME_URL', 'https://hentai.fansubs.cat/anime');
define('HENTAI_MANGA_URL', 'https://hentai.fansubs.cat/manga');

//Internal paths (no final slash)
define('SERVICES_DIRECTORY', '/srv/services/fansubs.cat');
define('STATIC_DIRECTORY', '/srv/websites/static.fansubs.cat');

//Cookie params
define('COOKIE_NAME', 'session_id');
define('COOKIE_DURATION', 60*60*24*365*10);
define('COOKIE_DOMAIN', '.fansubs.cat');
define('ADMIN_COOKIE_NAME', 'admin_session_id');
define('ADMIN_COOKIE_DURATION', 60*60*24*30);
define('ADMIN_COOKIE_DOMAIN', '.fansubs.cat');

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

<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"
require_once('config_per_site.inc.php');

$version='5.0.0.wip';

//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//Website URLs (no final slash)
$main_url='https://www.fansubs.cat';
$advent_url='https://advent.fansubs.cat';
$anime_url='https://anime.fansubs.cat';
$manga_url='https://manga.fansubs.cat';
$liveaction_url='https://accioreal.fansubs.cat';
$news_url='https://noticies.fansubs.cat';
$static_url='https://static.fansubs.cat';
$users_url='https://usuaris.fansubs.cat';

//Internal path (no final slash)
$static_directory='/srv/websites/static.fansubs.cat';

//Memcached access (for storing remote requests cache)
$memcached_host='YOUR_MEMCACHED_HOST_HERE';
$memcached_port=YOUR_MEMCACHED_PORT_HERE;
$memcached_expiry_time=12*3600;

//Google Drive API key
$google_drive_api_key='YOUR_GOOGLE_DRIVE_API_KEY_HERE';

//Cookie params
$cookie_duration = 60*60*24*365*10;
$cookie_domain = ".fansubs.cat";

//Specific data
$default_fansub_id=28; //"Fansub independent"

//Populate this variable if you want to display a message on all listing pages
$site_message="";

//Storages
$storages = array(
	'https://YOUR_STORAGE_SERVERS/'
);

//Storage URL customization
function generate_storage_url($url) {
	//Your custom code
	return $url;
}
?>

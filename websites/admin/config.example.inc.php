<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

//Version shown in the main menu
$version_name='VERSION';

//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//This site (without final slash)
$base_url="https://admin.fansubs.cat";

//Other sites
$main_url="https://www.fansubs.cat";
$anime_url="https://anime.fansubs.cat";
$manga_url="https://manga.fansubs.cat";
$liveaction_url="https://accioreal.fansubs.cat";
$news_url="https://noticies.fansubs.cat";
$resources_url="https://recursos.fansubs.cat";
$advent_url="https://advent.fansubs.cat";
$static_url="https://static.fansubs.cat";
$tutorial_url="YOUR_TUTORIAL_URL";

//Static data directory (*NO* final slash)
$static_directory="/srv/websites/static.fansubs.cat";

//Specific data
$default_fansub_id=28; //"Fansub independent"

//Token to bypass admin authentication in twitter_image.php
$internal_token='YOUR_TOKEN';

//Memcached access (for storing remote requests cache)
$memcached_host='YOUR_MEMCACHED_HOST_HERE';
$memcached_port=YOUR_MEMCACHED_PORT_HERE;
$memcached_expiry_time=12*3600;

//Lock file for MEGA sync processes
$mega_lock_file='/tmp/mega_fetch.lock';

//Password salt for hashes
$password_salt='YOUR_PASSWORD_SALT_HERE';

//Storages
$storages = array(
	//Array of arrays, example:
//	array(
//		'hostname' => 'whatever.xyz',
//		'base_url' => 'https://www.whatever.xyz/files/',
//		'api_url' => 'https://whatever.api/endpoint/',
//		'api_username' => 'my_user',
//		'api_password' => 'my_password'
//	)
);

//Storage URL customization
function generate_storage_url($url) {
	return $url;
}
?>

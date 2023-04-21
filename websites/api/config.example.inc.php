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

//Internal paths (no final slash)
define('SERVICES_DIRECTORY', '/srv/services/fansubs.cat');
define('STATIC_DIRECTORY', '/srv/websites/static.fansubs.cat');

//Used to check internal calls only
define('INTERNAL_SERVICES_TOKEN', 'YOUR_INTERNAL_TOKEN_HERE');

//Storages
define('STORAGES', array(
	'https://YOUR_STORAGE_SERVERS/'
));

//Storage URL customization
function generate_storage_url($url) {
	//Your custom code
	return $url;
}
?>

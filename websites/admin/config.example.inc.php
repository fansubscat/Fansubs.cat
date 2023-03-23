<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

require_once("../common.fansubs.cat/config.inc.php");

define('ADMIN_TUTORIAL_URL', 'YOUR_TUTORIAL_URL');

//Token to bypass admin authentication in twitter_image.php
define('INTERNAL_TOKEN', 'YOUR_TOKEN');

//Lock file for MEGA sync processes
define('MEGA_LOCK_FILE', '/tmp/mega_fetch.lock');

//Password salt for hashes
define('PASSWORD_SALT', 'YOUR_PASSWORD_SALT');

//Storages
define('ADMIN_STORAGES', array(
		//Array of arrays, example:
//		array(
//			'hostname' => 'whatever.xyz',
//			'base_url' => 'https://www.whatever.xyz/files/',
//			'api_url' => 'https://whatever.api/endpoint/',
//			'api_username' => 'my_user',
//			'api_password' => 'my_password'
//		)
	)
);
?>

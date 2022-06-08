<?php
//This is an example file. Edit it accordingly and rename it to "config.inc.php"

//Database access
$db_host="YOUR_DB_HOST_HERE";
$db_name="YOUR_DB_NAME_HERE";
$db_user="YOUR_DB_USER_HERE";
$db_passwd="YOUR_DB_PASS_HERE";

//Manga database access
$db_host_manga="YOUR_MANGA_DB_HOST_HERE";
$db_name_manga="YOUR_MANGA_DB_NAME_HERE";
$db_user_manga="YOUR_MANGA_DB_USER_HERE";
$db_passwd_manga="YOUR_MANGA_DB_PASS_HERE";

//Social network tokens
$facebook_api_token='YOUR_APP_TOKEN_HERE';

//Firebase Cloud Messaging key
$firebase_api_key='YOUR_FIREBASE_TOKEN_HERE';

//Twitter keys
$twitter_consumer_key='YOUR_TWITTER_KEY_HERE';
$twitter_consumer_secret='YOUR_TWITTER_SECRET_HERE';
$twitter_access_token='YOUR_TWITTER_ACCESS_TOKEN_HERE';
$twitter_access_token_secret='YOUR_TWITTER_ACCESS_SECRET_HERE';

//Multiple Discord webhooks are possible
$discord_webhooks = array(
	'YOUR_DISCORD_WEBHOOK_1_HERE',
	'YOUR_DISCORD_WEBHOOK_2_HERE'
);

//Files
$lock_file='/tmp/fansubscat_fetch_lock';

//Lock file for MEGA sync processes
$mega_lock_file='/tmp/mega_fetch.lock';

//Paths (no end slash)
$static_directory='/srv/websites/static.fansubs.cat';
?>

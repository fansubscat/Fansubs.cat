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

//Cookie params
$cookie_duration = 60*60*24*365*10;
$cookie_domain = ".fansubs.cat";
?>

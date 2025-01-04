<?php
//This file defines default system values that should not be changed.
//If you edit this, be sure of what you are doing and check the code that uses these: you might break things!

//Software version
define('VERSION', '5.3.0-DEV');

//Database charset
define('DB_CHARSET', 'utf8mb4');

//Cookie params
define('COOKIE_NAME', 'session_id');
define('COOKIE_DURATION', 60*60*24*365*10);
define('ADMIN_COOKIE_NAME', 'admin_session_id');
define('ADMIN_COOKIE_DURATION', 60*60*24*30);

//Internal paths (no final slash)
define('SERVICES_DIRECTORY', '/srv/fansubscat/services');
define('STATIC_DIRECTORY', '/srv/fansubscat/websites/static');

//Lock file to avoid parallel invocations of the news fetcher
define('LOCK_FILE', '/srv/fansubscat/locks/news_fetch_lock');

//Lock file for MEGA sync processes
define('MEGA_LOCK_FILE', '/srv/fansubscat/locks/mega_fetch_lock');
?>

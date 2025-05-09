<?php
require_once(__DIR__.'/../common/user_init.inc.php');

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.MAIN_URL);
	die();
}

define('PAGE_TITLE', lang('users.login.page_title'));
define('PAGE_PATH', lang('url.login'));
define('PAGE_STYLE_TYPE', 'login');

require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/../common/footer.inc.php');
?>

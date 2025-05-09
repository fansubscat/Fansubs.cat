<?php
require_once(__DIR__.'/../common/user_init.inc.php');

//Kick the user if already logged in
if (!empty($user)) {
	header('Location: '.MAIN_URL);
	die();
}

define('PAGE_TITLE', lang('users.reset_password.page_title'));
define('PAGE_PATH', lang('url.reset_password'));
define('PAGE_STYLE_TYPE', 'login');
define('PAGE_IS_RESET_PASSWORD', TRUE);

require_once(__DIR__.'/../common/header.inc.php');
require_once(__DIR__.'/../common/footer.inc.php');
?>

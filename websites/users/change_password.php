<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL.lang('url.login'));
	die();
}

define('PAGE_TITLE', lang('users.change_password.page_title'));
define('PAGE_PATH', lang('url.change_password'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once(__DIR__.'/../common/header.inc.php');
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header"><?php echo lang('users.change_password.header'); ?></div>
		<div class="profile-section-explanation"><?php echo lang('users.change_password.explanation'); ?></div>
		<form id="change-password-form" onsubmit="return changePassword();" autocomplete="off" novalidate="">
			<label for="change_password_old_password"><?php echo lang('users.change_password.old_password'); ?></label>
			<input id="change_password_old_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_old_password_validation" class="validation-message"></div>
			<label for="change_password_password"><?php echo lang('users.change_password.new_password'); ?></label>
			<input id="change_password_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_password_validation" class="validation-message"></div>
			<label for="change_password_repeat_password"><?php echo lang('users.change_password.repeat_password'); ?></label>
			<input id="change_password_repeat_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_repeat_password_validation" class="validation-message"></div>
			<div id="change_password_generic_validation" class="validation-message-generic"></div>
			<button id="change_password_submit" type="submit" class="login-button account-button"><?php echo lang('users.change_password.confirm_button'); ?></button>
		</form>
	</div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

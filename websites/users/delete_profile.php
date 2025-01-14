<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL.lang('url.login'));
	die();
}

define('PAGE_TITLE', lang('users.delete_profile.page_title'));
define('PAGE_PATH', lang('url.delete_profile'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once(__DIR__.'/../common/header.inc.php');
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header"><?php echo lang('users.delete_profile.header'); ?></div>
		<div class="profile-section-explanation"><?php echo sprintf(lang('users.delete_profile.explanation'), CURRENT_SITE_NAME_ACCOUNT); ?></div>
		<form id="delete-profile-form" onsubmit="return deleteProfile();" autocomplete="off" novalidate="">
			<label for="delete_profile_password"><?php echo lang('users.delete_profile.password'); ?></label>
			<input id="delete_profile_password" type="password" oninput="removeValidation(this.id);">
			<div id="delete_profile_password_validation" class="validation-message"></div>
			<div id="delete_profile_generic_validation" class="validation-message-generic"></div>
			<button id="delete_profile_submit" type="submit" class="login-button account-button"><?php echo lang('users.delete_profile.confirm_button'); ?></button>
		</form>
	</div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

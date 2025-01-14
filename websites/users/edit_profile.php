<?php
require_once(__DIR__.'/../common/user_init.inc.php');
require_once(__DIR__.'/../common/common.inc.php');
require_once(__DIR__.'/queries.inc.php');

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL.lang('url.login'));
	die();
}

define('PAGE_TITLE', lang('users.edit_profile.page_title'));
define('PAGE_PATH', lang('url.edit_profile'));
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once(__DIR__.'/../common/header.inc.php');
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header"><?php echo lang('users.edit_profile.header'); ?></div>
		<form id="edit-profile-form" onsubmit="return editProfile();" autocomplete="off" novalidate="">
			<label for=edit_profile_username"><?php echo lang('users.edit_profile.username'); ?></label>
			<input id="edit_profile_username" type="text" oninput="removeValidation(this.id);" value="<?php echo htmlspecialchars($user['username']); ?>">
			<div id="edit_profile_username_validation" class="validation-message"></div>
			<label for=edit_profile_email"><?php echo lang('users.edit_profile.email'); ?></label>
			<input id="edit_profile_email" type="email" oninput="removeValidation(this.id);" value="<?php echo htmlspecialchars($user['email']); ?>">
			<div id="edit_profile_email_validation" class="validation-message"></div>
			<label for="edit_profile_birthday_day"><?php echo lang('users.edit_profile.birth_date'); ?></label>
			<div class="date-chooser">
				<input class="date-day" id="edit_profile_birthday_day" type="text" maxlength="2" oninput="removeValidationOnlyText('edit_profile_birthday');" placeholder="<?php echo lang('users.edit_profile.day'); ?>" value="<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'd'); ?>">
				<select class="date-month" id="edit_profile_birthday_month" onchange="removeValidationOnlyText('edit_profile_birthday');">
<?php
	for ($i=1;$i<=12;$i++) {
		if ($i<10) {
			$month='0'.$i;
		} else {
			$month=$i;
		}
?>
					<option value="<?php echo $month; ?>"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==$i ? ' selected' : ''; ?>><?php echo lang('users.edit_profile.month.'.$month); ?></option>
<?php
	}
?>
				</select>
				<input class="date-year" id="edit_profile_birthday_year" type="text" maxlength="4" oninput="removeValidationOnlyText('edit_profile_birthday');" placeholder="<?php echo lang('users.edit_profile.year'); ?>" value="<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'Y'); ?>">
			</div>
			<div id="edit_profile_birthday_validation" class="validation-message"></div>
			<label for="edit_profile_avatar_file"><?php echo lang('users.edit_profile.profile_image'); ?></label>
			<div class="profile-avatar">
				<img alt="<?php echo lang('users.edit_profile.profile_image.alt'); ?>" onclick="" class="profile-avatar-image" src="<?php echo !empty($user['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$user['avatar_filename'] : STATIC_URL.'/images/site/default_avatar.jpg'; ?>">
				<input id="edit_profile_avatar_file" class="hidden" name="file" type="file" onchange="checkAvatarUpload();" accept="image/png, image/gif, image/jpeg" />
				<div class="profile-avatar-change" onclick="chooseAvatar();"><i class="fa fa-fw fa-upload"></i><?php echo lang('users.edit_profile.change_image'); ?></div>
			</div>
			<div id="edit_profile_generic_validation" class="validation-message-generic"></div>
			<button id="edit_profile_submit" type="submit" class="login-button account-button"><?php echo lang('users.edit_profile.save_button'); ?></button>
		</form>
	</div>
</div>
<?php
require_once(__DIR__.'/../common/footer.inc.php');
?>

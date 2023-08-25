<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'Edita el perfil');
define('PAGE_PATH', '/edita-el-perfil');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once("../common.fansubs.cat/header.inc.php");
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header">Edita el perfil</div>
		<form id="edit-profile-form" onsubmit="return editProfile();" autocomplete="off" novalidate="">
			<label for=edit_profile_email">Adreça electrònica</label>
			<input id="edit_profile_email" type="email" oninput="removeValidation(this.id);" value="<?php echo htmlspecialchars($user['email']); ?>">
			<div id="edit_profile_email_validation" class="validation-message"></div>
			<label for="edit_profile_birthday_day">Data de naixement</label>
			<div class="date-chooser">
				<input class="date-day" id="edit_profile_birthday_day" type="text" maxlength="2" oninput="removeValidationOnlyText('edit_profile_birthday');" placeholder="Dia" value="<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'd'); ?>">
				<select class="date-month" id="edit_profile_birthday_month" onchange="removeValidationOnlyText('edit_profile_birthday');">
					<option value="01"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==1 ? ' selected' : ''; ?>>gener</option>
					<option value="02"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==2 ? ' selected' : ''; ?>>febrer</option>
					<option value="03"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==3 ? ' selected' : ''; ?>>març</option>
					<option value="04"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==4 ? ' selected' : ''; ?>>abril</option>
					<option value="05"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==5 ? ' selected' : ''; ?>>maig</option>
					<option value="06"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==6 ? ' selected' : ''; ?>>juny</option>
					<option value="07"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==7 ? ' selected' : ''; ?>>juliol</option>
					<option value="08"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==8 ? ' selected' : ''; ?>>agost</option>
					<option value="09"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==9 ? ' selected' : ''; ?>>setembre</option>
					<option value="10"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==10 ? ' selected' : ''; ?>>octubre</option>
					<option value="11"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==11 ? ' selected' : ''; ?>>novembre</option>
					<option value="12"<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'm')==12 ? ' selected' : ''; ?>>desembre</option>
				</select>
				<input class="date-year" id="edit_profile_birthday_year" type="text" maxlength="4" oninput="removeValidationOnlyText('edit_profile_birthday');" placeholder="Any" value="<?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'Y'); ?>">
			</div>
			<div id="edit_profile_birthday_validation" class="validation-message"></div>
			<label for="edit_profile_avatar_file">Imatge de perfil</label>
			<div class="profile-avatar">
				<img alt="Avatar de l’usuari" onclick="" class="profile-avatar-image" src="<?php echo !empty($user['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$user['avatar_filename'] : STATIC_URL.'/images/site/default_avatar.jpg'; ?>">
				<input id="edit_profile_avatar_file" name="file" type="file" onchange="checkAvatarUpload();" accept="image/png, image/gif, image/jpeg" />
				<div class="profile-avatar-change" onclick="chooseAvatar();"><i class="fa fa-fw fa-upload"></i>Canvia la imatge</div>
			</div>
			<div id="edit_profile_generic_validation" class="validation-message-generic"></div>
			<button id="edit_profile_submit" type="submit" class="login-button account-button">Desa els canvis</button>
		</form>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");
require_once("queries.inc.php");

validate_hentai();

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'Canvia la contrasenya');
define('PAGE_PATH', '/canvia-la-contrasenya');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once("../common.fansubs.cat/header.inc.php");
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header">Canvia la contrasenya</div>
		<div class="profile-section-explanation">Introdueix la contrasenya anterior i la nova contrasenya per a canviar-la.<br>Rebràs un correu electrònic confirmant el canvi.</div>
		<form id="change-password-form" onsubmit="return changePassword();" autocomplete="off" novalidate="">
			<label for="change_password_old_password">Contrasenya antiga</label>
			<input id="change_password_old_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_old_password_validation" class="validation-message"></div>
			<label for="change_password_password">Contrasenya nova</label>
			<input id="change_password_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_password_validation" class="validation-message"></div>
			<label for="change_password_repeat_password">Repeteix la contrasenya nova</label>
			<input id="change_password_repeat_password" type="password" oninput="removeValidation(this.id);">
			<div id="change_password_repeat_password_validation" class="validation-message"></div>
			<div id="change_password_generic_validation" class="validation-message-generic"></div>
			<button id="change_password_submit" type="submit" class="login-button account-button">Canvia la contrasenya</button>
		</form>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

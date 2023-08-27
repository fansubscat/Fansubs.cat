<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("queries.inc.php");

if (empty($user)) {
	header("Location: ".USERS_URL."/inicia-la-sessio");
	die();
}

define('PAGE_TITLE', 'Elimina el perfil');
define('PAGE_PATH', '/elimina-el-perfil');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'profile');

require_once("../common.fansubs.cat/header.inc.php");
?>
<div class="account-profile-layout">
	<div class="content-layout account-profile-page">
		<div class="profile-section-header">Elimina el perfil</div>
		<div class="profile-section-explanation">Ens sap greu que hagis decidit eliminar el teu perfil de Fansubs.cat.<br>En fer-ho, s’eliminaran completament totes les teves dades d’usuari.<br>Les estadístiques de visualitzacions s’anonimitzaran.<br><br>Introdueix la contrasenya del teu compte per a confirmar-ne l’eliminació.<br>En completar el procés, es tancarà la sessió i rebràs un últim correu confirmant l’eliminació.</div>
		<form id="delete-profile-form" onsubmit="return deleteProfile();" autocomplete="off" novalidate="">
			<label for="delete_profile_password">Contrasenya</label>
			<input id="delete_profile_password" type="password" oninput="removeValidation(this.id);">
			<div id="delete_profile_password_validation" class="validation-message"></div>
			<div id="delete_profile_generic_validation" class="validation-message-generic"></div>
			<button id="delete_profile_submit" type="submit" class="login-button account-button">Elimina el meu perfil</button>
		</form>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

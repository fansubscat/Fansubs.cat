<?php
require_once("../common.fansubs.cat/user_init.inc.php");

if (empty($user)) {
	header("Location: /configuracio");
	die();
}

define('PAGE_TITLE', 'El meu perfil');
define('PAGE_STYLE_TYPE', 'settings');

require_once("../common.fansubs.cat/header.inc.php");
?>
<div class="profile-layout">
	<div class="profile-section">
		<div class="profile-avatar">
			<img alt="Avatar de l’usuari" onclick="" class="profile-avatar-image" src="<?php echo !empty($user['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$user['avatar_filename'] : STATIC_URL.'/images/site/default_avatar.jpg'; ?>">
			<input id="profile-upload-file" name="file" type="file" />
			<div class="profile-avatar-change" onclick="chooseAvatar();"><i class="fa fa-fw fa-upload"></i>Canvia la imatge</div>
		</div>
		<div class="profile-name"><?php echo htmlspecialchars($user['username']); ?></div>
		<a class="profile-my-list-button normal-button" href="/la-meva-llista"><i class="fa fa-fw fa-bookmark"></i> La meva llista</a>
		<a class="profile-button" href="/edita-el-perfil"><i class="fa fa-fw fa-pen-to-square"></i> Edita el perfil</a>
		<a class="profile-button" href="/canvia-la-contrasenya"><i class="fa fa-fw fa-key"></i> Canvia la contrasenya</a>
		<div class="profile-h-divider"></div>
		<a class="profile-button" href="/tanca-la-sessio"><i class="fa fa-fw fa-sign-out"></i> Tanca la sessió</a>
	</div>
	<div class="content-section">
		<div class="content-tabs">
			<a class="content-tab content-tab-active"><i class="fa fa-fw fa-user"></i> Perfil</a>
			<a class="content-tab" href="/configuracio"><i class="fa fa-fw fa-gear"></i> Configuració</a>
		</div>
		<div class="content-layout profile-page">
			<div class="profile-basic-info">
				<div class="profile-section-header">Informació bàsica</div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header">Adreça electrònica</div>
						<div class="profile-section-data-info"><?php echo $user['email']; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Data de naixement</div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d', $user['birthdate']), 'd/m/Y'); ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Data de registre a Fansubs.cat</div>
						<div class="profile-section-data-info"><?php echo date_format(date_create_from_format('Y-m-d H:i:s', $user['created']), 'd/m/Y'); ?></div>
					</div>
				</div>
			</div>
			<div class="profile-statistics">
				<div class="profile-section-header">Estadístiques</div>
				<div class="profile-section-content">
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total d’anime vist</div>
						<div class="profile-section-data-info"><?php echo "123 hores"; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total de manga llegit</div>
						<div class="profile-section-data-info"><?php echo "123 pàgines"; ?></div>
					</div>
					<div class="profile-section-data">
						<div class="profile-section-data-header">Total d’acció real vist</div>
						<div class="profile-section-data-info"><?php echo "123 hores"; ?></div>
					</div>
				</div>
			</div>
			<a class="profile-button remove-profile" onclick="confirmDeleteProfile();">Elimina el perfil</a>
		</div>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

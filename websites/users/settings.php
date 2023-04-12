<?php
define('PAGE_TITLE', 'Configuració');
define('PAGE_PATH', '/configuracio');
define('PAGE_STYLE_TYPE', 'settings');

require_once("../common.fansubs.cat/header.inc.php");
?>
<div class="profile-layout">
<?php
if (!empty($user)) {
?>
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
<?php
}
?>
	<div class="content-section">
		<div class="content-tabs">
<?php
if (!empty($user)) {
?>
			<a class="content-tab" href="/"><i class="fa fa-fw fa-user"></i> Perfil</a>
<?php
}
?>
			<a class="content-tab content-tab-active"><i class="fa fa-fw fa-gear"></i> Configuració</a>
		</div>
		<div class="content-layout settings-page">
			<div class="settings-display">
				<div class="settings-section-header">Visualització</div>
				<div class="settings-section-content">
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="show-cancelled">
								<span class="slider"></span>
								<div class="settings-section-data-header">Mostra els projectes cancel·lats o abandonats pels fansubs</div>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="show-lost">
								<span class="slider"></span>
								<div class="settings-section-data-header">Mostra els projectes amb capítols perduts (no visualitzables)</div>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="settings-seen-settings">
				<div class="settings-section-header">Acció en obrir un capítol</div>
				<div class="settings-section-content">
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="show-cancelled">
								<span class="slider"></span>
								<div class="settings-section-data-header">Marca tots els capítols anteriors com a vistos</div>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="show-cancelled">
								<span class="slider"></span>
								<div class="settings-section-data-header">Demana’m què fer cada vegada</div>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="show-cancelled">
								<span class="slider"></span>
								<div class="settings-section-data-header">No facis res</div>
							</label>
						</div>
					</div>
				</div>
			</div>
			<div class="settings-reader">
				<div class="settings-section-header">Lector de manga</div>
				<div class="settings-section-content">
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="always-use-vertical">
								<span class="slider"></span>
								<div class="settings-section-data-header">Llegeix el manga sempre en mode vertical</div>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<label class="switch">
								<input type="checkbox" id="force-western-order">
								<span class="slider"></span>
								<div class="settings-section-data-header">Força el sentit de lectura occidental al lector paginat</div>
							</label>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
require_once("../common.fansubs.cat/footer.inc.php");
?>

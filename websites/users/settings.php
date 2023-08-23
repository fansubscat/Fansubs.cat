<?php
define('PAGE_TITLE', 'Configuració');
define('PAGE_PATH', '/configuracio');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'settings');

require_once("../common.fansubs.cat/header.inc.php");

if (!empty($user)) {
	$show_cancelled = $user['show_cancelled_projects'];
	$show_lost = $user['show_lost_projects'];
	$show_hentai = empty($user['hide_hentai_access']);
	$hentai_incognito_mode = $user['hentai_incognito_mode'];
	$mark_as_seen = ($user['previous_chapters_read_behavior']==1);
	$reader_type = $user['manga_reader_type'];
	$blacklisted_fansub_ids = $user['blacklisted_fansub_ids'];
} else {
	$show_cancelled = !empty($_COOKIE['show_cancelled_projects']);
	$show_lost = !empty($_COOKIE['show_lost_projects']);
	$reader_type = $_COOKIE['manga_reader_type'];
	$blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
}
?>
<div class="profile-layout">
	<div class="content-layout settings-page">
		<div class="settings-display settings-section">
			<div class="settings-section-header">Opcions de visualització</div>
			<div class="settings-section-content">
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Mostra projectes cancel·lats o abandonats</div>
							<div class="settings-section-data-header-subtitle">Decideix si vols veure a les llistes del web els projectes que els fansubs han cancel·lat o abandonat. A la pàgina de cada contingut s’hi mostraran sempre.</div>
						</div>
						<label class="switch">
							<input type="checkbox" id="show-cancelled"<?php echo $show_cancelled ? ' checked' : ''; ?>>
							<span class="slider"></span>
						</label>
					</div>
				</div>
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Mostra projectes amb capítols perduts</div>
							<div class="settings-section-data-header-subtitle">Decideix si vols veure a les llistes del web els projectes de fansubs històrics amb capítols perduts (editats fa anys però no recuperats). A la pàgina de cada contingut s’hi mostraran sempre.</div>
						</div>
						<label class="switch">
							<input type="checkbox" id="show-lost"<?php echo $show_lost ? ' checked' : ''; ?>>
							<span class="slider"></span>
						</label>
					</div>
				</div>
			</div>
		</div>
<?php
if (!empty($user) && is_adult()) {
?>
		<div class="settings-hentai settings-section">
			<div class="settings-section-header">Contingut per a adults</div>
			<div class="settings-section-content">
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Mostra la icona d’accés al portal de hentai</div>
							<div class="settings-section-data-header-subtitle">Decideix si vols que es mostri la icona que permet l’accés al portal de hentai a la capçalera del web.</div>
						</div>
						<label class="switch">
							<input type="checkbox" id="show-hentai"<?php echo $show_hentai ? ' checked' : ''; ?>>
							<span class="slider"></span>
						</label>
					</div>
				</div>
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Mode d’incògnit al portal de hentai</div>
							<div class="settings-section-data-header-subtitle">Si està activat, no es desaran les visualitzacions de contingut hentai al teu perfil. Encara podràs afegir elements manualment a la teva llista.</div>
						</div>
						<label class="switch">
							<input type="checkbox" id="hentai-incognito-mode"<?php echo $hentai_incognito_mode ? ' checked' : ''; ?>>
							<span class="slider"></span>
						</label>
					</div>
				</div>
			</div>
		</div>
<?php
}
if (!empty($user)) {
?>
		<div class="settings-seen-settings settings-section">
			<div class="settings-section-header">Funcionalitats</div>
			<div class="settings-section-content">
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Marca els capítols anteriors com a vistos o llegits</div>
							<div class="settings-section-data-header-subtitle">Decideix si vols que, en obrir un capítol, tots els capítols anteriors d’aquell projecte es marquin automàticament com a vistos o llegits.</div>
						</div>
						<label class="switch">
							<input type="checkbox" id="mark-previous-as-seen"<?php echo $mark_as_seen ? ' checked' : ''; ?>>
							<span class="slider"></span>
						</label>
					</div>
				</div>
			</div>
		</div>
<?php
}
?>
		<div class="settings-reader settings-section">
			<div class="settings-section-header">Lectura de manga</div>
			<div class="settings-section-content">
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Tipus de lector</div>
							<div class="settings-section-data-header-subtitle">Decideix quin lector de manga vols utilitzar per defecte: sentit oriental (de dreta a esquerra), sentit occidental (d’esquerra a dreta) o tira vertical. Alguns mangues poden ignorar aquesta preferència.</div>
						</div>
						<select id="reader-type" class="settings-combo">
							<option value="rtl"<?php echo $reader_type==0 ? ' selected' : ''; ?>>Sentit oriental</option>
							<option value="ltr"<?php echo $reader_type==1 ? ' selected' : ''; ?>>Sentit occidental</option>
							<option value="strip"<?php echo $reader_type==2 ? ' selected' : ''; ?>>Tira vertical</option>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="settings-blacklist settings-section">
			<div class="settings-section-header">Llista negra de fansubs</div>
			<div class="settings-section-content">
				<div class="settings-section-data">
					<div class="settings-section-data-switch">
						<div class="settings-section-data-header">
							<div class="settings-section-data-header-title">Fansubs a la llista negra</div>
							<div class="settings-section-data-header-subtitle">Els projectes dels fansubs que afegeixis aquí no es mostraran mai a les llistes del web. Encara es mostraran, tot i que amb un estil diferent, a les fitxes de cada contingut.</div>
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

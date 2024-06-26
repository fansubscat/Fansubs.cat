<?php
require_once("../common.fansubs.cat/user_init.inc.php");
require_once("../common.fansubs.cat/common.inc.php");

validate_hentai();

define('PAGE_TITLE', 'Configuració');
define('PAGE_PATH', '/configuracio');
define('PAGE_STYLE_TYPE', 'settings');
define('SETTINGS_ITEM_TYPE', 'settings');

require_once("../common.fansubs.cat/header.inc.php");
require_once("queries.inc.php");

if (!empty($user)) {
	$show_cancelled = $user['show_cancelled_projects'];
	$show_lost = $user['show_lost_projects'];
	$show_hentai = empty($user['hide_hentai_access']);
	$mark_as_seen = $user['previous_chapters_read_behavior'];
	$episode_sort_order = $user['episode_sort_order'];
	$reader_type = $user['manga_reader_type'];
	$blacklisted_fansub_ids = $user['blacklisted_fansub_ids'];
} else {
	$show_cancelled = !empty($_COOKIE['show_cancelled_projects']);
	$show_lost = !empty($_COOKIE['show_lost_projects']);
	$episode_sort_order = !empty($_COOKIE['episode_sort_order']);
	$reader_type = (isset($_COOKIE['manga_reader_type']) ? $_COOKIE['manga_reader_type'] : 0);
	$blacklisted_fansub_ids = get_cookie_blacklisted_fansub_ids();
}
?>
<div class="section">
	<h2 class="section-title-main"><i class="fa fa-fw fa-gear"></i> Configuració</h2>
	<div class="profile-layout">
		<div class="content-layout settings-page">
			<div class="settings-display settings-section">
				<div class="settings-section-header">Opcions de visualització</div>
				<div class="settings-section-content">
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Mostra projectes cancel·lats o abandonats</div>
								<div class="settings-section-data-header-subtitle">Tria si vols veure a les llistes del web els projectes que els fansubs han cancel·lat o abandonat. A la pàgina de cada contingut s’hi mostraran sempre.</div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-cancelled"<?php echo $show_cancelled ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Mostra projectes amb capítols perduts</div>
								<div class="settings-section-data-header-subtitle">Tria si vols veure a les llistes del web els projectes de fansubs històrics amb capítols perduts (editats fa anys però no recuperats). A la pàgina de cada contingut s’hi mostraran sempre.</div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-lost"<?php echo $show_lost ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
<?php
if (!empty($user) && is_adult()) {
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Mostra el botó d’accés a Hentai.cat</div>
								<div class="settings-section-data-header-subtitle">Tria si vols que es mostri la icona que permet canviar a Hentai.cat a la capçalera de Fansubs.cat.</div>
							</div>
							<label class="switch">
								<input type="checkbox" id="show-hentai"<?php echo $show_hentai ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
<?php
}
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Mostra els últims capítols primer</div>
								<div class="settings-section-data-header-subtitle">Si està activat, a les pàgines de contingut es mostraran els últims capítols en primer lloc. Per defecte, s’hi mostren els primers.</div>
							</div>
							<label class="switch">
								<input type="checkbox" id="episode-sort-order"<?php echo $episode_sort_order ? ' checked' : ''; ?> onchange="saveSettings();">
								<span class="slider"></span>
							</label>
						</div>
					</div>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Llista negra de fansubs</div>
								<div class="settings-section-data-header-subtitle">Els projectes dels fansubs que tinguis a la llista negra no es mostraran mai a les llistes del web. Encara es mostraran, tot i que amb un estil diferent, a la pàgina de cada contingut.</div>
							</div>
							<div class="settings-blacklist-chooser">
								<button class="normal-button edit-blacklisted-fansubs">Edita la llista</button>
<?php
$fansubs = query_all_fansubs();
$all_fansubs = array();
$blacklisted_fansubs = array();
foreach ($fansubs as $fansub) {
	array_push($all_fansubs, array(
		'id' => (int)$fansub['id'],
		'name' => $fansub['name']
		));
	if (in_array($fansub['id'], $blacklisted_fansub_ids)) {
		array_push($blacklisted_fansubs, $fansub);
	}
}

if (count($blacklisted_fansubs)==1) {
?>
								<div class="blacklisted-fansubs-list-number">1 fansub blocat</div>
<?php
} else {
?>
								<div class="blacklisted-fansubs-list-number"><?php echo count($blacklisted_fansubs); ?> fansubs blocats</div>
<?php
}
?>
								<input id="blacklisted-fansubs-ids" type="hidden" value="<?php echo implode(',',$blacklisted_fansub_ids);?>">
								<input id="all-fansubs-json" type="hidden" value="<?php echo htmlspecialchars(json_encode($all_fansubs));?>">
							</div>	
						</div>
					</div>
				</div>
			</div>
			<div class="settings-seen-settings settings-section">
				<div class="settings-section-header">Funcionalitats</div>
				<div class="settings-section-content">
<?php
if (!empty($user)) {
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Marca els capítols anteriors com a vistos</div>
								<div class="settings-section-data-header-subtitle">Tria si vols que, en marcar un capítol com a vist, tots els capítols anteriors es marquin automàticament com a vistos.</div>
							</div>
							<select id="mark-previous-as-seen" class="settings-combo" onchange="saveSettings();">
								<option value="0"<?php echo $mark_as_seen==0 ? ' selected' : ''; ?>>Demana-ho</option>
								<option value="1"<?php echo $mark_as_seen==1 ? ' selected' : ''; ?>>Marca’ls sempre</option>
								<option value="2"<?php echo $mark_as_seen==2 ? ' selected' : ''; ?>>No els marquis mai</option>
							</select>
						</div>
					</div>
<?php
}
?>
					<div class="settings-section-data">
						<div class="settings-section-data-switch">
							<div class="settings-section-data-header">
								<div class="settings-section-data-header-title">Lector de manga</div>
								<div class="settings-section-data-header-subtitle">Tria quin lector de manga vols utilitzar: el recomanat per a cada manga, sempre en sentit oriental (de dreta a esquerra), sempre en sentit occidental (d’esquerra a dreta) o sempre en tira vertical.</div>
							</div>
							<select id="reader-type" class="settings-combo" onchange="saveSettings();">
								<option value="0"<?php echo $reader_type==0 ? ' selected' : ''; ?>>Opció recomanada</option>
								<option value="1"<?php echo $reader_type==1 ? ' selected' : ''; ?>>Sentit oriental</option>
								<option value="2"<?php echo $reader_type==2 ? ' selected' : ''; ?>>Sentit occidental</option>
								<option value="3"<?php echo $reader_type==3 ? ' selected' : ''; ?>>Tira vertical</option>
							</select>
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

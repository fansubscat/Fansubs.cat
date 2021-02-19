<?php
ob_start();
require_once("db.inc.php");
require_once('common.inc.php');

if ($header_tab!='oneshots' && $header_tab!='serialized' && $header_tab!='search' && $header_tab!='error' && $header_tab!='about'){
	$header_tab='main';
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#000000" />
<?php
if (!empty($header_social)) {
?>
		<meta name="twitter:card" content="summary_large_image" />
		<meta property="og:title" content="<?php echo htmlspecialchars($header_social['title']); ?>" />
		<meta property="og:url" content="<?php echo htmlspecialchars($header_social['url']); ?>" />
		<meta property="og:description" content="<?php echo htmlspecialchars($header_social['description']); ?>" />
		<meta property="og:image" content="<?php echo htmlspecialchars($header_social['image']); ?>" />
<?php
}
?>
		<title><?php echo !empty($header_page_title) ? $header_page_title.' | Fansubs.cat - Manga en català' : 'Fansubs.cat - Manga en català'; ?></title>
		<link rel="shortcut icon" href="/favicon.png" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
		<link rel="stylesheet" href="<?php echo $base_url; ?>/style/manga.css?v=8" media="screen" />
<?php
$is_fools_day = (date('d')==28 && date('m')==12);
if ($is_fools_day){
?>
		<link rel="stylesheet" href="<?php echo $base_url; ?>/style/28dec.css" media="screen" />
<?php
}
?>
		<script async src="https://www.googletagmanager.com/gtag/js?id=UA-628107-15"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@2.2.1/src/js.cookie.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
		<script src="<?php echo $base_url; ?>/js/common.js?v=8"></script>
	</head>
	<body>
		<div data-nosnippet id="overlay" class="hidden">
			<a id="overlay-close" style="display: none;"><span class="fa fa-times"></span></a>
			<div id="overlay-content"></div>
		</div>
		<div data-nosnippet id="options-overlay" class="hidden flex">
			<div id="options-overlay-content">
				<form id="options-form">
					<h2 class="section-title">Opcions de visualització</h2>
					<div class="options-item">
						<input id="show_cancelled" type="checkbox"<?php echo !empty($_COOKIE['show_cancelled']) ? ' checked' : ''; ?>>
					  	<label for="show_cancelled">Mostra els mangues cancel·lats o abandonats pels fansubs</label>
					</div>
					<div class="options-item">
						<input id="show_missing" type="checkbox"<?php echo !empty($_COOKIE['show_missing']) ? ' checked' : ''; ?>>
					  	<label for="show_missing">Mostra els mangues amb algun capítol sense enllaç vàlid</label>
					</div>
					<div class="options-item">
						<input id="show_hentai" type="checkbox"<?php echo !empty($_COOKIE['show_hentai']) ? ' checked' : ''; ?>>
					  	<label for="show_hentai">Mostra el hentai (confirmes que ets major d'edat)</label>
					</div>
					<h2 class="section-title options-section-divider">Opcions de lectura</h2>
					<div class="options-item">
						<input id="force_long_strip" type="checkbox"<?php echo !empty($_COOKIE['force_long_strip']) ? ' checked' : ''; ?>>
					  	<label for="force_long_strip">Utilitza sempre el lector de manga en mode tira vertical</label>
					</div>
					<div class="options-item">
						<input id="force_reader_ltr" type="checkbox"<?php echo !empty($_COOKIE['force_reader_ltr']) ? ' checked' : ''; ?>>
					  	<label for="force_reader_ltr">Força el sentit de lectura occidental en lloc de l'oriental</label>
					</div>
					<h2 class="section-title options-section-divider">Fansubs que es mostren</h2>
					<div id="options-fansubs">
<?php
$cookie_fansub_ids = get_cookie_fansub_ids();
$resultf = query("SELECT f.id, IF(f.name='Fansub independent','Fansubs independents',f.name) name FROM fansub f WHERE EXISTS (SELECT vf.manga_version_id FROM rel_manga_version_fansub vf LEFT JOIN manga_version v ON vf.manga_version_id=v.id WHERE vf.fansub_id=f.id AND v.hidden=0) ORDER BY IF(f.name='Fansub independent','Fansubs independents',f.name)");
while ($row = mysqli_fetch_assoc($resultf)) {
?>
						<div class="options-item options-fansub">
							<input id="show_fansub_<?php echo $row['id']; ?>" type="checkbox"<?php echo in_array($row['id'],$cookie_fansub_ids) ? '' : ' checked'; ?> value="<?php echo $row['id']; ?>">
						  	<label for="show_fansub_<?php echo $row['id']; ?>"><?php echo $row['name']; ?></label>
						</div>
<?php
}
mysqli_free_result($resultf);
?>
					</div>
					<div id="options-select-buttons">
						<a id="options-select-all">Selecciona'ls tots</a> / <a id="options-unselect-all">Desselecciona'ls tots</a>
					</div>
				</form>
				<div id="options-buttonbar">
					<button id="options-save-button"><span class="fa fa-check icon"></span>Desa la configuració</button>
					<button id="options-cancel-button"><span class="fa fa-times icon"></span>Cancel·la</button>
				</div>
			</div>
		</div>
		<div data-nosnippet id="contact-overlay" class="hidden flex">
			<div id="contact-overlay-content">
				<form id="contact-form">
					<h2 class="section-title">Envia'ns un comentari</h2>
					<div id="contact-explanation"></div>
					<div>
					  	<label for="contact_address">Adreça electrònica <small>(et respondrem aquí)</small></label><br />
						<input id="contact_address" name="email" required>
					</div>
					<div style="margin-top: 0.5em;">
					  	<label for="contact_address">Missatge <small>(digues qui ets i per què ens escrius)</small></label><br />
						<textarea id="contact_message" name="message" required></textarea>
					</div>
				</form>
				<div id="contact-buttonbar">
					<button id="contact-send-button"><span class="fa fa-check icon"></span>Envia el missatge</button>
					<button id="contact-send-button-loading" class="hidden">S'està enviant...</button>
					<button id="contact-send-button-done" class="hidden">Missatge enviat!</button>
					<button id="contact-cancel-button"><span class="fa fa-times icon"></span>Cancel·la</button>
				</div>
			</div>
		</div>
		<div id="page">
			<div data-nosnippet id="header">
				<div class="page-title-block">
					<a class="page-title" href="<?php echo $base_url; ?>/">Fansubs.cat</a>
					<div class="page-links">
						<a href="https://anime.fansubs.cat/">Anime</a> | <b>Manga</b> | <a href="https://www.fansubs.cat/">Notícies</a>
					</div>
				</div>
				<div class="tabs">
					<a class="tab<?php if ($header_tab=='main') echo ' selectedtab'; ?>" href="<?php echo $base_url; ?>/"><span class="fa fa-star"></span> Destacats</a>
					<a class="tab<?php if ($header_tab=='oneshots') echo ' selectedtab'; ?>" href="<?php echo $base_url; ?>/one-shots"><span class="fa fa-book-open"></span> One&#8209;shots</a>
					<a class="tab<?php if ($header_tab=='serialized') echo ' selectedtab'; ?>" href="<?php echo $base_url; ?>/serialitzats"><span class="fa fa-book"></span> Serialitzats</a>
				</div>
				<div class="separator"></div>
				<div class="user-options">
					<div id="options-tooltip-base">
						<a id="options-button" class="iconbutton" title="Opcions"><span class="fa fa-cogs"></span></a>
						<span id="options-tooltip" class="hidden"><a id="options-tooltip-close" class="fa fa-times" style="float: right; color: black;"></a>Si canvies opcions, veuràs més mangues</span>
					</div>
					<a id="about-button" class="iconbutton<?php if ($header_tab=='about') echo ' selectedtab'; ?>" title="Qui som?" href="<?php echo $base_url; ?>/qui-som"><span class="fa fa-info-circle"></span></a>
				</div>
				<div class="search-form">
					<form id="search_form">
						<input id="search_query" type="text" value="<?php echo !empty($_GET['query']) ? htmlspecialchars($_GET['query']) : ''; ?>" placeholder="Cerca...">
						<span id="search_button" class="fa fa-search" title="Cerca"></span>
					</form>
				</div>
			</div>
			<div id="content"<?php echo !empty($header_series_page) ? ' class="series-page"' : ''; ?>>
<?php
//Show app layout if we are being accessed from an Android browser (rely on the User-Agent)
if ((!isset($_COOKIE['tachiyomi_message_closed']) || $_COOKIE['tachiyomi_message_closed']!='1') && !empty($_SERVER['HTTP_USER_AGENT']) && stripos(strtolower($_SERVER['HTTP_USER_AGENT']),'android')!==FALSE){
?>
				<span id="tachiyomi-message">
					<span id="tachiyomi-message-real">
						<a id="tachiyomi-message-close" class="fa fa-times" style="float: right; color: black;" title="Amaga aquest missatge"></a>Sabies que a Android també pots llegir tots els mangues amb el Tachiyomi? <a href="https://www.tachiyomi.org" target="_blank">Baixa'l aquí</a> i instal·la-hi l'extensió «Fansubs.cat»!
					</span>
				</span>
<?php
}
?>

<?php
ob_start();
require_once("db.inc.php");
require_once('common.inc.php');

if ($header_tab!='movies' && $header_tab!='series' && $header_tab!='search' && $header_tab!='error'){
	$header_tab='main';
}
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0" />
		<meta name="theme-color" content="#000000" />

		<title><?php echo !empty($header_page_title) ? $header_page_title.' - Fansubs.cat - Anime' : 'Fansubs.cat - Anime'; ?></title>
		<link rel="stylesheet" media="screen" type="text/css" href="/style/anime.css" />
		<link rel="stylesheet" type="text/css" media="screen" href="/style/magnific-popup-1.1.0.css" />
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.9.0/css/all.css">
		<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
		<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick-theme.css"/>
		<link rel="shortcut icon" href="/favicon.png" />
		<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.0.0/jquery.min.js"></script>
		<script type="text/javascript" src="/js/jquery.magnific-popup-1.1.0.min.js"></script>
		<script type="text/javascript" src="/js/js.cookie-2.1.2.min.js"></script>
		<script type="text/javascript" src="/js/common.js"></script>
		<script type="text/javascript" src="//cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
	</head>
	<body>
		<div id="overlay" class="hidden">
			<a id="overlay-close"><span class="fa fa-times"></span></a>
			<div id="overlay-content"></div>
		</div>
		<div id="options-overlay" class="hidden flex">
			<div id="options-overlay-content">
				<form id="options-form">
					<h2 class="section-title">Opcions de visualització</h2>
					<div class="options-item">
						<input id="show_cancelled" type="checkbox"<?php echo !empty($_COOKIE['show_cancelled']) ? ' checked' : ''; ?>>
					  	<label for="show_cancelled">Mostra sèries cancel·lades o abandonades</label>
					</div>
					<div class="options-item">
						<input id="show_hentai" type="checkbox"<?php echo !empty($_COOKIE['show_hentai']) ? ' checked' : ''; ?>>
					  	<label for="show_hentai">Mostra hentai (confirmes que ets major d'edat)</label>
					</div>
					<h2 class="section-title options-section-divider">Fansubs que es mostren</h2>
					<div id="options-fansubs">
<?php
$cookie_fansub_ids = get_cookie_fansub_ids();
$resultf = query("SELECT id, IF(name='Fansub independent','Fansubs independents',name) name FROM fansub ORDER BY IF(name='Fansub independent','Fansubs independents',name)");
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
				</form>
				<div id="options-buttonbar">
					<button id="options-save-button"><span class="fa fa-check icon"></span>Desa la configuració</button>
					<button id="options-cancel-button"><span class="fa fa-times icon"></span>Cancel·la</button>
				</div>
			</div>
		</div>
		<div id="page">
			<div id="header">
				<a class="page-title" href="/">Fansubs.cat - Anime</a>
				<div class="tabs">
					<a class="tab<?php if ($header_tab=='main') echo ' selectedtab'; ?>" href="/">Destacat</a>
					<a class="tab<?php if ($header_tab=='movies') echo ' selectedtab'; ?>" href="/films">Films</a>
					<a class="tab<?php if ($header_tab=='series') echo ' selectedtab'; ?>" href="/series">Sèries</a>
				</div>
				<div class="separator"></div>
				<div class="user-options">
					<a id="options-button" class="tab">Opcions</a>
				</div>
				<div class="search-form">
					<form id="search_form">
						<input id="search_query" type="text" value="<?php echo !empty($_GET['query']) ? $_GET['query'] : ''; ?>" placeholder="Fes una cerca...">
						<span id="search_button" class="fa fa-search" title="Cerca"></span>
					</form>
				</div>
			</div>
			<div id="content">
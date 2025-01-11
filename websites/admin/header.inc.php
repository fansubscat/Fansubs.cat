<?php
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if(!empty($_SESSION['username']) && mysqli_num_rows(query("SELECT * FROM admin_user u WHERE username='".escape($_SESSION['username'])."'"))==0) {
	session_destroy();
	header("Location: login.php");
	die();
}
?>
<!doctype html>
<html lang="ca">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-config" content="<?php echo STATIC_URL; ?>/favicons/admin/browserconfig.xml">
		<meta name="theme-color" content="#6aa0f8">
		<title><?php echo $header_title; ?> - Tauler d’administració de Fansubs.cat</title>
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo STATIC_URL; ?>/favicons/admin/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon-16x16.png">
		<link rel="manifest" href="<?php echo STATIC_URL; ?>/favicons/admin/site.webmanifest">
		<link rel="mask-icon" href="<?php echo STATIC_URL; ?>/favicons/admin/safari-pinned-tab.svg" color="#6aa0f8">
		<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon.ico">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/admin.css?v=<?php echo VERSION; ?>" />
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2"></script>
		<script src="<?php echo STATIC_URL; ?>/js/admin.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/uncompress.js"></script>
	</head>
	<body>
<?php
if (empty($skip_navbar) && !empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<div class="container-fluid">
				<span class="navbar-brand"><a href="<?php echo ADMIN_URL; ?>/">Tauler d’administració</a> <small><small><a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank" title="Registre de canvis">v<?php echo VERSION; ?></a></small></small></span>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Commuta la navegació">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='anime' ? ' active' : ''; ?>" href="#" id="navbarDropdownSeries" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anime</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=anime">Llista d’anime</a>
								<a class="dropdown-item" href="series_edit.php?type=anime">Afegeix un anime nou</a>
								<div class="dropdown-divider"></div>
<?php
	}
?>
								<a class="dropdown-item" href="version_list.php?type=anime">Llista de versions d’anime</a>
								<a class="dropdown-item" href="series_choose.php?type=anime">Afegeix una versió nova</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='manga' ? ' active' : ''; ?>" href="#" id="navbarDropdownSeries" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manga</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=manga">Llista de manga</a>
								<a class="dropdown-item" href="series_edit.php?type=manga">Afegeix un manga nou</a>
								<div class="dropdown-divider"></div>
<?php
	}
?>
								<a class="dropdown-item" href="version_list.php?type=manga">Llista de versions de manga</a>
								<a class="dropdown-item" href="series_choose.php?type=manga">Afegeix una versió nova</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='liveaction' ? ' active' : ''; ?>" href="#" id="navbarDropdownSeries" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Imatge real</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=liveaction">Llista de contingut d’imatge real</a>
								<a class="dropdown-item" href="series_edit.php?type=liveaction">Afegeix un contingut d’imatge real nou</a>
								<div class="dropdown-divider"></div>
<?php
	}
?>
								<a class="dropdown-item" href="version_list.php?type=liveaction">Llista de versions d’imatge real</a>
								<a class="dropdown-item" href="series_choose.php?type=liveaction">Afegeix una versió nova</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='news' ? ' active' : ''; ?>" href="#" id="navbarDropdownSeries" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Notícies</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
								<a class="dropdown-item" href="news_list.php">Llista de notícies</a>
								<a class="dropdown-item" href="news_edit.php">Afegeix una notícia a mà</a>
<?php
	if ($_SESSION['admin_level']>=3) {
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="news_fetcher_list.php">Llista de recollidors de notícies</a>
								<a class="dropdown-item" href="news_fetcher_edit.php">Afegeix un recollidor de notícies nou</a>
<?php
	}
?>
							</div>
						</li>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='fansub' ? ' active' : ''; ?>" href="#" id="navbarDropdownFansubs" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fansubs</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownFansubs">
								<a class="dropdown-item" href="fansub_list.php">Llista de fansubs</a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
								<a class="dropdown-item" href="fansub_edit.php">Afegeix un fansub nou</a>
<?php
		}
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="remote_account_list.php">Llista de comptes remots</a>
								<a class="dropdown-item" href="remote_account_edit.php">Afegeix un compte remot nou</a>
							</div>
						</li>
<?php
	}
	if ($_SESSION['admin_level']>=3) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='other' ? ' active' : ''; ?>" href="#" id="navbarDropdownOthers" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Altres</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownOthers">
								<a class="dropdown-item" href="admin_list.php">Llista d’administradors</a>
								<a class="dropdown-item" href="admin_edit.php">Afegeix un administrador nou</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="community_list.php">Llista de comunitats</a>
								<a class="dropdown-item" href="community_edit.php">Afegeix una comunitat nova</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="advent_list.php">Llista de calendaris d’advent</a>
							</div>
						</li>
<?php
	}
	if ($_SESSION['admin_level']>=1) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='analytics' ? ' active' : ''; ?>" href="#" id="navbarDropdownAnalytics" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anàlisi</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownAnalytics">
								<a class="dropdown-item" href="views.php?type=anime">Darreres visualitzacions - Anime</a>
								<a class="dropdown-item" href="views.php?type=manga">Darreres visualitzacions - Manga</a>
								<a class="dropdown-item" href="views.php?type=liveaction">Darreres visualitzacions - Imatge real</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="comment_list.php">Darrers comentaris</a>
								<a class="dropdown-item" href="popular.php">Continguts més populars</a>
								<a class="dropdown-item" href="stats.php">Estadístiques</a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="error_list.php">Errors de reproducció</a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='tools' ? ' active' : ''; ?>" href="#" id="navbarDropdownTools" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Eines</a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownTools">
								<a class="dropdown-item" href="link_verifier.php">Verificador d’enllaços remots</a>
								<a class="dropdown-item" href="news_fetcher_status.php">Estat dels recollidors de notícies</a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
								<a class="dropdown-item" href="admin_log.php">Registre d’accions</a>
								<a class="dropdown-item" href="pending_conversions.php">Conversions pendents</a>
								<a class="dropdown-item" href="maintenance.php">Manteniment</a>
<?php
		}
?>
								<a class="dropdown-item" href="change_password.php">Canvia la contrasenya</a>
							</div>
						</li>
<?php
	}
?>
					</ul>
					<a class="navbar-extra-link pe-3" href="<?php echo MAIN_URL; ?>/" target="_blank" title="Portada - Web públic (www.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-main fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo ANIME_URL; ?>/" target="_blank" title="Anime - Web públic (anime.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-anime fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo MANGA_URL; ?>/" target="_blank" title="Manga - Web públic (manga.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-manga fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo LIVEACTION_URL; ?>/" target="_blank" title="Imatge real - Web públic (imatgereal.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-liveaction fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo NEWS_URL; ?>/" target="_blank" title="Notícies - Web públic (noticies.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-news fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="https://www.<?php echo HENTAI_DOMAIN; ?>/" target="_blank" title="Hentai - Web públic (www.hentai.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-hentai fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo RESOURCES_URL; ?>/" target="_blank" title="Recursos - Web públic (recursos.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-resources fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo ADVENT_URL; ?>/" target="_blank" title="Calendari d’advent - Web públic (advent.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-advent fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo ADMIN_TUTORIAL_URL; ?>" target="_blank" title="Ajuda"><span class="fa fa-question-circle"></span></a>
					<a class="navbar-extra-link" href="logout.php" title="Tanca la sessió"><?php echo htmlspecialchars($_SESSION['username']); ?><span class="fa fa-sign-out-alt ms-2"></span></a>
				</div>
			</div>
		</nav>
		<div class="modal fade" id="generic-modal" tabindex="-1" role="dialog" aria-labelledby="generic-modal-title" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="generic-modal-title"></h5>
						<button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
							<span aria-hidden="true" class="fa fa-times"></span>
						</button>
					</div>
					<div class="modal-body"></div>
					<button type="button" data-bs-dismiss="modal" class="btn btn-secondary align-self-center m-3">
						Tanca
					</button>
				</div>
			</div>
		</div>
<?php
}
?>

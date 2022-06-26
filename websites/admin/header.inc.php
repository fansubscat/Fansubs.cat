<?php
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
require_once("db.inc.php");
require_once("common.inc.php");

session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();
?>
<!doctype html>
<html lang="ca">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo $header_title; ?> - Tauler d'administració de Fansubs.cat</title>
		<link rel="shortcut icon" href="/favicon.png" />
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.3/css/all.css">
		<link rel="stylesheet" href="/style/admin.css?v=<?php echo $version_name; ?>" />
		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
		<script src="js/adminfansubscat.js?v=<?php echo $version_name; ?>"></script>
		<script src="js/uncompress.js"></script>
	</head>
	<body>
<?php
if (empty($skip_navbar) && !empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<span class="navbar-brand"><a href="<?php echo $base_url; ?>/">Tauler d'administració</a> <small><small><a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank" title="Registre de canvis">v<?php echo $version_name; ?></a></small></small></span>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Commuta la navegació">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
					<li class="nav-item dropdown<?php echo $page=='anime' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anime</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
							<a class="dropdown-item" href="series_list.php?type=anime">Llista d'anime</a>
							<a class="dropdown-item" href="series_edit.php?type=anime">Afegeix un anime nou</a>
							<div class="dropdown-divider"></div>
<?php
	}
?>
							<a class="dropdown-item" href="version_list.php?type=anime">Llista de versions d'anime</a>
							<a class="dropdown-item" href="series_choose.php?type=anime">Afegeix una versió nova</a>
						</div>
					</li>
					<li class="nav-item dropdown<?php echo $page=='manga' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manga</a>
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
					<li class="nav-item dropdown<?php echo $page=='liveaction' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Acció real</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
							<a class="dropdown-item" href="series_list.php?type=liveaction">Llista de contingut d'acció real</a>
							<a class="dropdown-item" href="series_edit.php?type=liveaction">Afegeix un contingut d'acció real nou</a>
							<div class="dropdown-divider"></div>
<?php
	}
?>
							<a class="dropdown-item" href="version_list.php?type=liveaction">Llista de versions d'acció real</a>
							<a class="dropdown-item" href="series_choose.php?type=liveaction">Afegeix una versió nova</a>
						</div>
					</li>
					<li class="nav-item dropdown<?php echo $page=='news' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Notícies</a>
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
					<li class="nav-item dropdown<?php echo $page=='fansub' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownFansubs" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fansubs</a>
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
					<li class="nav-item dropdown<?php echo $page=='admin' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownOthers" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Altres</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownOthers">
							<a class="dropdown-item" href="admin_list.php">Llista d'administradors</a>
							<a class="dropdown-item" href="admin_edit.php">Afegeix un administrador nou</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="advent_list.php">Llista de calendaris d'advent</a>
						</div>
					</li>
<?php
	}
	if ($_SESSION['admin_level']>=1) {
?>
					<li class="nav-item dropdown<?php echo $page=='analytics' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAnalytics" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anàlisi</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownAnalytics">
							<a class="dropdown-item" href="views.php?type=anime">Darreres visualitzacions - Anime</a>
							<a class="dropdown-item" href="views.php?type=manga">Darreres visualitzacions - Manga</a>
							<a class="dropdown-item" href="views.php?type=liveaction">Darreres visualitzacions - Acció real</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="popular.php">Continguts més populars</a>
							<a class="dropdown-item" href="search_history.php">Historial de cerques</a>
							<a class="dropdown-item" href="stats.php">Estadístiques</a>
							<div class="dropdown-divider"></div>
							<a class="dropdown-item" href="error_list.php">Errors de reproducció</a>
							<a class="dropdown-item" href="storage_status.php">Servidors d'emmagatzematge</a>
						</div>
					</li>
					<li class="nav-item dropdown<?php echo $page=='tools' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTools" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Eines</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownTools">
							<a class="dropdown-item" href="link_verifier.php">Verificador d'enllaços remots</a>
							<a class="dropdown-item" href="news_fetcher_status.php">Estat dels recollidors de notícies</a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
							<a class="dropdown-item" href="admin_log.php">Registre d'accions</a>
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
				<a class="text-light pr-4" href="<?php echo $main_url; ?>/" target="_blank" title="Portada - Web públic (www.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-main fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="<?php echo $anime_url; ?>/" target="_blank" title="Anime - Web públic (anime.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-anime fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
				<a class="text-light pr-4" href="<?php echo $manga_url; ?>/" target="_blank" title="Manga - Web públic (manga.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-manga fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="<?php echo $liveaction_url; ?>/" target="_blank" title="Acció real - Web públic (accioreal.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-liveaction fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
				<a class="text-light pr-4" href="<?php echo $news_url; ?>/" target="_blank" title="Notícies - Web públic (www.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-news fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="<?php echo $resources_url; ?>/" target="_blank" title="Recursos - Web públic (recursos.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-resources fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="<?php echo $tutorial_url; ?>" target="_blank" title="Ajuda"><span class="fa fa-question-circle"></span></a>
				<a class="text-light" href="logout.php" title="Tanca la sessió"><?php echo htmlspecialchars($_SESSION['username']); ?><span class="fa fa-sign-out-alt ml-2"></span></a>
			</div>
		</nav>
<?php
}
?>

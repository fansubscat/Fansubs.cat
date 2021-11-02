<?php
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
require_once("db.inc.php");

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
		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
		<script src="js/adminfansubscat.js?v=9"></script>
		<script src="js/uncompress.js"></script>
		<style>
			html,body{height: 100%;}
			a{text-decoration: none !important;}
			.form-group label{font-weight: bold;}
			.mandatory:after {padding-left: 0.2em; content:"*"; color:red;}
			.fa-stack { font-size: 0.5em; vertical-align: middle; margin-bottom: 0.5em; }
			.fa-custom-anime:before { font-family: Arial; font-weight: bold; content: 'A'; background-color: #007bff!important; padding-left: 0.25em; padding-right: 0.25em;}
			.fa-custom-manga:before { font-family: Arial; font-weight: bold; content: 'M'; background-color: #007bff!important; padding-left: 0.25em; padding-right: 0.25em;}
			.fa-custom-news:before { font-family: Arial; font-weight: bold; content: 'N'; background-color: #007bff!important; padding-left: 0.25em; padding-right: 0.25em;}
			.fa-custom-resources:before { font-family: Arial; font-weight: bold; content: 'R'; background-color: #007bff!important; padding-left: 0.25em; padding-right: 0.25em;}
			tr[id^="form-instance-links-list-"] input[type="url"], tr[id^="form-extras-list-row-"] input[type="url"] { padding-left: 24px; }
			tr[id^="form-instance-links-list-"] input[type="url"][value^="http://"], tr[id^="form-extras-list-row-"] input[type="url"][value^="http://"], tr[id^="form-instance-links-list-"] input[type="url"][value^="https://"], tr[id^="form-extras-list-row-"] input[type="url"][value^="https://"] { background: url("images/unknown.png") no-repeat scroll 4px center; }
			tr[id^="form-instance-links-list-"] input[type="url"][value^="https://mega.nz/"], tr[id^="form-extras-list-row-"] input[type="url"][value^="https://mega.nz/"] { background: url("images/mega.png") no-repeat scroll 4px center !important; }
			tr[id^="form-instance-links-list-"] input[type="url"][value^="storage://"], tr[id^="form-extras-list-row-"] input[type="url"][value^="storage://"] { background: url("images/storage.png") no-repeat scroll 4px center !important; }
			tr[id^="form-instance-links-list-"] input[type="url"][value^="https://drive.google.com/"], tr[id^="form-extras-list-row-"] input[type="url"][value^="https://drive.google.com/"] { background: url("images/drive.png") no-repeat scroll 4px center !important; }
			tr[id^="form-instance-links-list-"] input[type="url"][value^="https://www.youtube.com/"], tr[id^="form-extras-list-row-"] input[type="url"][value^="https://www.youtube.com/"] { background: url("images/youtube.png") no-repeat scroll 4px center !important; }
		</style>
	</head>
	<body>
<?php
if (empty($skip_navbar) && !empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<a class="navbar-brand" href="<?php echo $base_url; ?>/">Tauler d'administració</a>
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
							<a class="dropdown-item" href="series_list.php">Llista d'anime</a>
							<a class="dropdown-item" href="series_edit.php">Afegeix un anime nou</a>
							<div class="dropdown-divider"></div>
<?php
	}
?>
							<a class="dropdown-item" href="version_list.php">Llista de versions d'anime</a>
							<a class="dropdown-item" href="series_choose.php">Afegeix una versió nova</a>
						</div>
					</li>
					<li class="nav-item dropdown<?php echo $page=='manga' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Manga</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
							<a class="dropdown-item" href="manga_list.php">Llista de manga</a>
							<a class="dropdown-item" href="manga_edit.php">Afegeix un manga nou</a>
							<div class="dropdown-divider"></div>
<?php
	}
?>
							<a class="dropdown-item" href="manga_version_list.php">Llista de versions de manga</a>
							<a class="dropdown-item" href="manga_choose.php">Afegeix una versió nova</a>
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
							<a class="dropdown-item" href="fetcher_list.php">Llista de recollidors</a>
							<a class="dropdown-item" href="fetcher_edit.php">Afegeix un recollidor nou</a>
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
<?php
		if ($_SESSION['admin_level']>=3) {
?>
							<a class="dropdown-item" href="fansub_list.php">Llista de fansubs</a>
							<a class="dropdown-item" href="fansub_edit.php">Afegeix un fansub nou</a>
							<div class="dropdown-divider"></div>
<?php
		}
?>
							<a class="dropdown-item" href="account_list.php">Llista de comptes</a>
							<a class="dropdown-item" href="account_edit.php">Afegeix un compte nou</a>
						</div>
					</li>
<?php
	}
	if ($_SESSION['admin_level']>=3) {
?>
					<li class="nav-item dropdown<?php echo $page=='user' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsers" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Usuaris</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
							<a class="dropdown-item" href="user_list.php">Llista d'usuaris</a>
							<a class="dropdown-item" href="user_edit.php">Afegeix un usuari nou</a>
						</div>
					</li>
<?php
	}
	if ($_SESSION['admin_level']>=1) {
?>
					<li class="nav-item dropdown<?php echo $page=='analytics' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsers" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Anàlisi</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
							<a class="dropdown-item" href="stats.php">Estadístiques</a>
							<a class="dropdown-item" href="manga_views.php">Darreres lectures de manga</a>
							<a class="dropdown-item" href="views.php">Darreres visualitzacions d'anime</a>
							<a class="dropdown-item" href="search_history.php">Cerques d'anime</a>
							<a class="dropdown-item" href="manga_search_history.php">Cerques de manga</a>
							<a class="dropdown-item" href="popular.php">Els més populars del mes</a>
							<a class="dropdown-item" href="error_list.php">Errors de reproducció</a>
						</div>
					</li>
					<li class="nav-item dropdown<?php echo $page=='tools' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsers" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Eines</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
							<a class="dropdown-item" href="link_verifier.php">Verificador d'enllaços</a>
							<a class="dropdown-item" href="fetcher_status.php">Estat dels recollidors</a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
							<a class="dropdown-item" href="action_log.php">Registre d'accions</a>
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
				<a class="text-light pr-4" href="https://anime.fansubs.cat/" target="_blank" title="Anime - Web públic (anime.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-anime fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
				<a class="text-light pr-4" href="https://manga.fansubs.cat/" target="_blank" title="Manga - Web públic (manga.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-manga fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="https://www.fansubs.cat/" target="_blank" title="Notícies - Web públic (www.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-news fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="https://recursos.fansubs.cat/" target="_blank" title="Recursos - Web públic (recursos.fansubs.cat)"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-resources fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></span></a>
				<a class="text-light pr-4" href="https://docs.google.com/document/d/10EMgWjVqrmDFUpxLI44cq4n5iOPHRKL3vZfL59Tt3SA/edit?usp=sharing" target="_blank" title="Ajuda"><span class="fa fa-question-circle"></span></a>
				<a class="text-light" href="logout.php" title="Tanca la sessió"><?php echo htmlspecialchars($_SESSION['username']); ?><span class="fa fa-sign-out-alt ml-2"></span></a>
			</div>
		</nav>
<?php
}
?>

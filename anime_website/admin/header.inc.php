<?php
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
require_once("../db.inc.php");

session_set_cookie_params(3600 * 24 * 30); // 30 days
session_start();
?>
<!doctype html>
<html lang="ca">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<title><?php echo $header_title; ?> - Tauler d'administració de Fansubs.cat - Anime</title>
		<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.8/css/all.css">
		<script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@2.8.0"></script>
		<script src="scripts.js"></script>
		<style>
			html,body{height: 100%;}
			a{text-decoration: none !important;}
			.form-group label{font-weight: bold;}
			.mandatory:after {padding-left: 0.2em; content:"*"; color:red;}
		</style>
	</head>
	<body>
<?php
if (empty($skip_navbar) && !empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<a class="navbar-brand" href="/admin/">Tauler d'administració</a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Commuta la navegació">
				<span class="navbar-toggler-icon"></span>
			</button>
			<div class="collapse navbar-collapse" id="navbarSupportedContent">
				<ul class="navbar-nav mr-auto">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<li class="nav-item dropdown<?php echo $page=='series' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownSeries" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Sèries</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownSeries">
							<a class="dropdown-item" href="series_list.php">Llista de sèries</a>
							<a class="dropdown-item" href="series_edit.php">Afegeix una sèrie nova</a>
						</div>
					</li>
<?php
	}
?>
					<li class="nav-item dropdown<?php echo $page=='version' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownVersions" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Versions</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownVersions">
							<a class="dropdown-item" href="version_list.php">Llista de versions</a>
							<a class="dropdown-item" href="series_choose.php">Afegeix una versió nova</a>
						</div>
					</li>
<?php
	if ($_SESSION['admin_level']>=2) {
?>
					<li class="nav-item dropdown<?php echo $page=='account' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAccounts" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Comptes</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownAccounts">
							<a class="dropdown-item" href="account_list.php">Llista de comptes</a>
							<a class="dropdown-item" href="account_edit.php">Afegeix un compte nou</a>
						</div>
					</li>
<?php
	}
	if ($_SESSION['admin_level']>=3) {
?>
					<li class="nav-item dropdown<?php echo $page=='fansub' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownFansubs" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Fansubs</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownFansubs">
							<a class="dropdown-item" href="fansub_list.php">Llista de fansubs</a>
							<a class="dropdown-item" href="fansub_edit.php">Afegeix un fansub nou</a>
						</div>
					</li>
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
					<li class="nav-item dropdown<?php echo $page=='tools' ? ' active' : ''; ?>">
						<a class="nav-link dropdown-toggle" href="#" id="navbarDropdownUsers" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Eines</a>
						<div class="dropdown-menu" aria-labelledby="navbarDropdownUsers">
							<a class="dropdown-item" href="stats.php">Estadístiques</a>
							<a class="dropdown-item" href="link_verifier.php">Verificador d'enllaços</a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
							<a class="dropdown-item" href="action_log.php">Registre d'accions</a>
							<a class="dropdown-item" href="maintenance.php">Manteniment</a>
<?php
		}
?>
						</div>
					</li>
<?php
	}
?>
				</ul>
				<a class="text-light pr-4" href="https://anime.fansubs.cat/" target="_blank" title="Mostra el web públic"><span class="fa fa-globe"></span></a>
				<a class="text-light pr-4" href="https://docs.google.com/document/d/10EMgWjVqrmDFUpxLI44cq4n5iOPHRKL3vZfL59Tt3SA/edit?usp=sharing" target="_blank" title="Ajuda"><span class="fa fa-question-circle"></span></a>
				<a class="text-light" href="logout.php" title="Tanca la sessió"><?php echo htmlspecialchars($_SESSION['username']); ?><span class="fa fa-sign-out-alt ml-2"></span></a>
			</div>
		</nav>
<?php
}
?>

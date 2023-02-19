<?php
require_once("user_init.inc.php");
?>
<!DOCTYPE html>
<html lang="ca">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<meta name="theme-color" content="#000000">
		<meta name="referrer" content="origin">
		<meta name="twitter:card" content="summary_large_image">
		<meta property="og:title" content="<?php echo $social_title; ?>">
		<meta property="og:url" content="<?php echo $social_url; ?>">
		<meta property="og:description" content="<?php echo $social_description; ?>">
		<meta property="og:image" content="<?php echo $social_image_url; ?>">
		<meta property="og:image:type" content="image/jpeg">
		<title><?php echo $page_title; ?></title>
		<link rel="shortcut icon" href="/favicon.png">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.3.0/css/all.css">
		<link rel="stylesheet" href="/style/main.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
		<script src="/js/main.js"></script>
	</head>
	<body>
		<div class="main-container<?php echo !empty($obscure_background) ? ' obscured-background' : ''; ?>">
			<div class="main-body">
				<div class="header">
<?php
if (!empty($show_social)) {
?>
					<a class="social-link twitter-link fab fa-fw fa-twitter" href="https://twitter.com/fansubscat" target="_blank" title="Twitter de Fansubs.cat"></a>
					<a class="social-link mastodon-link fab fa-fw fa-mastodon" href="https://mastodont.cat/@fansubscat" target="_blank" title="Mastodon de Fansubs.cat"></a>
					<a class="social-link telegram-link fab fa-fw fa-telegram" href="https://t.me/fansubscat" target="_blank" title="Telegram de Fansubs.cat"></a>
<?php
} else {
?>
					<a class="logo-small" href="/"><?php include($static_directory.'/common/images/logo.svg'); ?></a>
<?php
}
?>
					<div class="user-options">
<?php
if (!empty($user)) {
?>
						<div class="user-name"><strong><?php echo $user['username']; ?></strong></div>
						<a class="user-logout" href="<?php echo $users_url.'/tanca-la-sessio/'; ?>"><span class="fa fa-fw fa-sign-out-alt"></span></a>
<?php
} else {
?>
						<a class="user-login" href="<?php echo $users_url.'/inicia-la-sessio/'; ?>">Inicia la sessió</a>
<?php
}
?>
					</div>
				</div>
				<div class="main-section">

<?php
error_reporting(E_ALL & ~E_NOTICE);
ob_start();
require_once(__DIR__.'/db.inc.php');
require_once(__DIR__.'/common.inc.php');

session_name(ADMIN_COOKIE_NAME);
session_set_cookie_params(ADMIN_COOKIE_DURATION, '/', COOKIE_DOMAIN, TRUE, FALSE);
session_start();

if(!empty($_SESSION['username']) && mysqli_num_rows(query("SELECT * FROM admin_user u WHERE username='".escape($_SESSION['username'])."' AND disabled=0"))==0) {
	session_destroy();
	header("Location: login.php");
	die();
}
?>
<!doctype html>
<html lang="<?php echo SITE_LANGUAGE; ?>">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-config" content="<?php echo STATIC_URL; ?>/favicons/admin/browserconfig.xml">
		<meta name="theme-color" content="#6aa0f8">
		<title><?php echo $header_title; ?> - <?php echo sprintf(lang('admin.generic.header'), MAIN_SITE_NAME); ?></title>
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo STATIC_URL; ?>/favicons/admin/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon-16x16.png">
		<link rel="manifest" href="<?php echo STATIC_URL; ?>/favicons/admin/site.webmanifest">
		<link rel="mask-icon" href="<?php echo STATIC_URL; ?>/favicons/admin/safari-pinned-tab.svg" color="#6aa0f8">
		<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>/favicons/admin/favicon.ico">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" integrity="sha256-zRgmWB5PK4CvTx4FiXsxbHaYRBBjz/rvu97sOC7kzXI=" crossorigin="anonymous">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.0/css/all.min.css">
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/admin.css?v=<?php echo VERSION; ?>" />
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha256-NfRUfZNkERrKSFA0c1a8VmCplPDYtpTYj5lQmKe1R/o=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.0"></script>
		<script src="<?php echo STATIC_URL; ?>/js/lang_<?php echo SITE_LANGUAGE; ?>.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/admin_lang_<?php echo SITE_LANGUAGE; ?>.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/admin.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/uncompress.js"></script>
	</head>
	<body>
<?php
if (empty($skip_navbar) && !empty($_SESSION['username']) && !empty($_SESSION['admin_level']) && $_SESSION['admin_level']>=1) {
?>
		<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
			<div class="container-fluid">
				<span class="navbar-brand"><a href="<?php echo ADMIN_URL; ?>/"><?php echo lang('admin.menu.title'); ?></a> <small><small><a href="https://github.com/fansubscat/Fansubs.cat/blob/master/CHANGELOG.md#registre-de-canvis" target="_blank" title="<?php echo lang('admin.menu.changelog'); ?>">v<?php echo VERSION; ?></a></small></small></span>
				<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="<?php echo lang('admin.menu.toggle'); ?>">
					<span class="navbar-toggler-icon"></span>
				</button>
				<div class="collapse navbar-collapse" id="navbarSupportedContent">
					<ul class="navbar-nav me-auto">
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='anime' ? ' active' : ''; ?>" href="#" id="navbarDropdownAnime" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.anime'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownAnime">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=anime"><?php echo lang('admin.menu.anime.series_list'); ?></a>
								<a class="dropdown-item" href="series_edit.php?type=anime"><?php echo lang('admin.menu.anime.series_add'); ?></a>
								<div class="dropdown-divider"></div>
<?php
	}
?>
								<a class="dropdown-item" href="version_list.php?type=anime"><?php echo lang('admin.menu.anime.version_list'); ?></a>
								<a class="dropdown-item" href="series_choose.php?type=anime"><?php echo lang('admin.menu.anime.version_add'); ?></a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='manga' ? ' active' : ''; ?>" href="#" id="navbarDropdownManga" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.manga'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownManga">
<?php
	if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=manga"><?php echo lang('admin.menu.manga.series_list'); ?></a>
								<a class="dropdown-item" href="series_edit.php?type=manga"><?php echo lang('admin.menu.manga.series_add'); ?></a>
								<div class="dropdown-divider"></div>
<?php
	}
?>
								<a class="dropdown-item" href="version_list.php?type=manga"><?php echo lang('admin.menu.manga.version_list'); ?></a>
								<a class="dropdown-item" href="series_choose.php?type=manga"><?php echo lang('admin.menu.manga.version_add'); ?></a>
							</div>
						</li>
<?php
	if (!DISABLE_LIVE_ACTION) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='liveaction' ? ' active' : ''; ?>" href="#" id="navbarDropdownLiveAction" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.liveaction'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownLiveAction">
<?php
		if ($_SESSION['admin_level']>=2) {
?>
								<a class="dropdown-item" href="series_list.php?type=liveaction"><?php echo lang('admin.menu.liveaction.series_list'); ?></a>
								<a class="dropdown-item" href="series_edit.php?type=liveaction"><?php echo lang('admin.menu.liveaction.series_add'); ?></a>
								<div class="dropdown-divider"></div>
<?php
		}
?>
								<a class="dropdown-item" href="version_list.php?type=liveaction"><?php echo lang('admin.menu.liveaction.version_list'); ?></a>
								<a class="dropdown-item" href="series_choose.php?type=liveaction"><?php echo lang('admin.menu.liveaction.version_add'); ?></a>
							</div>
						</li>
<?php
	}
	if (!DISABLE_NEWS) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='news' ? ' active' : ''; ?>" href="#" id="navbarDropdownNews" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.news'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownNews">
								<a class="dropdown-item" href="news_list.php"><?php echo lang('admin.menu.news.news_list'); ?></a>
								<a class="dropdown-item" href="news_edit.php"><?php echo lang('admin.menu.news.news_add'); ?></a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="news_fetcher_list.php"><?php echo lang('admin.menu.news.fetcher_list'); ?></a>
								<a class="dropdown-item" href="news_fetcher_edit.php"><?php echo lang('admin.menu.news.fetcher_add'); ?></a>
<?php
		}
?>
							</div>
						</li>
<?php
	}
	if ($_SESSION['admin_level']>=2) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='fansub' ? ' active' : ''; ?>" href="#" id="navbarDropdownFansubs" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.fansubs'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownFansubs">
								<a class="dropdown-item" href="fansub_list.php"><?php echo lang('admin.menu.fansubs.fansub_list'); ?></a>
<?php
		if ($_SESSION['admin_level']>=3) {
?>
								<a class="dropdown-item" href="fansub_edit.php"><?php echo lang('admin.menu.fansubs.fansub_add'); ?></a>
<?php
		}
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="remote_account_list.php"><?php echo lang('admin.menu.fansubs.remote_account_list'); ?></a>
								<a class="dropdown-item" href="remote_account_edit.php"><?php echo lang('admin.menu.fansubs.remote_account_add'); ?></a>
							</div>
						</li>
<?php
	}
	if ($_SESSION['admin_level']>=3) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='other' ? ' active' : ''; ?>" href="#" id="navbarDropdownOthers" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.others'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownOthers">
								<a class="dropdown-item" href="admin_list.php"><?php echo lang('admin.menu.others.admin_list'); ?></a>
								<a class="dropdown-item" href="admin_edit.php"><?php echo lang('admin.menu.others.admin_add'); ?></a>
<?php
		if (!DISABLE_LINKS) {
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="link_list.php"><?php echo lang('admin.menu.others.link_list'); ?></a>
								<a class="dropdown-item" href="link_edit.php"><?php echo lang('admin.menu.others.link_add'); ?></a>
<?php
		}
		if (!DISABLE_ADVENT) {
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="advent_list.php"><?php echo lang('admin.menu.others.advent_list'); ?></a>
<?php
		}
?>
							</div>
						</li>
<?php
	}
	if ($_SESSION['admin_level']>=1) {
?>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='analytics' ? ' active' : ''; ?>" href="#" id="navbarDropdownAnalytics" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.analysis'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownAnalytics">
								<a class="dropdown-item" href="views.php?type=anime"><?php echo lang('admin.menu.analysis.anime_views'); ?></a>
								<a class="dropdown-item" href="views.php?type=manga"><?php echo lang('admin.menu.analysis.manga_views'); ?></a>
<?php
		if (!DISABLE_LIVE_ACTION) {
?>
								<a class="dropdown-item" href="views.php?type=liveaction"><?php echo lang('admin.menu.analysis.liveaction_views'); ?></a>
<?php
		}
?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="comment_list.php"><?php echo lang('admin.menu.analysis.latest_comments'); ?></a>
								<a class="dropdown-item" href="popular.php"><?php echo lang('admin.menu.analysis.popular'); ?></a>
								<a class="dropdown-item" href="stats.php"><?php echo lang('admin.menu.analysis.stats'); ?></a>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" href="error_list.php"><?php echo lang('admin.menu.analysis.errors'); ?></a>
							</div>
						</li>
						<li class="nav-item dropdown">
							<a class="nav-link dropdown-toggle<?php echo $page=='tools' ? ' active' : ''; ?>" href="#" id="navbarDropdownTools" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo lang('admin.menu.tools'); ?></a>
							<div class="dropdown-menu" aria-labelledby="navbarDropdownTools">
								<a class="dropdown-item" href="link_verifier.php"><?php echo lang('admin.menu.tools.link_verifier'); ?></a>
<?php
		if (!DISABLE_NEWS) {
?>
								<a class="dropdown-item" href="news_fetcher_status.php"><?php echo lang('admin.menu.tools.news_fetcher_status'); ?></a>
<?php
		}
		if ($_SESSION['admin_level']>=3) {
?>
								<a class="dropdown-item" href="admin_log.php"><?php echo lang('admin.menu.tools.admin_log'); ?></a>
								<a class="dropdown-item" href="pending_conversions.php"><?php echo lang('admin.menu.tools.pending_conversions'); ?></a>
								<a class="dropdown-item" href="maintenance.php"><?php echo lang('admin.menu.tools.maintenance'); ?></a>
<?php
		}
?>
								<a class="dropdown-item" href="change_password.php"><?php echo lang('admin.menu.tools.change_password'); ?></a>
							</div>
						</li>
<?php
	}
?>
					</ul>
					<a class="navbar-extra-link pe-3" href="<?php echo MAIN_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.main'), MAIN_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-main fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo ANIME_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.anime'), ANIME_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-anime fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
					<a class="navbar-extra-link pe-3" href="<?php echo MANGA_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.manga'), MANGA_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-manga fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	if (!DISABLE_LIVE_ACTION) {
?>
					<a class="navbar-extra-link pe-3" href="<?php echo LIVEACTION_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.liveaction'), LIVEACTION_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-liveaction fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	}
	if (!DISABLE_COMMUNITY) {
?>
					<a class="navbar-extra-link pe-3" href="<?php echo COMMUNITY_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.community'), COMMUNITY_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-community fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	}
	if (!DISABLE_NEWS) {
?>
					<a class="navbar-extra-link pe-3" href="<?php echo NEWS_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.news'), NEWS_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-news fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	}
?>
					<a class="navbar-extra-link pe-3" href="https://<?php echo MAIN_SUBDOMAIN.'.'.HENTAI_DOMAIN; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.hentai'), MAIN_SUBDOMAIN.'.'.HENTAI_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-hentai fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	if (!DISABLE_RESOURCES) {
?>
					<a class="navbar-extra-link pe-3" href="<?php echo RESOURCES_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.resources'), RESOURCES_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-resources fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	}
	if (!DISABLE_ADVENT) {
?>
					<a class="navbar-extra-link pe-3" href="<?php echo ADVENT_URL; ?>/" target="_blank" title="<?php echo sprintf(lang('admin.menu.quicklinks.advent'), ADVENT_SUBDOMAIN.'.'.MAIN_DOMAIN); ?>"><span class="fa-stack"><span class="fa fa-globe fa-stack-2x"></span><span class="fa fa-custom-advent fa-stack-1x" style="margin-top: 0.5em; margin-left: 0.75em;"></span></span></a>
<?php
	}
?>
					<a class="navbar-extra-link pe-3" href="<?php echo ADMIN_TUTORIAL_URL; ?>" target="_blank" title="<?php echo lang('admin.menu.help'); ?>"><span class="fa fa-question-circle"></span></a>
					<a class="navbar-extra-link" href="logout.php" title="<?php echo lang('admin.menu.logout'); ?>"><?php echo htmlspecialchars($_SESSION['username']); ?><span class="fa fa-sign-out-alt ms-2"></span></a>
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
						<?php echo lang('admin.generic.close'); ?>
					</button>
				</div>
			</div>
		</div>
<?php
}
?>

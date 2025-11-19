<?php
require_once(__DIR__.'/user_init.inc.php');
require_once(__DIR__.'/common.inc.php');

if (!empty($user)) {
	define('SITE_THEME', $user['site_theme']==1 ? 'light' : 'dark');
} else if (!empty($_COOKIE['site_theme']) && $_COOKIE['site_theme']=='light') {
	define('SITE_THEME', 'light');
} else {
	define('SITE_THEME', 'dark');
}

if ((defined('PAGE_DISABLED_IF_HENTAI') && PAGE_DISABLED_IF_HENTAI && SITE_IS_HENTAI) || (DISABLE_LINKS && defined('PAGE_PATH') && PAGE_PATH==lang('url.links'))) {
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: /");
}

$special_day = get_special_day();
?>
<!DOCTYPE html>
<html lang="<?php echo SITE_LANGUAGE; ?>" class="theme-<?php echo SITE_THEME; ?><?php echo SITE_IS_HENTAI ? ' subtheme-hentai' : ''; ?><?php echo SITE_IS_HENTAI && empty($_COOKIE['hentai_warning_accepted']) ? ' page-no-overflow' : ''; ?>">
	<head>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
<?php
if (SITE_IS_HENTAI) {
?>
		<meta name="theme-color" content="#d91883">
		<meta name="rating" content="RTA-5042-1996-1400-1577-RTA">
<?php
} else {
?>
		<meta name="theme-color" content="#6aa0f8">
<?php
}
?>
		<meta name="description" content="<?php echo htmlspecialchars(defined('PAGE_DESCRIPTION') ? PAGE_DESCRIPTION : SITE_DESCRIPTION); ?>">
		<meta name="referrer" content="origin">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-config" content="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/browserconfig.xml">
		<meta name="base_url" content="<?php echo SITE_BASE_URL; ?>">
		<meta name="current_domain" content="<?php echo CURRENT_DOMAIN; ?>">
		<meta name="users_url" content="<?php echo USERS_URL; ?>">
		<meta name="main_url" content="<?php echo MAIN_URL; ?>">
		<meta name="site_name" content="<?php echo CURRENT_SITE_NAME; ?>">
		<meta property="og:title" content="<?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?>">
		<meta property="og:url" content="<?php echo htmlspecialchars(defined('PAGE_PATH') ? SITE_BASE_URL.PAGE_PATH : SITE_BASE_URL); ?>">
		<meta property="og:description" content="<?php echo htmlspecialchars(defined('PAGE_DESCRIPTION') ? PAGE_DESCRIPTION : SITE_DESCRIPTION); ?>">
		<meta property="og:image" content="<?php echo htmlspecialchars(defined('PAGE_PREVIEW_IMAGE') ? PAGE_PREVIEW_IMAGE : STATIC_URL.'/social/'.SITE_PREVIEW_IMAGE.(SITE_IS_HENTAI ? '_hentai' : '').'.jpg'); ?>">
		<meta property="og:image:type" content="image/jpeg">
		<title><?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?></title>
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/favicon-16x16.png">
		<link rel="manifest" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/site.webmanifest">
		<link rel="mask-icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/safari-pinned-tab.svg" color="<?php echo SITE_IS_HENTAI ? '#d91883' : '#6aa0f8'; ?>">
		<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME.(SITE_IS_HENTAI ? '_hentai' : ''); ?>/favicon.ico">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@12.0.3/swiper-bundle.min.css">
<?php
if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='embed') {
?>
		<link rel="stylesheet" href="https://vjs.zencdn.net/8.23.4/video-js.css">
<?php
} else if (PAGE_STYLE_TYPE=='news') {
?>
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/magnific-popup-1.2.0.css">
<?php
}
?>
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/common.css?v=<?php echo VERSION; ?>">
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/<?php echo SITE_INTERNAL_TYPE; ?>.css?v=<?php echo VERSION; ?>">
<?php
if ($special_day=='fools'){
?>
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/28dec.css?v=<?php echo VERSION; ?>">
<?php
}
?>
		<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/swiper@12.0.3/swiper-bundle.min.js"></script>
		<script>
			window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
				preloadWebComponents: true,
			};
		</script>
<?php
if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='embed') {
?>
		<script src="https://unpkg.com/megajs@1.3.9/dist/main.browser-umd.js"></script>
		<script src="https://vjs.zencdn.net/8.23.4/video.min.js"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videostream.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-lang_<?php echo SITE_LANGUAGE; ?>.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-chromecast.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-landscape-fullscreen.min.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-hotkeys.min.js?v=<?php echo VERSION; ?>"></script>
		<script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
<?php
} else if (PAGE_STYLE_TYPE=='news') {
?>
		<script src="<?php echo STATIC_URL; ?>/js/jquery.magnific-popup-1.2.0.min.js?v=<?php echo VERSION; ?>"></script>
<?php
}
?>
		<script src="<?php echo STATIC_URL; ?>/js/double-slider.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/lang_<?php echo SITE_LANGUAGE; ?>.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/common.js?v=<?php echo VERSION; ?>"></script>
<?php
	if (!defined('ERROR_PAGE')) {
?>
		<script src="<?php echo STATIC_URL; ?>/js/<?php echo SITE_INTERNAL_TYPE; ?>.js?v=<?php echo VERSION; ?>"></script>
<?php
	}
?>
	</head>
	<body class="style-type-<?php echo PAGE_STYLE_TYPE; ?><?php echo defined('PAGE_EXTRA_BODY_CLASS') ? ' '.PAGE_EXTRA_BODY_CLASS : ''; ?><?php echo !empty($user) ? ' user-logged-in' : ''; ?>">
		<div class="main-container<?php echo (PAGE_STYLE_TYPE=='login' || PAGE_STYLE_TYPE=='text' || PAGE_STYLE_TYPE=='contact') ? ' obscured-background' : ''; ?>">
<?php
if (SITE_IS_HENTAI && !is_robot() && empty($_COOKIE['hentai_warning_accepted'])) {
?>
			<div data-nosnippet id="warning-overlay" class="flex">
				<div id="warning-overlay-content">
					<h2 id="warning-title"><?php echo lang('hentai.warning.title'); ?></h2>
					<div id="warning-message"><?php echo lang('hentai.warning.disclaimer_part_1'); ?></div>
					<div id="warning-post-explanation"><?php echo sprintf(lang('hentai.warning.disclaimer_part_2'), CURRENT_SITE_NAME); ?></div>
					<div id="warning-buttonbar">
						<button id="warning-ok-button" class="normal-button" onclick="acceptHentaiWarning();"><?php echo lang('hentai.warning.accept'); ?></button>
						<button id="warning-close-button" class="normal-button" onclick="window.location.href='<?php echo 'https://www.'.MAIN_DOMAIN; ?>';"><?php echo sprintf(lang('hentai.warning.go_back'), MAIN_SITE_NAME); ?></button>
					</div>
				</div>
			</div>
<?php
}
if (PAGE_STYLE_TYPE=='login') {
?>
			<div class="overlay-page">
				<div class="login-page">
					<input id="redirect" type="hidden" value="<?php echo htmlspecialchars(get_redirect_from_referrer()); ?>">
					<div class="login-explanation">
						<div class="login-header"><?php echo sprintf(lang('users.register.title'), CURRENT_SITE_NAME); ?></div>
<?php
	if (SITE_IS_HENTAI) {
?>
						<div class="login-shared"><?php echo sprintf(lang('users.register.users_shared'), MAIN_SITE_NAME); ?></div>
<?php
	}
?>
						<div class="login-points">
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-bookmark"></div>
								<div class="login-point-text"><?php echo lang('users.register.point.save_content'); ?></div>
							</div>
							<div class="login-point">
								<div class="login-point-text"><?php echo lang('users.register.point.recommendations'); ?></div>
								<div class="login-point-icon fas fa-fw fa-star"></div>
							</div>
<?php
	if (!DISABLE_COMMUNITY) {
?>
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-comment"></div>
								<div class="login-point-text"><?php echo lang('users.register.point.community'); ?></div>
							</div>
<?php
	} else {
?>
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-thumbs-up"></div>
								<div class="login-point-text"><?php echo lang('users.register.point.rate_translations'); ?></div>
							</div>
<?php
	}
?>
							<div class="login-point">
								<div class="login-point-text"><?php echo lang('users.register.point.sync_progress'); ?></div>
								<div class="login-point-icon fas fa-fw fa-house-laptop"></div>
							</div>
						</div>
					</div>
					<div class="login-form<?php echo defined('PAGE_IS_RESET_PASSWORD') ? ' hidden' : ''; ?>">
						<div class="login-form-main">
							<div class="login-subheader"><?php echo lang('users.login.subtitle'); ?></div>
							<form id="login-form" onsubmit="return login();" autocomplete="off" novalidate>
								<label for="login_username"><?php echo lang('users.login.username'); ?></label>
								<input id="login_username" type="text" oninput="removeValidation(this.id);">
								<div id="login_username_validation" class="validation-message"></div>
								<label for="login_password"><?php echo lang('users.login.password'); ?></label>
								<input id="login_password" type="password" oninput="removeValidation(this.id);">
								<div id="login_password_validation" class="validation-message"></div>
								<div id="login_generic_validation" class="validation-message-generic"></div>
								<button id="login_submit" type="submit" class="login-button"><?php echo lang('users.login.login_button'); ?></button>
								<a class="forgot-password" onclick="showForgotPassword();"><?php echo lang('users.login.forgot_password'); ?></a>
							</form>
						</div>
						<div class="login-footer"><?php echo lang('users.login.not_a_member'); ?> <a onclick="showRegister();"><?php echo lang('users.login.register_button'); ?></a></div>
					</div>
					<div class="reset-password-form<?php echo !defined('PAGE_IS_RESET_PASSWORD') ? ' hidden' : ''?>">
						<div class="login-form-main">
							<div class="login-subheader"><?php echo lang('users.reset_password.subtitle'); ?></div>
							<form id="reset-password-form" onsubmit="return resetPassword();" autocomplete="off" novalidate>
								<label for="reset_password"><?php echo lang('users.reset_password.new_password'); ?></label>
								<input id="reset_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_password_validation" class="validation-message"></div>
								<label for="reset_repeat_password"><?php echo lang('users.reset_password.repeat_password'); ?></label>
								<input id="reset_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_repeat_password_validation" class="validation-message"></div>
								<div id="reset_generic_validation" class="validation-message-generic"></div>
								<input id="reset_username" type="hidden" value="<?php echo !empty($_GET['user']) ? htmlspecialchars($_GET['user']) : ''; ?>">
								<input id="reset_code" type="hidden" value="<?php echo !empty($_GET['code']) ? htmlspecialchars($_GET['code']) : ''; ?>">
								<button id="reset_submit" type="submit" class="login-button"><?php echo lang('users.reset_password.confirm_button'); ?></button>
							</form>
						</div>
						<div class="login-footer"><?php echo lang('users.reset_password.remembered_password'); ?> <a onclick="showLogin();"><?php echo lang('users.reset_password.login'); ?></a></div>
					</div>
					<div class="forgot-password-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader"><?php echo lang('users.forgot_password.subtitle'); ?></div>
							<form id="forgot-password-form" onsubmit="return forgotPassword();" autocomplete="off" novalidate>
								<label for="forgot_email"><?php echo lang('users.forgot_password.email'); ?></label>
								<input id="forgot_email" type="email" oninput="removeValidation(this.id);">
								<div id="forgot_email_validation" class="validation-message"></div>
								<div id="forgot_generic_validation" class="validation-message-generic"></div>
								<button id="forgot_submit" type="submit" class="login-button"><?php echo lang('users.forgot_password.confirm_button'); ?></button>
							</form>
						</div>
						<div class="login-footer"><?php echo lang('users.forgot_password.mistaken'); ?> <a onclick="showLogin();"><?php echo lang('users.forgot_password.login'); ?></a></div>
					</div>
					<div class="forgot-password-result-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader"><?php echo lang('users.forgot_password.submitted.subtitle'); ?></div>
							<div class="forgot-password-result-text"><?php echo lang('users.forgot_password.submitted.explanation'); ?></div>
						</div>
						<div class="login-footer"><?php echo lang('users.forgot_password.submitted.already_changed'); ?> <a onclick="showLogin();"><?php echo lang('users.forgot_password.submitted.login'); ?></a></div>
					</div>
					<div class="register-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader"><?php echo lang('users.register.subtitle'); ?></div>
							<form id="register-form" onsubmit="return register();" autocomplete="off" novalidate>
								<label for="register_username"><?php echo lang('users.register.username'); ?></label>
								<input id="register_username" type="text" oninput="removeValidation(this.id);">
								<div id="register_username_validation" class="validation-message"></div>
								<label for="register_password"><?php echo lang('users.register.password'); ?></label>
								<input id="register_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_password_validation" class="validation-message"></div>
								<label for="register_repeat_password"><?php echo lang('users.register.repeat_password'); ?></label>
								<input id="register_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_repeat_password_validation" class="validation-message"></div>
								<label for="register_email"><?php echo lang('users.register.email'); ?></label>
								<input id="register_email" type="email" oninput="removeValidation(this.id);">
								<div id="register_email_validation" class="validation-message"></div>
								<label for="register_birthday_day"><?php echo lang('users.register.birth_date'); ?></label>
								<div class="date-chooser">
									<input class="date-day" id="register_birthday_day" type="text" maxlength="2" oninput="removeValidationOnlyText('register_birthday');" placeholder="<?php echo lang('users.register.day'); ?>">
									<select class="date-month" id="register_birthday_month" onchange="removeValidationOnlyText('register_birthday');">
										<option value="" disabled selected><?php echo lang('users.register.month'); ?></option>
<?php
	for ($i=1;$i<=12;$i++) {
		if ($i<10) {
			$month='0'.$i;
		} else {
			$month=$i;
		}
?>
										<option value="<?php echo $month; ?>"><?php echo lang('users.register.month.'.$month); ?></option>
<?php
	}
?>
									</select>
									<input class="date-year" id="register_birthday_year" type="text" maxlength="4" oninput="removeValidationOnlyText('register_birthday');" placeholder="<?php echo lang('users.register.year'); ?>">
								</div>
								<div id="register_birthday_validation" class="validation-message"></div>
								<label for="register_pronoun"><?php echo lang('users.register.pronoun'); ?></label>
								<select id="register_pronoun" onchange="removeValidation(this.id);">
									<option value="" disabled selected><?php echo lang('users.register.pronoun.select'); ?></option>
									<option value="male"><?php echo lang('users.register.pronoun.male'); ?></option>
									<option value="female"><?php echo lang('users.register.pronoun.female'); ?></option>
									<option value="nonbinary"><?php echo lang('users.register.pronoun.other'); ?></option>
								</select>
								<div id="register_pronoun_validation" class="validation-message"></div>
								<div class="checkbox-layout">
									<input id="register_privacy_policy_accept" type="checkbox" onchange="removeValidationOnlyText('register_privacy_policy_accept');">
									<label for="register_privacy_policy_accept"><?php echo lang('users.register.accept_privacy_policy_1'); ?><a href="<?php echo MAIN_URL.lang('url.privacy_policy'); ?>" target="_blank"><?php echo lang('users.register.accept_privacy_policy_2'); ?></a></label>
								</div>
								<div id="register_privacy_policy_accept_validation" class="validation-message"></div>
								<div id="register_generic_validation" class="validation-message-generic"></div>
								<button id="register_submit" type="submit" class="login-button"><?php echo lang('users.register.confirm_button'); ?></button>
							</form>
						</div>
						<div class="login-footer"><?php echo lang('users.register.already_registered'); ?> <a onclick="showLogin();"><?php echo lang('users.register.login'); ?></a></div>
					</div>
				</div>
			</div>
<?php
} else {
	if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='embed') {
?>
			<div data-nosnippet id="overlay" class="hidden">
				<div id="overlay-content"></div>
			</div>
<?php
	}
?>
			<div class="main-body">
<?php
	if (PAGE_STYLE_TYPE!='embed') {
?>
				<div class="header">
<?php
		if (PAGE_STYLE_TYPE=='main') {
?>
					<a class="social-link bluesky-link fab fa-bluesky" href="<?php echo SOCIAL_LINK_BLUESKY; ?>" target="_blank" title="<?php echo sprintf(lang('main.social_link_alt.bluesky'), CURRENT_SITE_NAME); ?>"></a>
					<a class="social-link mastodon-link fab fa-mastodon" href="<?php echo SOCIAL_LINK_MASTODON; ?>" target="_blank" title="<?php echo sprintf(lang('main.social_link_alt.mastodon'), CURRENT_SITE_NAME); ?>" rel="me"></a>
					<a class="social-link telegram-link fab fa-telegram" href="<?php echo SOCIAL_LINK_TELEGRAM; ?>" target="_blank" title="<?php echo sprintf(lang('main.social_link_alt.telegram'), CURRENT_SITE_NAME); ?>"></a>
					<a class="social-link twitter-link fab fa-x-twitter" href="<?php echo SOCIAL_LINK_X; ?>" target="_blank" title="<?php echo sprintf(lang('main.social_link_alt.x'), CURRENT_SITE_NAME); ?>"></a>
<?php
		} else {
?>
					<a class="logo-small" href="<?php echo MAIN_URL; ?>" title="<?php echo sprintf(lang('main.header.go_back'), CURRENT_SITE_NAME); ?>">
						<?php include(STATIC_DIRECTORY.'/images/site/'.(SITE_IS_HENTAI ? 'logo_hentai.svg' : 'logo.svg')); ?>
<?php
			if (!empty($special_day) && file_exists(STATIC_DIRECTORY.'/images/site/logo_'.(SITE_IS_HENTAI ? 'hentai_' : '').'layer_'.$special_day.'.png')) {
?>
						<img class="logo-layer-small" src="<?php echo STATIC_URL; ?>/images/site/logo_<?php echo SITE_IS_HENTAI ? 'hentai_' : ''; ?>layer_<?php echo $special_day; ?>.png">
<?php
			}
?>
					</a>
<?php
			if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='news' || PAGE_STYLE_TYPE=='fansubs' || PAGE_STYLE_TYPE=='settings') {
?>
					<div class="catalogues-navigation">
						<a href="<?php echo ANIME_URL; ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='anime' ? ' class="catalogue-selected"' : ''; ?>><?php echo lang('main.header.anime'); ?></a>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo MANGA_URL; ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='manga' ? ' class="catalogue-selected"' : ''; ?>><?php echo lang('main.header.manga'); ?></a>
<?php
					if (!SITE_IS_HENTAI && !DISABLE_LIVE_ACTION) {
?>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo LIVEACTION_URL; ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='liveaction' ? ' class="catalogue-selected"' : ''; ?>><?php echo lang('main.header.liveaction'); ?></a>
<?php
					}
					if (!DISABLE_COMMUNITY && !DISABLE_NEWS) {
?>
						<span class="catalogues-newline"></span>
						<span class="catalogues-separator catalogues-newline-separator">|</span>
<?php
						if (!SITE_IS_HENTAI) {
?>
						<a href="<?php echo COMMUNITY_URL; ?>"><?php echo lang('main.header.community'); ?></a>
						<span class="catalogues-separator">|</span>
<?php
						}
?>
						<a href="<?php echo NEWS_URL; ?>"<?php echo PAGE_STYLE_TYPE=='news' ? ' class="catalogue-selected"' : ''; ?>><?php echo lang('main.header.news'); ?></a>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo MAIN_URL.lang('url.fansubs'); ?>"<?php echo PAGE_STYLE_TYPE=='fansubs' ? ' class="catalogue-selected"' : ''; ?>><?php echo lang('main.header.fansubs'); ?></a>
<?php
					}
?>
						<span class="catalogues-underline"></span>
					</div>
<?php
			}
		}
?>
					<div class="separator">
<?php
		if ((!defined('CATALOGUE_ITEM_TYPE') || CATALOGUE_ITEM_TYPE!='liveaction') && !SITE_IS_HENTAI && (is_robot() || (!empty($user) && is_adult() && empty($user['hide_hentai_access'])))) {
?>
						<a class="hentai-button" href="<?php echo get_opposite_url(); ?>" title="<?php echo sprintf(lang('main.header.change_button.hentai'), HENTAI_SITE_NAME); ?>">
							<i class="fsc fa-fw fsc-hentai fa-2x"></i>
						</a>
<?php
		} else if (SITE_IS_HENTAI) {
?>
						<a class="hentai-button" href="<?php echo get_opposite_url(); ?>" title="<?php echo sprintf(lang('main.header.change_button'), MAIN_SITE_NAME); ?>">
							<i class="fa fa-solid fa-fw fa-house-chimney fa-2x"></i>
						</a>
<?php
		}
		if (PAGE_STYLE_TYPE=='catalogue' && !defined('PAGE_IS_SEARCH')) {
?>
						<a class="filter-button" href="<?php echo SITE_BASE_URL.lang('url.search'); ?>" title="<?php echo lang('main.header.filter_catalogue'); ?>">
							<i class="fsc fa-fw fsc-catalogue fa-2x"></i>
						</a>
						<a class="filter-button mobile-search-button" href="<?php echo SITE_BASE_URL.lang('url.search'); ?>?focus=1" title="<?php echo lang('main.header.search'); ?>">
							<i class="fa fa-fw fa-search fa-2x"></i>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="" placeholder="<?php echo lang('main.header.search.placeholder_catalogue'); ?>" autocomplete="off">
								<i id="search_button" class="fa fa-search" title="<?php echo lang('main.header.search.alt_catalogue'); ?>"></i>
								<div id="search_query_autocomplete" class="hidden"></div>
							</form>
						</div>
<?php
		} else if (PAGE_STYLE_TYPE=='news' && !defined('PAGE_IS_SEARCH')) {
?>
						<a class="filter-button" href="<?php echo lang('url.search'); ?>" title="<?php echo lang('main.header.filter_news'); ?>">
							<i class="fsc fa-fw fsc-news fa-2x"></i>
						</a>
						<a class="filter-button mobile-search-button" href="<?php echo SITE_BASE_URL.lang('url.search'); ?>?focus=1" title="<?php echo lang('main.header.search'); ?>">
							<i class="fa fa-fw fa-search fa-2x"></i>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="" placeholder="<?php echo lang('main.header.search.placeholder_news'); ?>" autocomplete="off">
								<i id="search_button" class="fa fa-search" title="<?php echo lang('main.header.search.alt_news'); ?>"></i>
							</form>
						</div>
<?php
		}
?>
					</div>
<?php
		if (empty($user)) {
?>
					<a class="user-login" href="<?php echo USERS_URL.lang('url.login'); ?>"><span class="user-login-text"><?php echo lang('main.header.login'); ?></span><span class="user-login-icon"><i class="fa fa-fw fa-sign-in"></i></span></a>
<?php
		}
?>
					<div class="user-options">
						<div class="dropdown-menu">
<?php
		if (!empty($user)) {
?>
							<img alt="<?php echo lang('main.header.menu.alt'); ?>" onclick="showUserDropdown();" class="user-avatar dropdown-button" src="<?php echo get_user_avatar_url($user); ?>">
<?php
		} else {
?>
							<div onclick="showUserDropdown();" class="anon-avatar dropdown-button"><i class="fa fa-gear"></i></div>
<?php
		}
?>
							<div id="user-dropdown" class="dropdown-content">
								<div class="dropdown-title"><?php echo !empty($user) ? $user['username'] : lang('main.header.menu.options'); ?></div>
								<hr class="dropdown-separator">
<?php
		if (!empty($user)) {
?>
								<a href="<?php echo USERS_URL; ?>"><i class="fa fa-fw fa-user"></i> <?php echo lang('main.header.menu.my_profile'); ?></a>
								<a href="<?php echo USERS_URL.lang('url.my_list'); ?>"><i class="fa fa-fw fa-bookmark"></i> <?php echo lang('main.header.menu.my_list'); ?></a>
								<hr class="dropdown-separator-secondary">
<?php
		}
?>
								<a href="<?php echo USERS_URL.lang('url.settings'); ?>"><i class="fa fa-fw fa-gear"></i> <?php echo lang('main.header.menu.settings'); ?></a>
<?php
		if (!defined('SITE_THEME_FORCED')) {
?>
								<a class="theme-button" onclick="toggleSiteTheme();"><i class="fa fa-fw fa-circle-half-stroke"></i> <span class="theme-button-text"><?php echo SITE_THEME=='light' ? lang('main.header.menu.change_theme.dark') : lang('main.header.menu.change_theme.light'); ?></span></a>
<?php
		}
?>
								<hr class="dropdown-separator-secondary">
<?php
		if (!empty($user)) {
?>
								<a href="<?php echo USERS_URL.lang('url.logout'); ?>"><i class="fa fa-fw fa-sign-out"></i> <?php echo lang('main.header.menu.logout'); ?></a>
<?php
		} else {
?>
								<a href="<?php echo USERS_URL.lang('url.login'); ?>"><i class="fa fa-fw fa-sign-in"></i> <?php echo lang('main.header.menu.login'); ?></a>
<?php
		}
?>
							</div>
						</div>
					</div>
				</div>
<?php

		if (GLOBAL_MESSAGE!='' || $special_day=='fools'){
?>
				<div data-nosnippet class="site-message"><?php echo $special_day=='fools' ? lang('main.header.fools_message').' <a href="'.STATIC_URL.'/various/innocents.png" target="_blank" style="color: black;">'.lang('main.header.fools_message.more_info').'</a>.' : GLOBAL_MESSAGE; ?></div>
<?php
		}
	}
?>
				<div class="main-section">
<?php
}
?>

<?php
require_once(dirname(__FILE__)."/user_init.inc.php");
require_once(dirname(__FILE__)."/common.inc.php");

/*if (PAGE_STYLE_TYPE=='main' || PAGE_STYLE_TYPE=='text' || PAGE_STYLE_TYPE=='contact') {
	define('SITE_THEME', 'dark');
	define('SITE_THEME_FORCED', TRUE);
}
else */if (!empty($user)) {
	define('SITE_THEME', $user['site_theme']==1 ? 'light' : 'dark');
} else if (!empty($_COOKIE['site_theme']) && $_COOKIE['site_theme']=='light') {
	define('SITE_THEME', 'light');
} else {
	define('SITE_THEME', 'dark');
}

$special_day = get_special_day();
?>
<!DOCTYPE html>
<html lang="ca" class="theme-<?php echo SITE_THEME; ?><?php echo SITE_IS_HENTAI ? ' subtheme-hentai' : ''; ?><?php echo SITE_IS_HENTAI && empty($_COOKIE['hentai_warning_accepted']) ? ' page-no-overflow' : ''; ?>">
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
		<meta name="robots" content="noindex"> <!-- TODO REMOVE THIS BEFORE ENTERING PRODUCTION AND REVIEW ALL robots.txt fansubs.online!!! -->
		<meta name="referrer" content="origin">
		<meta name="base_url" content="<?php echo SITE_BASE_URL; ?>">
		<meta name="twitter:card" content="summary_large_image">
		<meta name="msapplication-TileColor" content="#da532c">
		<meta name="msapplication-config" content="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/browserconfig.xml">
		<meta property="og:title" content="<?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?>">
		<meta property="og:url" content="<?php echo htmlspecialchars(defined('PAGE_PATH') ? SITE_BASE_URL.PAGE_PATH : SITE_BASE_URL); ?>">
		<meta property="og:description" content="<?php echo htmlspecialchars(defined('PAGE_DESCRIPTION') ? PAGE_DESCRIPTION : SITE_DESCRIPTION); ?>">
		<meta property="og:image" content="<?php echo htmlspecialchars(defined('PAGE_PREVIEW_IMAGE') ? PAGE_PREVIEW_IMAGE : STATIC_URL.'/social/'.SITE_PREVIEW_IMAGE.'.jpg'); ?>">
		<meta property="og:image:type" content="image/jpeg">
		<title><?php echo htmlspecialchars(defined('PAGE_TITLE') ? PAGE_TITLE.' | '.SITE_TITLE : SITE_TITLE); ?></title>
		<link rel="apple-touch-icon" sizes="180x180" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/apple-touch-icon.png">
		<link rel="icon" type="image/png" sizes="32x32" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon-32x32.png">
		<link rel="icon" type="image/png" sizes="16x16" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon-16x16.png">
		<link rel="manifest" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/site.webmanifest">
		<link rel="mask-icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/safari-pinned-tab.svg" color="#6aa0f8">
		<link rel="shortcut icon" href="<?php echo STATIC_URL; ?>/favicons/<?php echo SITE_INTERNAL_NAME; ?>/favicon.ico">
		<link rel="stylesheet" href="https://use.fontawesome.com/releases/v6.4.2/css/all.css">
		<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10.3.1/swiper-bundle.min.css">
<?php
if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='embed') {
?>
		<link rel="stylesheet" href="https://vjs.zencdn.net/8.5.2/video-js.css">
<?php
} else if (PAGE_STYLE_TYPE=='news') {
?>
		<link rel="stylesheet" href="<?php echo STATIC_URL; ?>/css/magnific-popup-1.1.0.css">
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
		<script src="https://cdn.jsdelivr.net/npm/swiper@10.3.1/swiper-bundle.min.js"></script>
<?php
if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='embed') {
?>
		<script>
			window.SILVERMINE_VIDEOJS_CHROMECAST_CONFIG = {
				preloadWebComponents: true,
			};
		</script>
		<script src="https://unpkg.com/megajs@1.1.4/dist/main.browser-umd.js"></script>
		<script src="https://vjs.zencdn.net/8.5.2/video.min.js"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videostream.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-lang_ca.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-chromecast.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-landscape-fullscreen.min.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/videojs-hotkeys.min.js?v=<?php echo VERSION; ?>"></script>
		<script src="https://www.gstatic.com/cv/js/sender/v1/cast_sender.js?loadCastFramework=1"></script>
<?php
} else if (PAGE_STYLE_TYPE=='news') {
?>
		<script src="<?php echo STATIC_URL; ?>/js/jquery.magnific-popup-1.1.0.min.js?v=<?php echo VERSION; ?>"></script>
<?php
}
?>
		<script src="<?php echo STATIC_URL; ?>/js/double-slider.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/common.js?v=<?php echo VERSION; ?>"></script>
		<script src="<?php echo STATIC_URL; ?>/js/<?php echo SITE_INTERNAL_TYPE; ?>.js?v=<?php echo VERSION; ?>"></script>
	</head>
	<body class="style-type-<?php echo PAGE_STYLE_TYPE; ?><?php echo defined('PAGE_EXTRA_BODY_CLASS') ? ' '.PAGE_EXTRA_BODY_CLASS : ''; ?><?php echo !empty($user) ? ' user-logged-in' : ''; ?>">
		<div class="main-container<?php echo (PAGE_STYLE_TYPE=='login' || PAGE_STYLE_TYPE=='text' || PAGE_STYLE_TYPE=='contact') ? ' obscured-background' : ''; ?>">
<?php
if (PAGE_STYLE_TYPE=='login') {
?>
			<div class="overlay-page">
				<div class="login-page">
					<div class="login-explanation">
						<div class="login-header">Registra’t a Fansubs.cat</div>
						<div class="login-points">
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-bookmark"></div>
								<div class="login-point-text">Podràs desar el contingut<br>en una llista personal</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Rebràs recomanacions<br>personalitzades</div>
								<div class="login-point-icon fas fa-fw fa-star"></div>
							</div>
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-thumbs-up"></div>
								<div class="login-point-text">Podràs valorar la qualitat<br>de les traduccions i edicions</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Tindràs el progrés sincronitzat<br>a tots els dispositius</div>
								<div class="login-point-icon fas fa-fw fa-house-laptop"></div>
							</div>
						</div>
					</div>
					<div class="login-form<?php echo defined('PAGE_IS_RESET_PASSWORD') ? ' hidden' : ''; ?>">
						<div class="login-form-main">
							<div class="login-subheader">Inicia la sessió</div>
							<form id="login-form" onsubmit="return login();" autocomplete="off" novalidate>
								<label for="login_username">Nom d’usuari</label>
								<input id="login_username" type="text" oninput="removeValidation(this.id);">
								<div id="login_username_validation" class="validation-message"></div>
								<label for="login_password">Contrasenya</label>
								<input id="login_password" type="password" oninput="removeValidation(this.id);">
								<div id="login_password_validation" class="validation-message"></div>
								<div id="login_generic_validation" class="validation-message-generic"></div>
								<button id="login_submit" type="submit" class="login-button">Inicia la sessió</button>
								<a class="forgot-password" onclick="showForgotPassword();">He oblidat la contrasenya</a>
							</form>
						</div>
						<div class="login-footer">Encara no n’ets membre? <a onclick="showRegister();">Registra-t’hi</a></div>
					</div>
					<div class="reset-password-form<?php echo !defined('PAGE_IS_RESET_PASSWORD') ? ' hidden' : ''?>">
						<div class="login-form-main">
							<div class="login-subheader">Restableix la contrasenya</div>
							<form id="reset-password-form" onsubmit="return resetPassword();" autocomplete="off" novalidate>
								<label for="reset_password">Contrasenya nova</label>
								<input id="reset_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_password_validation" class="validation-message"></div>
								<label for="reset_repeat_password">Repeteix la contrasenya</label>
								<input id="reset_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="reset_repeat_password_validation" class="validation-message"></div>
								<div id="reset_generic_validation" class="validation-message-generic"></div>
								<input id="reset_username" type="hidden" value="<?php echo !empty($_GET['usuari']) ? htmlspecialchars($_GET['usuari']) : ''; ?>">
								<input id="reset_code" type="hidden" value="<?php echo !empty($_GET['codi']) ? htmlspecialchars($_GET['codi']) : ''; ?>">
								<button id="reset_submit" type="submit" class="login-button">Restableix i inicia la sessió</button>
							</form>
						</div>
						<div class="login-footer">L’has recordada? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="forgot-password-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Contrasenya oblidada</div>
							<form id="forgot-password-form" onsubmit="return forgotPassword();" autocomplete="off" novalidate>
								<label for="forgot_email">Adreça electrònica</label>
								<input id="forgot_email" type="email" oninput="removeValidation(this.id);">
								<div id="forgot_email_validation" class="validation-message"></div>
								<div id="forgot_generic_validation" class="validation-message-generic"></div>
								<button id="forgot_submit" type="submit" class="login-button">Envia’m un correu per a restablir-la</button>
							</form>
						</div>
						<div class="login-footer">T’has equivocat? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="forgot-password-result-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Comprova el correu</div>
							<div class="forgot-password-result-text">Si tenies un compte amb aquesta adreça electrònica, has d’haver rebut un correu electrònic amb informació sobre com restablir la contrasenya. Segueix-ne les instruccions.<br><br>Si no l’has rebut, comprova la carpeta del correu brossa i revisa que hagis introduït correctament l’adreça electrònica del compte.</div>
						</div>
						<div class="login-footer">Ja has canviat la contrasenya? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="register-form hidden">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Registre</div>
							<form id="register-form" onsubmit="return register();" autocomplete="off" novalidate>
								<label for="register_username">Nom d’usuari</label>
								<input id="register_username" type="text" oninput="removeValidation(this.id);">
								<div id="register_username_validation" class="validation-message"></div>
								<label for="register_password">Contrasenya</label>
								<input id="register_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_password_validation" class="validation-message"></div>
								<label for="register_repeat_password">Repeteix la contrasenya</label>
								<input id="register_repeat_password" type="password" oninput="removeValidation(this.id);">
								<div id="register_repeat_password_validation" class="validation-message"></div>
								<label for="register_email">Adreça electrònica</label>
								<input id="register_email" type="email" oninput="removeValidation(this.id);">
								<div id="register_email_validation" class="validation-message"></div>
								<label for="register_birthday_day">Data de naixement</label>
								<div class="date-chooser">
									<input class="date-day" id="register_birthday_day" type="text" maxlength="2" oninput="removeValidationOnlyText('register_birthday');" placeholder="Dia">
									<select class="date-month" id="register_birthday_month" onchange="removeValidationOnlyText('register_birthday');">
										<option value="" disabled selected>Mes</option>
										<option value="01">gener</option>
										<option value="02">febrer</option>
										<option value="03">març</option>
										<option value="04">abril</option>
										<option value="05">maig</option>
										<option value="06">juny</option>
										<option value="07">juliol</option>
										<option value="08">agost</option>
										<option value="09">setembre</option>
										<option value="10">octubre</option>
										<option value="11">novembre</option>
										<option value="12">desembre</option>
									</select>
									<input class="date-year" id="register_birthday_year" type="text" maxlength="4" oninput="removeValidationOnlyText('register_birthday');" placeholder="Any">
								</div>
								<div id="register_birthday_validation" class="validation-message"></div>
								<div class="checkbox-layout">
									<input id="register_privacy_policy_accept" type="checkbox" onchange="removeValidationOnlyText('register_privacy_policy_accept');">
									<label for="register_privacy_policy_accept">Accepto la <a href="<?php echo MAIN_URL; ?>/politica-de-privadesa" target="_blank">política de privadesa</a></label>
								</div>
								<div id="register_privacy_policy_accept_validation" class="validation-message"></div>
								<div id="register_generic_validation" class="validation-message-generic"></div>
								<button id="register_submit" type="submit" class="login-button">Registra-m’hi i inicia la sessió</button>
							</form>
						</div>
						<div class="login-footer">Ja t’hi has registrat? <a onclick="showLogin();">Inicia la sessió</a></div>
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
		if (SITE_IS_HENTAI && !is_robot() && empty($_COOKIE['hentai_warning_accepted'])) {
?>
			<div data-nosnippet id="warning-overlay" class="flex">
				<div id="warning-overlay-content">
					<h2 id="warning-title">Avís important: contingut per a adults</h2>
					<div id="warning-message">Aquest web permet accedir a contingut que sols és apte per a majors de 18 anys i que pot incloure representacions de comportaments o d’actituds intolerables a la vida real. Confirma que compleixes els requisits per a accedir-hi.</div>
					<div id="warning-post-explanation">Per a evitar que hi hagi menors que puguin accedir a aquest web amb el teu dispositiu, pots instal·lar-hi un programa de control parental que filtri els webs etiquetats per a adults. Tot aquest apartat del web està etiquetat adequadament i un filtre correctament configurat hi ha d’impedir l’accés.</div>
					<div id="warning-buttonbar">
						<button id="warning-ok-button" class="normal-button" onclick="acceptHentaiWarning();">Sóc major d’edat i hi vull entrar</button>
						<button id="warning-close-button" class="normal-button" onclick="window.location.href='<?php echo MAIN_URL; ?>';">No hi vull entrar, torna a Fansubs.cat</button>
					</div>
				</div>
			</div>
<?php
		}
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
					<a class="social-link mastodon-link fab fa-fw fa-mastodon" href="https://mastodont.cat/@fansubscat" target="_blank" title="Mastodon de Fansubs.cat"></a>
					<a class="social-link telegram-link fab fa-fw fa-telegram" href="https://t.me/fansubscat" target="_blank" title="Telegram de Fansubs.cat"></a>
					<a class="social-link twitter-link fab fa-fw fa-x-twitter" href="https://x.com/fansubscat" target="_blank" title="X de Fansubs.cat"></a>
<?php
		} else {
?>
					<a class="logo-small" href="<?php echo SITE_IS_HENTAI ? SITE_BASE_URL : MAIN_URL; ?>" title="Torna a la pàgina d’inici<?php echo !SITE_IS_HENTAI ? " de Fansubs.cat" : " del portal de hentai"; ?>">
						<?php include(STATIC_DIRECTORY.'/images/site/logo.svg'); ?>
<?php
if (!empty($special_day) && file_exists(STATIC_DIRECTORY.'/images/site/logo_layer_'.$special_day.'.png')) {
?>
						<img class="logo-layer-small" src="<?php echo STATIC_URL; ?>/images/site/logo_layer_<?php echo $special_day; ?>.png">
<?php
			}
			if (PAGE_STYLE_TYPE=='catalogue' && SITE_IS_HENTAI) {
?>
						<div class="catalogues-explicit-category">
							<i class="fsc fa-fw fsc-hentai fa-2x"></i>
						</div>
<?php
			}
?>
					</a>
<?php
			if (PAGE_STYLE_TYPE=='catalogue' || PAGE_STYLE_TYPE=='news' || PAGE_STYLE_TYPE=='fansubs' || PAGE_STYLE_TYPE=='settings') {
?>
					<div class="catalogues-navigation">
						<a href="<?php echo (SITE_IS_HENTAI ? HENTAI_ANIME_URL : ANIME_URL); ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='anime' ? ' class="catalogue-selected"' : ''; ?>>Anime</a>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo (SITE_IS_HENTAI ? HENTAI_MANGA_URL : MANGA_URL); ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='manga' ? ' class="catalogue-selected"' : ''; ?>>Manga</a>
<?php
					if (!SITE_IS_HENTAI) {
?>
						<span class="catalogues-separator">|</span>
						<a href="<?php echo LIVEACTION_URL; ?>"<?php echo defined('CATALOGUE_ITEM_TYPE') && CATALOGUE_ITEM_TYPE=='liveaction' ? ' class="catalogue-selected"' : ''; ?>>Imatge real</a>
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
		if (PAGE_STYLE_TYPE=='catalogue' && !defined('PAGE_IS_SEARCH') && !defined('PAGE_IS_SERIES') && CATALOGUE_ITEM_TYPE!='liveaction' && !SITE_IS_HENTAI && (is_robot() || (!empty($user) && is_adult() && empty($user['hide_hentai_access'])))) {
?>
						<a class="hentai-button" href="<?php echo CATALOGUE_ITEM_TYPE=='anime' ? HENTAI_ANIME_URL : HENTAI_MANGA_URL; ?>" title="Vés a l’apartat de hentai">
							<i class="fsc fa-fw fsc-hentai fa-2x"></i>
						</a>
<?php
		} else if (SITE_IS_HENTAI) {
?>
						<a class="hentai-button" href="<?php echo CATALOGUE_ITEM_TYPE=='anime' ? ANIME_URL : MANGA_URL; ?>" title="Vés al contingut general">
							<i class="fa-solid fa-fw fa-house-chimney fa-2x"></i>
						</a>
<?php
		}
		if (PAGE_STYLE_TYPE=='catalogue' && !defined('PAGE_IS_SEARCH')) {
?>
						<a class="filter-button" href="<?php echo SITE_BASE_URL; ?>/cerca" title="Filtra i mostra tot el catàleg">
							<i class="fsc fa-fw fsc-catalogue fa-2x"></i>
						</a>
						<a class="filter-button mobile-search-button" href="<?php echo SITE_BASE_URL; ?>/cerca?focus=1" title="Cerca">
							<i class="fa fa-fw fa-search fa-2x"></i>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="" placeholder="Cerca..." autocomplete="off">
								<i id="search_button" class="fa fa-search" title="Cerca en tot el catàleg"></i>
								<div id="search_query_autocomplete" class="hidden"></div>
							</form>
						</div>
<?php
		} else if (PAGE_STYLE_TYPE=='news' && !defined('PAGE_IS_SEARCH')) {
?>
						<a class="filter-button" href="/cerca" title="Filtra i mostra totes les notícies">
							<i class="fsc fa-fw fsc-news fa-2x"></i>
						</a>
						<a class="filter-button mobile-search-button" href="<?php echo SITE_BASE_URL; ?>/cerca?focus=1" title="Cerca">
							<i class="fa fa-fw fa-search fa-2x"></i>
						</a>
						<div class="search-form">
							<form id="search_form">
								<input id="search_query" type="text" value="" placeholder="Cerca..." autocomplete="off">
								<i id="search_button" class="fa fa-search" title="Cerca a totes les notícies"></i>
							</form>
						</div>
<?php
		}
?>
					</div>
<?php
		if (empty($user)) {
?>
					<a class="user-login" href="<?php echo USERS_URL.'/inicia-la-sessio'; ?>"><span class="user-login-text">Inicia la sessió</span><span class="user-login-icon"><i class="fa fa-fw fa-sign-in"></i></span></a>
<?php
		}
?>
					<div class="user-options">
						<div class="dropdown-menu">
<?php
		if (!empty($user)) {
?>
							<img alt="Menú de l’usuari" onclick="showUserDropdown();" class="user-avatar dropdown-button" src="<?php echo !empty($user['avatar_filename']) ? STATIC_URL.'/images/avatars/'.$user['avatar_filename'] : STATIC_URL.'/images/site/default_avatar.jpg'; ?>">
<?php
		} else {
?>
							<div onclick="showUserDropdown();" class="anon-avatar dropdown-button"><i class="fa fa-gear"></i></div>
<?php
		}
?>
							<div id="user-dropdown" class="dropdown-content">
								<div class="dropdown-title"><?php echo !empty($user) ? $user['username'] : 'Opcions'; ?></div>
								<hr class="dropdown-separator">
<?php
		if (!empty($user)) {
?>
								<a href="<?php echo USERS_URL; ?>"><i class="fa fa-fw fa-user"></i> El meu perfil</a>
								<a href="<?php echo USERS_URL.'/la-meva-llista'; ?>"><i class="fa fa-fw fa-bookmark"></i> La meva llista</a>
								<hr class="dropdown-separator-secondary">
<?php
		}
?>
								<a href="<?php echo USERS_URL.'/configuracio'; ?>"><i class="fa fa-fw fa-gear"></i> Configuració</a>
<?php
		if (!defined('SITE_THEME_FORCED')) {
?>
								<a class="theme-button" onclick="toggleSiteTheme();"><i class="fa fa-fw fa-circle-half-stroke"></i> <span class="theme-button-text"><?php echo SITE_THEME=='light' ? 'Canvia al tema fosc' : 'Canvia al tema clar'; ?></span></a>
<?php
		}
?>
								<hr class="dropdown-separator-secondary">
<?php
		if (!empty($user)) {
?>
								<a href="<?php echo USERS_URL.'/tanca-la-sessio'; ?>"><i class="fa fa-fw fa-sign-out"></i> Tanca la sessió</a>
<?php
		} else {
?>
								<a href="<?php echo USERS_URL.'/inicia-la-sessio'; ?>"><i class="fa fa-fw fa-sign-in"></i> Inicia la sessió</a>
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
				<div data-nosnippet class="site-message"><?php echo $special_day=='fools' ? 'Estem millorant el disseny de la pàgina. De moment hi hem afegit Comic Sans, que li donarà un toc més modern. <a href="'.STATIC_URL.'/various/innocents.png" target="_blank" style="color: black;">Més informació</a>.' : GLOBAL_MESSAGE; ?></div>
<?php
		}
	}
?>
				<div class="main-section">
<?php
}
?>

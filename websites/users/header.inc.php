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
		<link rel="stylesheet" href="/style/users.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
		<script src="/js/users.js"></script>
	</head>
	<body>
		<div class="main-container<?php echo !empty($obscure_background) ? ' obscured-background' : ''; ?>">
<?php
if (!empty($show_login)) {
?>
			<div class="overlay-page">
				<div class="login-page">
					<div class="login-explanation">
						<div class="login-header">Registra’t a Fansubs.cat</div>
						<div class="login-points">
							<div class="login-point">
								<div class="login-point-icon fas fa-fw fa-bars-staggered"></div>
								<div class="login-point-text">Podràs desar el contingut<br>en una llista personal</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Rebràs recomanacions<br>personalitzades</div>
								<div class="login-point-icon far fa-fw fa-thumbs-up"></div>
							</div>
							<div class="login-point">
								<div class="login-point-icon far fa-fw fa-star-half-stroke"></div>
								<div class="login-point-text">Podràs valorar la qualitat<br>de les traduccions i edicions</div>
							</div>
							<div class="login-point">
								<div class="login-point-text">Tindràs el progrés sincronitzat<br>a tots els dispositius</div>
								<div class="login-point-icon fas fa-fw fa-house-laptop"></div>
							</div>
						</div>
					</div>
					<div class="login-form"<?php echo (!empty($show_login) && !empty($show_reset_password)) ? ' style="display: none;"' : ''?>>
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="history.back();"></div>
							<div class="login-subheader">Inicia la sessió</div>
							<form id="login-form" onsubmit="return login();" autocomplete="off" novalidate>
								<label for="login_username">Nom d'usuari</label>
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
						<div class="login-footer">Encara no n’ets usuari? <a onclick="showRegister();">Registra-t’hi</a></div>
					</div>
					<div class="reset-password-form"<?php echo empty($show_reset_password) ? ' style="display: none;"' : ''?>>
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
					<div class="forgot-password-form" style="display: none;">
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
					<div class="forgot-password-result-form" style="display: none;">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Comprova el correu</div>
							<div class="forgot-password-result-text">Si tenies un compte amb aquesta adreça electrònica, has d’haver rebut un correu electrònic amb informació sobre com restablir la contrasenya. Segueix-ne les instruccions.<br><br>Si no l’has rebut, comprova la carpeta del correu brossa i revisa que hagis introduït correctament l’adreça electrònica del compte.</div>
						</div>
						<div class="login-footer">Ja has canviat la contrasenya? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
					<div class="register-form" style="display: none;">
						<div class="login-form-main">
							<div class="login-close fa fa-xmark" onclick="showLogin();"></div>
							<div class="login-subheader">Registre</div>
							<form id="register-form" onsubmit="return register();" autocomplete="off" novalidate>
								<label for="register_username">Nom d'usuari</label>
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
									<label for="register_privacy_policy_accept">Accepto la <a href="<?php echo $mail_url; ?>/politica-de-privadesa/" target="_blank">política de privadesa</a></label>
								</div>
								<div id="register_privacy_policy_accept_validation" class="validation-message"></div>
								<div id="register_generic_validation" class="validation-message-generic"></div>
								<button id="register_submit" type="submit" class="login-button">Registra-m’hi</button>
							</form>
						</div>
						<div class="login-footer">Ja t’hi has registrat? <a onclick="showLogin();">Inicia la sessió</a></div>
					</div>
				</div>
			</div>
<?php
} else {
?>
			<div class="main-body">
				<div class="header">
					<a class="logo-small" href="<?php echo $main_url; ?>/"><?php include($static_directory.'/common/images/logo.svg'); ?></a>
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
<?php
}
?>

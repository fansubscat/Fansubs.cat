const MAIN_URL = "https://wwwv2.fansubs.cat";

function clearForms() {
	$('.invalid').removeClass('invalid');
	$('.validation-message').text('');
	$('.validation-message-generic').text('');
	$('#login-form').get(0).reset();
	$('#register-form').get(0).reset();
	$('#forgot-password-form').get(0).reset();
	$('#reset-password-form').get(0).reset();
}

function showLogin() {
	clearForms();
	$('.login-form').show();
	$('.register-form').hide();
	$('.forgot-password-form').hide();
	$('.forgot-password-result-form').hide();
	$('.reset-password-form').hide();
}

function showForgotPassword() {
	clearForms();
	$('.login-form').hide();
	$('.register-form').hide();
	$('.forgot-password-form').show();
	$('.forgot-password-result-form').hide();
	$('.reset-password-form').hide();
}

function showForgotPasswordResult() {
	clearForms();
	$('.login-form').hide();
	$('.register-form').hide();
	$('.forgot-password-form').hide();
	$('.forgot-password-result-form').show();
	$('.reset-password-form').hide();
}

function showRegister() {
	clearForms();
	$('.login-form').hide();
	$('.register-form').show();
	$('.forgot-password-form').hide();
	$('.forgot-password-result-form').hide();
	$('.reset-password-form').hide();
}

function login() {
	removeValidationOnlyText('login_generic');
	$('#login_submit').prop('disabled', true);
	$('#login_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#login_username').val().trim()=='') {
		addValidation('login_username','Has d’introduir el nom d’usuari.');
		failedValidation = true;
	}
	if ($('#login_password').val()=='') {
		addValidation('login_password','Has d’introduir la contrasenya.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#login_submit').prop('disabled', false);
		$('#login_submit').html('Inicia la sessió');
		return false;
	}

	var values = {
		username: $('#login_username').val().trim(),
		password: $('#login_password').val()
	};

	$.post({
		url: "/do_login.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		window.location.href=MAIN_URL;
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('login_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidationOnlyText('login_generic','Usuari o contrasenya incorrectes.');
			} else {
				addValidationOnlyText('login_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('login_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#login_submit').prop('disabled', false);
		$('#login_submit').html('Inicia la sessió');
	});
	return false;
}

function register() {
	removeValidationOnlyText('register_generic');
	$('#register_submit').prop('disabled', true);
	$('#register_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#register_username').val().trim()=='') {
		addValidation('register_username','Has d’introduir un nom d’usuari.');
		failedValidation = true;
	}
	if ($('#register_password').val()=='') {
		addValidation('register_password','Has d’introduir una contrasenya.');
		failedValidation = true;
	}
	if ($('#register_repeat_password').val()=='') {
		addValidation('register_repeat_password','Has de repetir la contrasenya.');
		failedValidation = true;
	}
	if ($('#register_email').val()=='') {
		addValidation('register_email','Has d’introduir una adreça electrònica.');
		failedValidation = true;
	}
	if ($('#register_birthday_day').val()=='') {
		addValidationOnlyText('register_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if ($('#register_birthday_month').val()==null) {
		addValidationOnlyText('register_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if ($('#register_birthday_year').val()=='') {
		addValidationOnlyText('register_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if (!$('#register_privacy_policy_accept').prop('checked')) {
		addValidationOnlyText('register_privacy_policy_accept','Cal que acceptis la política de privadesa.');
		failedValidation = true;
	}
	if ($('#register_password').val().length<7 && !failedValidation) {
		addValidation('register_password','La contrasenya ha de tenir un mínim de 7 caràcters.');
		failedValidation = true;
	}
	if ($('#register_password').val()!=$('#register_repeat_password').val() && !failedValidation) {
		addValidation('register_repeat_password','Les dues contrasenyes no coincideixen.');
		failedValidation = true;
	}
	if ($('#register_birthday_year').val()<1900 && !failedValidation) {
		addValidationOnlyText('register_birthday','La data de naixement és massa antiga.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#register_submit').prop('disabled', false);
		$('#register_submit').html('Registra-m’hi');
		return false;
	}

	var values = {
		username: $('#register_username').val().trim(),
		password: $('#register_password').val(),
		email_address: $('#register_email').val(),
		birthday_day: $('#register_birthday_day').val(),
		birthday_month: $('#register_birthday_month').val(),
		birthday_year: $('#register_birthday_year').val()
	};

	$.post({
		url: "/do_register.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		window.location.href=MAIN_URL;
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('register_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidation('register_username','Aquest usuari ja existeix.');
			} else if (response.code==3) {
				addValidation('register_email','Aquesta adreça electrònica ja existeix.');
			} else if (response.code==4) {
				addValidationOnlyText('register_birthday','La data de naixement no és vàlida.');
			} else if (response.code==5) {
				addValidationOnlyText('register_birthday','La data de naixement és massa antiga.');
			} else if (response.code==6) {
				addValidationOnlyText('register_birthday','Véns del futur? Doncs no acceptem viatgers en el temps. I menors de 13 anys, tampoc.');
			} else if (response.code==7) {
				addValidationOnlyText('register_birthday','No és permès el registre als menors de 13 anys.');
			} else if (response.code==8) {
				addValidation('register_email','L’adreça electrònica no té un format correcte.');
			} else {
				addValidationOnlyText('register_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('register_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#register_submit').prop('disabled', false);
		$('#register_submit').html('Registra-m’hi');
	});
	return false;
}

function forgotPassword() {
	removeValidationOnlyText('forgot_generic');
	$('#forgot_submit').prop('disabled', true);
	$('#forgot_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#forgot_email').val()=='') {
		addValidation('forgot_email','Has d’introduir la teva adreça electrònica.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#forgot_submit').prop('disabled', false);
		$('#forgot_submit').html('Envia’m un correu per a restablir-la');
		return false;
	}

	var values = {
		email_address: $('#forgot_email').val(),
	};

	$.post({
		url: "/do_forgot_password.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		showForgotPasswordResult();
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('login_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else {
				addValidationOnlyText('login_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('login_generic','S’ha produït un error. Torna-ho a provar.');
		}
	}).always(function() {
		$('#forgot_submit').prop('disabled', false);
		$('#forgot_submit').html('Envia’m un correu per a restablir-la');
	});
	return false;
}

function resetPassword() {
	removeValidationOnlyText('reset_generic');
	$('#reset_submit').prop('disabled', true);
	$('#reset_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#reset_password').val()=='') {
		addValidation('reset_password','Has d’introduir una contrasenya.');
		failedValidation = true;
	}
	if ($('#reset_repeat_password').val()=='') {
		addValidation('reset_repeat_password','Has de repetir la contrasenya.');
		failedValidation = true;
	}
	if ($('#reset_password').val().length<7 && !failedValidation) {
		addValidation('reset_password','La contrasenya ha de tenir un mínim de 7 caràcters.');
		failedValidation = true;
	}
	if ($('#reset_password').val()!=$('#reset_repeat_password').val() && !failedValidation) {
		addValidation('reset_repeat_password','Les dues contrasenyes no coincideixen.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#reset_submit').prop('disabled', false);
		$('#reset_submit').html('Restableix i inicia la sessió');
		return false;
	}

	var values = {
		username: $('#reset_username').val().trim(),
		password: $('#reset_password').val(),
		code: $('#reset_code').val()
	};

	$.post({
		url: "/do_reset_password.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		window.location.href=MAIN_URL;
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('reset_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidationOnlyText('reset_generic','Aquest usuari ja no existeix.');
			} else if (response.code==3) {
				addValidationOnlyText('reset_generic','El codi per a restablir la contrasenya no és vàlid, ja s’ha fet servir o ha caducat. Torna a demanar el restabliment de la contrasenya.');
			} else {
				addValidationOnlyText('reset_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('reset_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#reset_submit').prop('disabled', false);
		$('#reset_submit').html('Restableix i inicia la sessió');
	});
	return false;
}


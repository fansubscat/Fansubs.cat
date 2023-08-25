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
	$('.login-form').removeClass('hidden');
	$('.register-form').addClass('hidden');
	$('.forgot-password-form').addClass('hidden');
	$('.forgot-password-result-form').addClass('hidden');
	$('.reset-password-form').addClass('hidden');
}

function showForgotPassword() {
	clearForms();
	$('.login-form').addClass('hidden');
	$('.register-form').addClass('hidden');
	$('.forgot-password-form').removeClass('hidden');
	$('.forgot-password-result-form').addClass('hidden');
	$('.reset-password-form').addClass('hidden');
}

function showForgotPasswordResult() {
	clearForms();
	$('.login-form').addClass('hidden');
	$('.register-form').addClass('hidden');
	$('.forgot-password-form').addClass('hidden');
	$('.forgot-password-result-form').removeClass('hidden');
	$('.reset-password-form').addClass('hidden');
}

function showRegister() {
	clearForms();
	$('.login-form').addClass('hidden');
	$('.register-form').removeClass('hidden');
	$('.forgot-password-form').addClass('hidden');
	$('.forgot-password-result-form').addClass('hidden');
	$('.reset-password-form').addClass('hidden');
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
				addValidationOnlyText('register_birthday','Véns del futur? Doncs no acceptem viatgers en el temps.');
			} else if (response.code==7) {
				addValidationOnlyText('register_birthday','No és permès el registre als menors de 13 anys.');
			} else if (response.code==8) {
				addValidation('register_email','L’adreça electrònica no té un format correcte.');
			} else if (response.code==9) {
				addValidation('register_email','No acceptem registres amb adreces electròniques d’aquest domini perquè no ens és possible enviar-hi correus de restabliment de la contrasenya.');
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

function bookmarkRemoved(seriesId) {
	var elements = $('.thumbnail[data-series-id='+seriesId+']');
	for (element of elements) {
		$(element).parent().addClass('thumbnail-removed');
		$(element).parent().on('transitionend', function(e){
			var listParent = $(this).parent().parent().parent();
			var sectionParent = listParent.parent();
			if (listParent.find('.thumbnail-outer:not(.thumbnail-removed)').length==0) {
				listParent.remove();
			}
			if (sectionParent.find('.section:not(.empty-list)').length==0) {
				$('.section.empty-list').removeClass('hidden');
			}
		});
	}
}

function saveSettings() {
	if ($('body.user-logged-in').length>0) {
		var values = {
			'show_cancelled_projects': $('#show-cancelled').prop('checked') ? 1 : 0,
			'show_lost_projects': $('#show-lost').prop('checked') ? 1 : 0,
			'manga_reader_type': $('#reader-type').val()=='strip' ? 2 : ($('#reader-type').val()=='ltr' ? 1 : 0),
			'blacklisted_fansub_ids': '', //TODO
			'hide_hentai_access': $('#show-hentai').prop('checked') ? 0 : 1,
			'previous_chapters_read_behavior': $('#mark-previous-as-seen').prop('checked') ? 1 : 0
		};
		$.post({
			url: USERS_URL+"/do_save_settings.php",
			data: values,
			xhrFields: {
				withCredentials: true
			},
		});
	} else {
		//Set cookies
		Cookies.set('show_cancelled_projects', $('#show-cancelled').prop('checked') ? 1 : 0, cookieOptions, {secure: true});
		Cookies.set('show_lost_projects', $('#show-lost').prop('checked') ? 1 : 0, cookieOptions, {secure: true});
		Cookies.set('manga_reader_type', $('#reader-type').val()=='strip' ? 2 : ($('#reader-type').val()=='ltr' ? 1 : 0), cookieOptions, {secure: true});
		Cookies.set('blacklisted_fansub_ids', '', cookieOptions, {secure: true}); //TODO
	}
}

function deleteProfile() {
	removeValidationOnlyText('delete_profile_generic');
	$('#delete_profile_submit').prop('disabled', true);
	$('#delete_profile_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#delete_profile_password').val()=='') {
		addValidation('delete_profile_password','Has d’introduir la contrasenya.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#delete_profile_submit').prop('disabled', false);
		$('#delete_profile_submit').html('Elimina el meu perfil');
		return false;
	}

	var values = {
		password: $('#delete_profile_password').val()
	};

	$.post({
		url: "/do_delete_profile.php",
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
				addValidationOnlyText('delete_profile_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidation('delete_profile_password','Contrasenya incorrecta.');
			} else {
				addValidationOnlyText('delete_profile_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('delete_profile_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#delete_profile_submit').prop('disabled', false);
		$('#delete_profile_submit').html('Elimina el meu perfil');
	});
	return false;
}

function changePassword() {
	removeValidationOnlyText('change_password_generic');
	$('#change_password_submit').prop('disabled', true);
	$('#change_password_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#change_password_old_password').val()=='') {
		addValidation('change_password_old_password','Has d’introduir la contrasenya antiga.');
		failedValidation = true;
	}
	if ($('#change_password_password').val()=='') {
		addValidation('change_password_password','Has d’introduir una contrasenya nova.');
		failedValidation = true;
	}
	if ($('#change_password_repeat_password').val()=='') {
		addValidation('change_password_repeat_password','Has de repetir la contrasenya nova.');
		failedValidation = true;
	}
	if ($('#change_password_password').val().length<7 && !failedValidation) {
		addValidation('change_password_password','La contrasenya nova ha de tenir un mínim de 7 caràcters.');
		failedValidation = true;
	}
	if ($('#change_password_password').val()!=$('#change_password_repeat_password').val() && !failedValidation) {
		addValidation('change_password_repeat_password','Les dues contrasenyes noves no coincideixen.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#change_password_submit').prop('disabled', false);
		$('#change_password_submit').html('Canvia la contrasenya');
		return false;
	}

	var values = {
		old_password: $('#change_password_old_password').val(),
		password: $('#change_password_password').val(),
	};

	$.post({
		url: "/do_change_password.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('.profile-section-explanation').text('La contrasenya s’ha canviat correctament.');
		$('#change-password-form').addClass('hidden');
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('change_password_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidation('change_password_old_password','Contrasenya incorrecta.');
			} else {
				addValidationOnlyText('change_password_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('change_password_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#change_password_submit').prop('disabled', false);
		$('#change_password_submit').html('Canvia la contrasenya');
	});
	return false;
}

function editProfile() {
	removeValidationOnlyText('edit_profile_generic');
	$('#edit_profile_submit').prop('disabled', true);
	$('#edit_profile_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#edit_profile_email').val()=='') {
		addValidation('edit_profile_email','Has d’introduir una adreça electrònica.');
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_day').val()=='') {
		addValidationOnlyText('edit_profile_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_month').val()==null) {
		addValidationOnlyText('edit_profile_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_year').val()=='') {
		addValidationOnlyText('edit_profile_birthday','La data de naixement no és vàlida.');
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_year').val()<1900 && !failedValidation) {
		addValidationOnlyText('edit_profile_birthday','La data de naixement és massa antiga.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#edit_profile_submit').prop('disabled', false);
		$('#edit_profile_submit').html('Desa els canvis');
		return false;
	}

	var values = {
		email_address: $('#edit_profile_email').val(),
		birthday_day: $('#edit_profile_birthday_day').val(),
		birthday_month: $('#edit_profile_birthday_month').val(),
		birthday_year: $('#edit_profile_birthday_year').val(),
		avatar: $('.profile-avatar-image').attr('src'),
	};

	$.post({
		url: "/do_edit_profile.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		window.location.href=USERS_URL;
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('edit_profile_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==3) {
				addValidation('edit_profile_email','Aquesta adreça electrònica ja la fa servir un altre usuari.');
			} else if (response.code==4) {
				addValidationOnlyText('edit_profile_birthday','La data de naixement no és vàlida.');
			} else if (response.code==5) {
				addValidationOnlyText('edit_profile_birthday','La data de naixement és massa antiga.');
			} else if (response.code==6) {
				addValidationOnlyText('edit_profile_birthday','Véns del futur? Doncs no acceptem viatgers en el temps.');
			} else if (response.code==7) {
				addValidationOnlyText('edit_profile_birthday','No és permès el registre als menors de 13 anys. Elimina el teu compte.');
			} else if (response.code==8) {
				addValidation('edit_profile_email','L’adreça electrònica no té un format correcte.');
			} else if (response.code==9) {
				addValidation('edit_profile_email','No acceptem registres amb adreces electròniques d’aquest domini perquè no ens és possible enviar-hi correus de restabliment de la contrasenya.');
			} else {
				addValidationOnlyText('edit_profile_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('edit_profile_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#edit_profile_submit').prop('disabled', false);
		$('#edit_profile_submit').html('Desa els canvis');
	});
	return false;
}

function chooseAvatar() {
	$('#edit_profile_avatar_file').click();
}

function checkAvatarUpload() {
	var fileInput = $('#edit_profile_avatar_file')[0];
	if (fileInput.files && fileInput.files[0]) {
		var reader = new FileReader();
		reader.onload = function(e) {
			var img = new Image();

			img.onload = function() {
				//Adapted from https://livefiredev.com/html5-how-to-scale-image-to-fit-a-canvas-with-demos/
				var canvasSize = 256;
				// Initialize the canvas and it's size
				const canvas = document.createElement("canvas");
				canvas.width = canvasSize;
				canvas.height = canvasSize;

				let loadedImageWidth = img.width;
				let loadedImageHeight = img.height;
				// get the scale
				// it is the min of the 2 ratios
				let scaleFactor = Math.max(canvas.width / img.width, canvas.height / img.height);

				// Finding the new width and height based on the scale factor
				let newWidth = img.width * scaleFactor;
				let newHeight = img.height * scaleFactor;

				// get the top left position of the image
				// in order to center the image within the canvas
				let x = (canvas.width / 2) - (newWidth / 2);
				let y = (canvas.height / 2) - (newHeight / 2);

				// When drawing the image, we have to scale down the image
				// width and height in order to fit within the canvas
				canvas.getContext("2d").drawImage(img, x, y, newWidth, newHeight);
				$('.profile-avatar-image').attr('src',canvas.toDataURL());
			};

			img.src = e.target.result;
		};
		reader.readAsDataURL(fileInput.files[0]);
	}
}


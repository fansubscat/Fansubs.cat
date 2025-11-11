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
		addValidation('login_username',lang('js.users.login.username.error'));
		failedValidation = true;
	}
	if ($('#login_password').val()=='') {
		addValidation('login_password',lang('js.users.login.password.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#login_submit').prop('disabled', false);
		$('#login_submit').html(lang('js.users.login.login_button'));
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
		window.location.href=$('#redirect').val();
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('login_generic',lang('js.users.login.server_error.invalid'));
			} else if (response.code==2) {
				addValidationOnlyText('login_generic',lang('js.users.login.server_error.invalid_credentials'));
			} else if (response.code==3) {
				addValidationOnlyText('login_generic',lang('js.users.login.server_error.age_not_valid'));
			} else {
				addValidationOnlyText('login_generic',lang('js.users.login.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('login_generic',lang('js.users.login.server_error.generic'));
		}
		$('#login_submit').prop('disabled', false);
		$('#login_submit').html(lang('js.users.login.login_button'));
	});
	return false;
}

function register() {
	removeValidationOnlyText('register_generic');
	$('#register_submit').prop('disabled', true);
	$('#register_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#register_username').val().trim()=='') {
		addValidation('register_username',lang('js.users.register.username.error'));
		failedValidation = true;
	}
	if ($('#register_password').val()=='') {
		addValidation('register_password',lang('js.users.register.password.error'));
		failedValidation = true;
	}
	if ($('#register_repeat_password').val()=='') {
		addValidation('register_repeat_password',lang('js.users.register.repeat_password.error'));
		failedValidation = true;
	}
	if ($('#register_email').val()=='') {
		addValidation('register_email',lang('js.users.register.email.error'));
		failedValidation = true;
	}
	if ($('#register_birthday_day').val()=='') {
		addValidationOnlyText('register_birthday',lang('js.users.register.birthdate.error'));
		failedValidation = true;
	}
	if ($('#register_birthday_month').val()==null) {
		addValidationOnlyText('register_birthday',lang('js.users.register.birthdate.error'));
		failedValidation = true;
	}
	if ($('#register_birthday_year').val()=='') {
		addValidationOnlyText('register_birthday',lang('js.users.register.birthdate.error'));
		failedValidation = true;
	}
	if (!$('#register_privacy_policy_accept').prop('checked')) {
		addValidationOnlyText('register_privacy_policy_accept',lang('js.users.register.privacy_policy.error'));
		failedValidation = true;
	}
	if ($('#register_password').val().length<7 && !failedValidation) {
		addValidation('register_password',lang('js.users.register.password_short.error'));
		failedValidation = true;
	}
	if ($('#register_password').val()!=$('#register_repeat_password').val() && !failedValidation) {
		addValidation('register_repeat_password',lang('js.users.register.password_mismatch.error'));
		failedValidation = true;
	}
	if ($('#register_birthday_year').val()<1900 && !failedValidation) {
		addValidationOnlyText('register_birthday',lang('js.users.register.birthdate_too_old.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#register_submit').prop('disabled', false);
		$('#register_submit').html(lang('js.users.register.register_button'));
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
		window.location.href=$('#redirect').val();
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('register_generic',lang('js.users.register.server_error.invalid'));
			} else if (response.code==2) {
				addValidation('register_username',lang('js.users.register.server_error.user_exists'));
			} else if (response.code==3) {
				addValidation('register_email',lang('js.users.register.server_error.email_exists'));
			} else if (response.code==4) {
				addValidationOnlyText('register_birthday',lang('js.users.register.server_error.invalid_birthdate'));
			} else if (response.code==5) {
				addValidationOnlyText('register_birthday',lang('js.users.register.server_error.birthdate_too_old'));
			} else if (response.code==6) {
				addValidationOnlyText('register_birthday',lang('js.users.register.server_error.birthdate_future'));
			} else if (response.code==7) {
				addValidationOnlyText('register_birthday',lang('js.users.register.server_error.must_be_13'));
			} else if (response.code==8) {
				addValidation('register_email',lang('js.users.register.server_error.malformed_email'));
			} else if (response.code==9) {
				addValidation('register_email',lang('js.users.register.server_error.email_domain_banned'));
			} else if (response.code==10) {
				addValidationOnlyText('register_birthday',lang('js.users.register.server_error.must_be_18'));
			} else if (response.code==11) {
				addValidationOnlyText('register_username',lang('js.users.register.server_error.username_is_email'));
			} else if (response.code==12) {
				addValidationOnlyText('register_username',lang('js.users.register.server_error.username_has_emoji'));
			} else {
				addValidationOnlyText('register_generic',lang('js.users.register.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('register_generic',lang('js.users.register.server_error.generic'));
		}
		$('#register_submit').prop('disabled', false);
		$('#register_submit').html(lang('js.users.register.register_button'));
	});
	return false;
}

function forgotPassword() {
	removeValidationOnlyText('forgot_generic');
	$('#forgot_submit').prop('disabled', true);
	$('#forgot_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#forgot_email').val()=='') {
		addValidation('forgot_email',lang('js.users.forgot_password.email.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#forgot_submit').prop('disabled', false);
		$('#forgot_submit').html(lang('js.users.forgot_password.forgot_button'));
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
				addValidationOnlyText('login_generic',lang('js.users.forgot_password.server_error.invalid'));
			} else {
				addValidationOnlyText('login_generic',lang('js.users.forgot_password.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('login_generic',lang('js.users.forgot_password.server_error.generic'));
		}
	}).always(function() {
		$('#forgot_submit').prop('disabled', false);
		$('#forgot_submit').html(lang('js.users.forgot_password.forgot_button'));
	});
	return false;
}

function resetPassword() {
	removeValidationOnlyText('reset_generic');
	$('#reset_submit').prop('disabled', true);
	$('#reset_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#reset_password').val()=='') {
		addValidation('reset_password',lang('js.users.reset_password.password.error'));
		failedValidation = true;
	}
	if ($('#reset_repeat_password').val()=='') {
		addValidation('reset_repeat_password',lang('js.users.reset_password.repeat_password.error'));
		failedValidation = true;
	}
	if ($('#reset_password').val().length<7 && !failedValidation) {
		addValidation('reset_password',lang('js.users.reset_password.password_short.error'));
		failedValidation = true;
	}
	if ($('#reset_password').val()!=$('#reset_repeat_password').val() && !failedValidation) {
		addValidation('reset_repeat_password',lang('js.users.reset_password.password_mismatch.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#reset_submit').prop('disabled', false);
		$('#reset_submit').html(lang('js.users.reset_password.reset_button'));
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
				addValidationOnlyText('reset_generic',lang('js.users.reset_password.server_error.invalid'));
			} else if (response.code==2) {
				addValidationOnlyText('reset_generic',lang('js.users.reset_password.server_error.user_no_longer_exists'));
			} else if (response.code==3) {
				addValidationOnlyText('reset_generic',lang('js.users.reset_password.server_error.code_invalid'));
			} else {
				addValidationOnlyText('reset_generic',lang('js.users.reset_password.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('reset_generic',lang('js.users.reset_password.server_error.generic'));
		}
		$('#reset_submit').prop('disabled', false);
		$('#reset_submit').html(lang('js.users.reset_password.reset_button'));
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

function applyBlacklist() {
	var elements = $('.blacklisted-fansubs-dialog-checkbox:checked');
	var output = '';
	for (var i=0; i<elements.length;i++){
		if (output!='') {
			output+=',';
		}
		output+=$(elements[i]).val();
	}
	$('#blacklisted-fansubs-ids').val(output);
	if (elements.length==1) {
		$('.blacklisted-fansubs-list-number').text(lang('js.users.settings.blacklist.blocked_fansubs_one'));
	} else {
		$('.blacklisted-fansubs-list-number').text(lang('js.users.settings.blacklist.blocked_fansubs_many').replaceAll('%d', elements.length));
	}
	saveSettings();
}

function saveSettings() {
	if ($('body.user-logged-in').length>0) {
		var values = {
			'show_cancelled_projects': $('#show-cancelled').prop('checked') ? 1 : 0,
			'show_lost_projects': $('#show-lost').prop('checked') ? 1 : 0,
			'manga_reader_type': $('#reader-type').val(),
			'blacklisted_fansub_ids': $('#blacklisted-fansubs-ids').val(),
			'hide_hentai_access': $('#show-hentai').prop('checked') ? 0 : 1,
			'episode_sort_order': $('#episode-sort-order').prop('checked') ? 1 : 0,
			'previous_chapters_read_behavior': $('#mark-previous-as-seen').val()
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
		Cookies.set('show_cancelled_projects', $('#show-cancelled').prop('checked') ? 1 : 0, cookieOptions);
		Cookies.set('show_lost_projects', $('#show-lost').prop('checked') ? 1 : 0, cookieOptions);
		Cookies.set('episode_sort_order', $('#episode-sort-order').prop('checked') ? 1 : 0, cookieOptions);
		Cookies.set('manga_reader_type', $('#reader-type').val(), cookieOptions);
		Cookies.set('blacklisted_fansub_ids', $('#blacklisted-fansubs-ids').val(), cookieOptions);
	}
}

function deleteProfile() {
	removeValidationOnlyText('delete_profile_generic');
	$('#delete_profile_submit').prop('disabled', true);
	$('#delete_profile_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#delete_profile_password').val()=='') {
		addValidation('delete_profile_password',lang('js.users.delete_profile.password.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#delete_profile_submit').prop('disabled', false);
		$('#delete_profile_submit').html(lang('js.users.delete_profile.delete_button'));
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
				addValidationOnlyText('delete_profile_generic',lang('js.users.delete_profile.server_error.invalid'));
			} else if (response.code==2) {
				addValidation('delete_profile_password',lang('js.users.delete_profile.server_error.invalid_password'));
			} else {
				addValidationOnlyText('delete_profile_generic',lang('js.users.delete_profile.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('delete_profile_generic',lang('js.users.delete_profile.server_error.generic'));
		}
		$('#delete_profile_submit').prop('disabled', false);
		$('#delete_profile_submit').html(lang('js.users.delete_profile.delete_button'));
	});
	return false;
}

function changePassword() {
	removeValidationOnlyText('change_password_generic');
	$('#change_password_submit').prop('disabled', true);
	$('#change_password_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#change_password_old_password').val()=='') {
		addValidation('change_password_old_password',lang('js.users.change_password.old_password.error'));
		failedValidation = true;
	}
	if ($('#change_password_password').val()=='') {
		addValidation('change_password_password',lang('js.users.change_password.password.error'));
		failedValidation = true;
	}
	if ($('#change_password_repeat_password').val()=='') {
		addValidation('change_password_repeat_password',lang('js.users.change_password.repeat_password.error'));
		failedValidation = true;
	}
	if ($('#change_password_password').val().length<7 && !failedValidation) {
		addValidation('change_password_password',lang('js.users.change_password.password_short.error'));
		failedValidation = true;
	}
	if ($('#change_password_password').val()!=$('#change_password_repeat_password').val() && !failedValidation) {
		addValidation('change_password_repeat_password',lang('js.users.change_password.password_mismatch.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#change_password_submit').prop('disabled', false);
		$('#change_password_submit').html(lang('js.users.change_password.change_button'));
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
		$('.profile-section-explanation').text(lang('js.users.change_password.success'));
		$('#change-password-form').addClass('hidden');
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('change_password_generic',lang('js.users.change_password.server_error.invalid'));
			} else if (response.code==2) {
				addValidation('change_password_old_password',lang('js.users.change_password.server_error.invalid_password'));
			} else {
				addValidationOnlyText('change_password_generic',lang('js.users.change_password.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('change_password_generic',lang('js.users.change_password.server_error.generic'));
		}
		$('#change_password_submit').prop('disabled', false);
		$('#change_password_submit').html(lang('js.users.change_password.change_button'));
	});
	return false;
}

function editProfile() {
	removeValidationOnlyText('edit_profile_generic');
	$('#edit_profile_submit').prop('disabled', true);
	$('#edit_profile_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#edit_profile_username').val()=='') {
		addValidation('edit_profile_username',lang('js.users.edit_profile.username.error'));
		failedValidation = true;
	}
	if ($('#edit_profile_email').val()=='') {
		addValidation('edit_profile_email',lang('js.users.edit_profile.email.error'));
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_day').val()=='') {
		addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.birthdate.error'));
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_month').val()==null) {
		addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.birthdate.error'));
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_year').val()=='') {
		addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.birthdate.error'));
		failedValidation = true;
	}
	if ($('#edit_profile_birthday_year').val()<1900 && !failedValidation) {
		addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.birthdate_too_old.error'));
		failedValidation = true;
	}

	if (failedValidation) {
		$('#edit_profile_submit').prop('disabled', false);
		$('#edit_profile_submit').html(lang('js.users.edit_profile.save_button'));
		return false;
	}

	var values = {
		username: $('#edit_profile_username').val(),
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
				addValidationOnlyText('edit_profile_generic',lang('js.users.edit_profile.server_error.invalid'));
			} else if (response.code==3) {
				addValidation('edit_profile_email',lang('js.users.edit_profile.server_error.email_exists'));
			} else if (response.code==4) {
				addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.server_error.invalid_birthdate'));
			} else if (response.code==5) {
				addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.server_error.birthdate_too_old'));
			} else if (response.code==6) {
				addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.server_error.birthdate_future'));
			} else if (response.code==7) {
				addValidationOnlyText('edit_profile_birthday',lang('js.users.edit_profile.server_error.must_be_13'));
			} else if (response.code==8) {
				addValidation('edit_profile_email',lang('js.users.edit_profile.server_error.malformed_email'));
			} else if (response.code==9) {
				addValidation('edit_profile_email',lang('js.users.edit_profile.server_error.email_domain_banned'));
			} else if (response.code==10) {
				addValidation('edit_profile_username',lang('js.users.edit_profile.server_error.user_exists'));
			} else if (response.code==11) {
				addValidationOnlyText('register_username',lang('js.users.edit_profile.server_error.username_is_email'));
			} else if (response.code==12) {
				addValidationOnlyText('register_username',lang('js.users.edit_profile.server_error.username_has_emoji'));
			} else {
				addValidationOnlyText('edit_profile_generic',lang('js.users.edit_profile.server_error.generic'));
			}
		} catch(e) {
			addValidationOnlyText('edit_profile_generic',lang('js.users.edit_profile.server_error.generic'));
		}
		$('#edit_profile_submit').prop('disabled', false);
		$('#edit_profile_submit').html(lang('js.users.edit_profile.save_button'));
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

$(document).ready(function() {
	$('.edit-blacklisted-fansubs').on("click", function() {
		var code = '';
		var fansubs = JSON.parse($('#all-fansubs-json').val());
		var blacklistedFansubs = JSON.parse('['+$('#blacklisted-fansubs-ids').val()+']');
		code+='<div class="blacklisted-fansubs-dialog-container">';
		for(var i=0;i<fansubs.length;i++) {
			code+='<div class="blacklisted-fansubs-dialog-element"><input id="blacklisted-fansub-'+fansubs[i].id+'" class="blacklisted-fansubs-dialog-checkbox" type="checkbox"'+(blacklistedFansubs.includes(fansubs[i].id) ? ' checked' : '')+' value="'+fansubs[i].id+'" onchange="applyBlacklist();"><label class="blacklisted-fansubs-dialog-fansub-name" for="blacklisted-fansub-'+fansubs[i].id+'">'+fansubs[i].name+'</label></div>';
		}
		code+='</div>';
		
		showCustomDialog(lang('js.users.settings.blacklist.edit.title'), code, null, true, true, [
			{
				text: lang('js.users.settings.blacklist.edit.go_back'),
				class: 'normal-button',
				onclick: function(){
					closeCustomDialog();
				}
			}
		], true);
	});
});

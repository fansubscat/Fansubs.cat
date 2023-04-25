var kcp = 0;

function resetKcp() {
	kcp = 0;
	$('#logo_fansubscat').removeClass('keep-hover');
}

function runEe() {
	$('body').animate({  borderSpacing: -360 }, {
		step: function(now,fx) {
			$(this).css('-webkit-transform','rotate('+now+'deg)'); 
			$(this).css('-moz-transform','rotate('+now+'deg)');
			$(this).css('transform','rotate('+now+'deg)');
		},
		duration:'slow'
	},'linear');
}

function sendMail() {
	removeValidationOnlyText('contact_generic');
	$('#contact_submit').prop('disabled', true);
	$('#contact_submit').html('<i class="fas fa-spinner fa-spin"></i>');
	var failedValidation = false;
	if ($('#contact_name').val()=='') {
		addValidation('contact_name','Has d’introduir un nom.');
		failedValidation = true;
	}
	if ($('#contact_email').val()=='') {
		addValidation('contact_email','Has d’introduir una adreça electrònica.');
		failedValidation = true;
	}
	if ($('#contact_message').val()=='') {
		addValidation('contact_message','Has d’introduir el missatge que ens vulguis fer arribar.');
		failedValidation = true;
	}
	if ($('#contact_question').val()=='') {
		addValidation('contact_question','Has d’introduir la qüestió de seguretat.');
		failedValidation = true;
	}

	if (failedValidation) {
		$('#contact_submit').prop('disabled', false);
		$('#contact_submit').html('Envia el missatge');
		return false;
	}

	var values = {
		name: $('#contact_name').val().trim(),
		email: $('#contact_email').val(),
		message: $('#contact_message').val(),
		question: $('#contact_question').val()
	};

	$.post({
		url: "/do_send_contact_email.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('#contact-form').hide();
		$('#contact-sent').show();
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==1) {
				addValidationOnlyText('contact_generic','Alguna dada no és vàlida. Revisa-les i torna-ho a provar.');
			} else if (response.code==2) {
				addValidation('contact_question','La resposta no és correcta.');
			} else if (response.code==3) {
				addValidation('contact_email','L’adreça electrònica no té un format correcte.');
			} else {
				addValidationOnlyText('contact_generic','S’ha produït un error. Torna-ho a provar.');
			}
		} catch(e) {
			addValidationOnlyText('contact_generic','S’ha produït un error. Torna-ho a provar.');
		}
		$('#contact_submit').prop('disabled', false);
		$('#contact_submit').html('Envia el missatge');
	});
	return false;
}

$(document).ready(function() {
	$('#logo_fansubscat').click(function (e){
		resetKcp();
		console.log(kcp);
	});
	$('#logo_fansubscat #line_1').click(function (e){
		if (kcp==0 || kcp==1) {
			kcp++;
		} else {
			resetKcp();
			kcp=1;
		}
		console.log(kcp);
		e.stopPropagation();
	});
	$('#logo_fansubscat #line_2').click(function (e){
		if (kcp==2 || kcp==3) {
			kcp++;
			if (kcp==4) {
				$('#logo_fansubscat').addClass('keep-hover');
			}
		} else {
			resetKcp();
		}
		console.log(kcp);
		e.stopPropagation();
	});
	$('#logo_fansubscat #line_3').click(function (e){
		if (kcp==4 || kcp==6) {
			kcp++;
		} else {
			resetKcp();
		}
		console.log(kcp);
		e.stopPropagation();
	});
	$('#logo_fansubscat #line_4').click(function (e){
		if (kcp==5 || kcp==7) {
			kcp++;
		} else {
			resetKcp();
		}
		console.log(kcp);
		e.stopPropagation();
	});
	$('#logo_fansubscat #path853').click(function (e){
		if (kcp==8) {
			kcp++;
		} else {
			resetKcp();
		}
		console.log(kcp);
		e.stopPropagation();
	});
	$('#logo_fansubscat #path845').click(function (e){
		if (kcp==9) {
			runEe();
		}
		resetKcp();
		console.log(kcp);
		e.stopPropagation();
	});
});


const MAIN_URL = "https://wwwv2.fansubs.cat";
const USERS_URL='https://usuarisv2.fansubs.cat';

var cookieOptions = {
	expires: 3650,
	path: '/',
	domain: ".fansubs.cat"
};

function addValidation(elementId, text){
	$('#'+elementId).addClass('invalid');
	$('#'+elementId+'_validation').text(text);
}

function addValidationOnlyText(elementId, text){
	$('#'+elementId+'_validation').text(text);
}

function removeValidation(elementId){
	$('#'+elementId).removeClass('invalid');
	$('#'+elementId+'_validation').text('');
}

function removeValidationOnlyText(elementId){
	$('#'+elementId+'_validation').text('');
}

function showUserDropdown(){
	$('#user-dropdown').toggleClass('dropdown-show');
}

function toggleSiteTheme() {
	var newTheme; 
	if ($('html').hasClass('theme-dark')) {
		$('html').removeClass('theme-dark');
		$('html').addClass('theme-light');
		$('.theme-button-text').text('Canvia al tema fosc');
		newTheme='light';
	} else {
		$('html').removeClass('theme-light');
		$('html').addClass('theme-dark');
		$('.theme-button-text').text('Canvia al tema clar');
		newTheme='dark';
	}

	values = {
		'site_theme': newTheme
	};

	Cookies.set('site_theme', newTheme, cookieOptions);
	$.post({
		url: USERS_URL+"/do_save_site_theme.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropdown-button')) {
		$('.dropdown-content').removeClass('dropdown-show');
	}
}

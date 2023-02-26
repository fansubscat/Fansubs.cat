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
	if ($('html').hasClass('theme-dark')) {
		$('html').removeClass('theme-dark');
		$('html').addClass('theme-light');
		Cookies.set('site_theme', 'light', cookieOptions);
		$('.theme-button-text').text('Canvia al tema fosc');
	} else {
		$('html').removeClass('theme-light');
		$('html').addClass('theme-dark');
		Cookies.set('site_theme', 'dark', cookieOptions);
		$('.theme-button-text').text('Canvia al tema clar');
	}
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropdown-button')) {
		$('.dropdown-content').removeClass('dropdown-show');
	}
}

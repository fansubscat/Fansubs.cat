const MAIN_URL = "https://wwwv2.fansubs.cat";
const USERS_URL='https://usuarisv2.fansubs.cat';

var cookieOptions = {
	expires: 3650,
	path: '/',
	domain: ".fansubs.cat"
};

function getBaseUrl() {
	return $('meta[name="base_url"]').attr('content');
}

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
	$('html').addClass('notransition');
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
	$('html')[0].offsetHeight; //Triggers reflow
	$('html').removeClass('notransition');

	values = {
		'site_theme': newTheme
	};

	Cookies.set('site_theme', newTheme, cookieOptions, {secure: true});
	$.post({
		url: USERS_URL+"/do_save_site_theme.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function toggleBookmark(seriesId){
	var action;
	if ($('.floating-info-bookmark[data-series-id='+seriesId+']').hasClass('fas'))	{
		$('.floating-info-bookmark[data-series-id='+seriesId+']').removeClass('fas').addClass('far');
		action='remove';
		bookmarkRemoved(seriesId);
	} else {
		$('.floating-info-bookmark[data-series-id='+seriesId+']').removeClass('far').addClass('fas');
		action='add';
	}

	var values = {
		series_id: seriesId,
		action: action
	};

	$.post({
		url: USERS_URL+"/do_save_to_my_list.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function getOffset(element){
	var offsetLeft = 0;
	var offsetTop  = 0;

	while (element) {
		offsetLeft += element.offsetLeft;
		offsetTop  += element.offsetTop;
		element = element.offsetParent;
	}

	return [offsetLeft, offsetTop];
}

function prepareFloatingInfo(element){
	var offset = getOffset(element);
	var regex = /translate3d\((.*)px, 0px, 0px\)/g;
	var inCarouselPage = $('.has-carousel').length>0;
	var translation = (inCarouselPage ? parseInt(regex.exec(element.parentNode.parentNode.parentNode.style.transform)[1]) : 0);
	var maxWidth = $(window).width(); //If search layout is on the right: $('.search-layout').length>0 ? ($(window).width() - $('.search-layout').width()) : $(window).width();
	$(element).removeClass('floating-info-right').removeClass('floating-info-left');
	if ((offset[0]+translation+element.clientWidth*1.25*2)<maxWidth){
		//We can fit it: right-side
		$(element).addClass('floating-info-right');
	} else {
		//We can't: left-side
		$(element).addClass('floating-info-left');
	}
}

function prepareClickableFloatingInfo(element){
	$('.thumbnail-clicked').removeClass('thumbnail-clicked');
	$(element).parent().parent().addClass('thumbnail-clicked');
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropdown-button')) {
		$('.dropdown-content').removeClass('dropdown-show');
	}
}

const BASE_DOMAIN = document.querySelector("meta[name=current_domain]").content;
const MAIN_URL = document.querySelector("meta[name=main_url]").content;
const USERS_URL = document.querySelector("meta[name=users_url]").content;
const SITE_NAME = document.querySelector("meta[name=site_name]").content;

var lastWindowWidth=0;

var cookieOptions = {
	expires: 3650,
	path: '/',
	domain: "."+BASE_DOMAIN,
	sameSite: "None",
	secure: true
};

function lang(string) {
	if (window.LANGUAGE_STRINGS[string]===undefined) {
		alert('Missing string: '+string);
		return string;
	}
	return window.LANGUAGE_STRINGS[string];
}

function showCustomDialog(title, text, subtext, closeable=true, blurred=true, buttonsArray, scrollable=false, keepNonScrollable=false) {
	$('html').addClass('page-no-overflow');
	$('#dialog-overlay').remove();
	var code = '<div data-nosnippet id="dialog-overlay" class="flex'+(blurred ? ' dialog-overlay-blurred' : '')+(keepNonScrollable ? ' dialog-overlay-keep-non-scroll' : '')+'"><div id="dialog-overlay-content"'+(scrollable ? ' class="scrollable-dialog"' : '')+'>';
	if (closeable) {
		code += '<a class="dialog-close-button fa fa-fw fa-times" title="'+lang('js.dialog.close')+'" onclick="closeCustomDialog();"></a>'
	}
	code += '<h2 id="dialog-title">'+title+'</h2>';
	if (text!=null) {
		code += '<div id="dialog-message">'+text+'</div>';
	}
	if (subtext!=null) {
		code += '<div id="dialog-post-explanation">'+subtext+'</div>';
	}
	code += '<div id="dialog-buttonbar"></div></div></div>';

	$(code).appendTo('.main-container');
		
	for (var i=0; i<buttonsArray.length;i++) {
		var button = $('<button class="dialog-button '+buttonsArray[i].class+'">'+buttonsArray[i].text+'</button>');
		button.click(buttonsArray[i].onclick);
		button.appendTo('#dialog-buttonbar');
	}
}

function showAlert(title, desc) {
	showCustomDialog(title, desc, null, true, true, [
		{
			text: lang('js.dialog.ok'),
			class: 'normal-button',
			onclick: function(){
				closeCustomDialog();
			}
		}
	]);
}

function closeCustomDialog() {
	if (!$('#dialog-overlay').hasClass('dialog-overlay-keep-non-scroll')) {
		$('html').removeClass('page-no-overflow');
	}
	$('#dialog-overlay').remove();
}

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
		$('.theme-button-text').text(lang('js.menu.change_theme.dark'));
		newTheme='light';
	} else {
		$('html').removeClass('theme-light');
		$('html').addClass('theme-dark');
		$('.theme-button-text').text(lang('js.menu.change_theme.light'));
		newTheme='dark';
	}
	$('html')[0].offsetHeight; //Triggers reflow
	$('html').removeClass('notransition');

	Cookies.set('site_theme', newTheme, cookieOptions);
	if ($('body.user-logged-in').length>0) {
		var values = {
			'site_theme': newTheme
		};
		$.post({
			url: USERS_URL+"/do_save_site_theme.php",
			data: values,
			xhrFields: {
				withCredentials: true
			},
		});
	}
}

function toggleBookmark(seriesId){
	if ($('body.user-logged-in').length==0) {
		showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.add_list'));
		return;
	}
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
	var translation = 0;
	var regexResult = regex.exec(element.parentNode.parentNode.parentNode.style.transform);
	if (inCarouselPage && regexResult!=null && regexResult.length>1) {
		translation = parseInt(regexResult[1]);
	}
	var maxWidth = $('.main-body')[0].offsetLeft+$('.main-body')[0].offsetWidth; //If search layout is on the right: $('.search-layout').length>0 ? ($(window).width() - $('.search-layout').width()) : $(window).width();
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
	$('.thumbnail-clicked .floating-info-main').click(function(e) { $('.thumbnail-clicked').removeClass('thumbnail-clicked'); });
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
	if (!event.target.matches('.dropdown-button') && !event.target.matches('.theme-button') && !event.target.matches('.fa-circle-half-stroke') && !event.target.matches('.theme-button-text')) {
		$('.dropdown-content').removeClass('dropdown-show');
	}
}

function menuOptionUnderlineSetup(element) {
	var target = document.querySelector(".catalogues-underline");
	var links = document.querySelectorAll(".catalogues-navigation a");
	var originalLink = document.querySelector(".catalogue-selected");
	if (originalLink!=null && !originalLink.classList.contains("catalogue-selected-processed")) {
		originalLink.classList.add("catalogue-selected-processed");
		target.classList.add('catalogues-underline-noanim');

		const width = originalLink.getBoundingClientRect().width;
		const height = originalLink.getBoundingClientRect().height;
		const left = originalLink.getBoundingClientRect().left + window.pageXOffset;
		const top = originalLink.getBoundingClientRect().top + window.pageYOffset+2;

		target.style.width = `${width}px`;
		target.style.height = `${height}px`;
		target.style.left = `${left}px`;
		target.style.top = `${top}px`;
		target.classList.remove('catalogues-underline-noanim');
	}
	if (!element.classList.contains("catalogues-underline-active")) {
		for (let i = 0; i < links.length; i++) {
			if (links[i].classList.contains("catalogues-underline-active")) {
				links[i].classList.remove("catalogues-underline-active");
			}
		}

		element.classList.add("catalogues-underline-active");

		const width = element.getBoundingClientRect().width;
		const height = element.getBoundingClientRect().height;
		const left = element.getBoundingClientRect().left + window.pageXOffset;
		const top = element.getBoundingClientRect().top + window.pageYOffset+2;

		target.style.opacity = `1`;
		target.style.width = `${width}px`;
		target.style.height = `${height}px`;
		target.style.left = `${left}px`;
		target.style.top = `${top}px`;
	}
}

function menuOptionMouseEnter(e) {
	menuOptionUnderlineSetup(e.currentTarget);
}

function menuOptionMouseLeave() {
	var target = document.querySelector(".catalogues-underline");
	var links = document.querySelectorAll(".catalogues-navigation a");
	for (let i = 0; i < links.length; i++) {
		if (links[i].classList.contains("catalogues-underline-active")) {
			links[i].classList.remove("catalogues-underline-active");
		}
	}

	var originalLink = document.querySelector(".catalogue-selected");
	if (originalLink!=null) {
		originalLink.classList.add("catalogues-underline-active");

		const width = originalLink.getBoundingClientRect().width;
		const height = originalLink.getBoundingClientRect().height;
		const left = originalLink.getBoundingClientRect().left + window.pageXOffset;
		const top = originalLink.getBoundingClientRect().top + window.pageYOffset+2;

		target.style.width = `${width}px`;
		target.style.height = `${height}px`;
		target.style.left = `${left}px`;
		target.style.top = `${top}px`;
		target.style.transform = "none";
	} else {
		target.style.opacity = `0`;
	}
}

function acceptHentaiWarning() {
	Cookies.set('hentai_warning_accepted', '1', cookieOptions);
	$('#warning-overlay').remove();
	$('html').removeClass('page-no-overflow');
}

$(document).ready(function() {
	var links = document.querySelectorAll(".catalogues-navigation a");
	var container = document.querySelector(".catalogues-navigation");

	if (container) {
		for (let i = 0; i < links.length; i++) {
			links[i].addEventListener("mouseenter", menuOptionMouseEnter);
		}
		container.addEventListener("mouseleave", menuOptionMouseLeave);
	}

	$(window).resize(function() {
		if ($(window).width()!=lastWindowWidth) {
			if (typeof resizeSynopsisHeight === "function") {
				resizeSynopsisHeight();
			}

			//Reposition underline
			var target = document.querySelector(".catalogues-underline");
			var active = document.querySelector("a.catalogues-underline-active");
			if (active) {
				const left = active.getBoundingClientRect().left + window.pageXOffset;
				const top = active.getBoundingClientRect().top + window.pageYOffset+2;
				target.style.left = `${left}px`;
				target.style.top = `${top}px`;
			}

			lastWindowWidth=$(window).width();
		}
	});

	lastWindowWidth=$(window).width();
});

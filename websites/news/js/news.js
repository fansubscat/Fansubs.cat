var lastSearchRequest = null;
var currentPage = 1;

function launchSearch(query) {
	window.location.href='/cerca'+(query!='' ? '/'+encodeURIComponent(query) : '');
}

function loadSearchResults(page) {
	if (page!=currentPage) {
		currentPage = page;
		window.scrollTo(0, 0);
	}
	var query = $('#news-search-query').val();
	if (lastSearchRequest==null && query=='' && !$('body').hasClass('has-search-results')) {
		$('.loading-message').text('S’estan carregant les notícies...');
	} else {
		history.replaceState(null, null, '/cerca'+(query!='' ? '/'+encodeURIComponent(query) : ''));
		$('.loading-message').text('S’estan carregant els resultats de la cerca...');
	}

	$('.style-type-news').removeClass('has-search-results');
	$('.error-layout').addClass('hidden');
	$('.results-layout').addClass('hidden');
	$('.loading-layout').removeClass('hidden');
	if (lastSearchRequest!=null) {
		lastSearchRequest.abort();
	}

	var beginDate = new Date('2003-05-01T00:00:00');
	var selectedStartDate = formatDateInternal(addMonths(beginDate, $('#date-from-slider').val()));
	var selectedEndDate = formatDateInternal(addMonths(beginDate, $('#date-to-slider').val()));

	var values = {
		'min_month': selectedStartDate,
		'max_month': selectedEndDate,
		'fansub_id': $('#news-search-fansub').val(),
		'hide_own_news': $('#news-search-include-own').is(':checked') ? 0 : 1
	};

	lastSearchRequest = $.post({
		url: "/results.php?search=1&page="+currentPage+"&query="+encodeURIComponent($('#news-search-query').val()),
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('.style-type-news').addClass('has-search-results');
		$('.results-layout').html(data);
		$('.loading-layout').addClass('hidden');
		$('.error-layout').addClass('hidden');
		$('.results-layout').removeClass('hidden');
		initializeImageZoom();
	}).fail(function(xhr, status, error) {
		if (error!='abort') {
			$('.style-type-news').removeClass('has-search-results');
			$('.loading-layout').addClass('hidden');
			$('.results-layout').addClass('hidden');
			$('.error-layout').removeClass('hidden');
		}
	});
}

function formatDateInternal(d) {
	var month = '' + (d.getMonth() + 1);
	var year = d.getFullYear();

	if (month.length < 2) {
		month = '0' + month;
	}

	return [year, month].join('-');
}

function formatDate(d) {
	var month = '' + (d.getMonth() + 1);
	var year = d.getFullYear();

	if (month.length < 2) {
		month = '0' + month;
	}

	return [month, year].join('/');
}

function addMonths(date, months) {
	var d = date.getDate();
	date.setMonth(date.getMonth() + +months);
	if (date.getDate() != d) {
		date.setDate(0);
	}
	return date;
}

function formatDoubleSliderInput(input, value) {
	var format = $(input).attr('value-formatting');
	if (format=='date') {
		var beginDate = new Date('2003-05-01T00:00:00');
		var selectedDate = addMonths(beginDate, value);
		input.innerText=formatDate(beginDate);
	} else {
		input.innerText=value;
	}
}

function toggleSearchLayout() {
	$('.thumbnail-clicked').removeClass('thumbnail-clicked');
	if ($('.search-layout-toggle-button-visible').length>0) {
		$('.search-layout-toggle-button').removeClass('search-layout-toggle-button-visible');
		$('.search-layout').removeClass('search-layout-visible');
	} else {
		$('.search-layout-toggle-button').addClass('search-layout-toggle-button-visible');
		$('.search-layout').addClass('search-layout-visible');
	}
}

function initializeImageZoom() {
	//We setup magnific popups for all fansub images
	$(".news-image").each(function(){
		$(this).magnificPopup({
			type: 'image',
			gallery: {
				enabled: true
			},
			callbacks: {
				elementParse: function(item) { item.src = item.el.attr('src'); }
			},
			zoom: {
				enabled: true,
				duration: 300
			}
		});
	});
	$(".news-image-mobile").each(function(){
		$(this).magnificPopup({
			type: 'image',
			gallery: {
				enabled: true
			},
			callbacks: {
				elementParse: function(item) { item.src = item.el.attr('src'); }
			},
			zoom: {
				enabled: true,
				duration: 300
			}
		});
	});
}

$(document).ready(function() {
	$('#search_form').submit(function(){
		launchSearch($('#search_query').val());
		return false;
	});
	$('#search_button').click(function(){
		$('#search_form').submit();
	});

	if ($('#news-search-query').length==1 && !$('body').hasClass('has-search-results')) {
		loadSearchResults(1);
	}

	initializeImageZoom();

	$(window).scroll(function () {
		if ($('.search-layout').length>0) {
			var scroll = $(window).scrollTop();
			var top = $('.main-section')[0].offsetTop;// + (2.4 * parseFloat(getComputedStyle(document.documentElement).fontSize));
			var headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height-local').replace('rem',''))*parseFloat(getComputedStyle(document.documentElement).fontSize);
			if (scroll>top) {
				$('.search-layout').css({'top': '0', 'height': 'calc(100%)'});
				$('.loading-layout').css({'top': '0', 'height': 'calc(100%)'});
				$('.error-layout').css({'top': '0', 'height': 'calc(100%)'});
				$('.search-layout-toggle-button').css({'top': '0', 'height': 'calc(100%)'});
			} else {
				$('.search-layout').css({'top': (top-scroll)+'px', 'height': 'calc(100% - '+(top-scroll)+'px)'});
				$('.loading-layout').css({'top': (top-scroll)+'px', 'height': 'calc(100% - '+(top-scroll)+'px)'});
				$('.error-layout').css({'top': (top-scroll)+'px', 'height': 'calc(100% - '+(top-scroll)+'px)'});
				$('.search-layout-toggle-button').css({'top': (top-scroll)+'px', 'height': 'calc(100% - '+(top-scroll)+'px)'});
			}
		}
	});

	if ($('#search_query').length>0) {
		var temp = $('#search_query').val();
		$('#search_query').focus().val('').val(temp);
	}

	if ($('.search-layout').length>0) {
		//Date
		const fromSliderDate = $('#date-from-slider')[0];
		const toSliderDate = $('#date-to-slider')[0];
		const fromInputDate = $('#date-from-input')[0];
		const toInputDate = $('#date-to-input')[0];
		fillDoubleSlider(fromSliderDate, toSliderDate, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderDate);
		setDoubleSliderToggleAccessible(fromSliderDate, toSliderDate);
		fromSliderDate.oninput = () => applyDoubleSliderFrom(fromSliderDate, toSliderDate, fromInputDate);
		toSliderDate.oninput = () => applyDoubleSliderTo(fromSliderDate, toSliderDate, toInputDate);

		var temp = $('#news-search-query').val();
		$('#news-search-query').focus().val('').val(temp);
	}
});

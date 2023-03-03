function launchSearch(query) {
	window.location.href='/cerca'+(query!='' ? '/'+encodeURIComponent(encodeURIComponent(query)) : '');
}

$(document).ready(function() {
	$('#search_form').submit(function(){
		launchSearch($('#search_query').val());
		return false;
	});
	$('#search_button').click(function(){
		$('#search_form').submit();
	});

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
});

var currentLinkId=-1;
var currentStartTime=-1;

var cookieOptions = {
	expires: 3650,
	path: '/',
	domain: 'anime.fansubs.cat'
};

function sendAjaxViewEnd(){
	if (currentLinkId!=-1){
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", '/counter.php?link_id='+currentLinkId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime), true);
		xmlHttp.send(null);
		currentLinkId=-1;
		currentStartTime=-1;
	}
}

function sendBeaconViewEnd(){
	if (currentLinkId!=-1){
		navigator.sendBeacon('/counter.php?link_id='+currentLinkId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime));
		currentLinkId=-1;
		currentStartTime=-1;
	}
}

function markLinkAsViewed(link_id){
	var current = Cookies.get('viewed_links', cookieOptions);
	if (current){
		var links = current.split(',');
		if (!links.includes(link_id)){
			links.push(link_id);
			Cookies.set('viewed_links', links.join(','), cookieOptions);
		}
	} else {
		var links = [];
		links.push(link_id);
		Cookies.set('viewed_links', links.join(','), cookieOptions);
	}
	$('.read-indicator[data-link-id='+link_id+']').attr('title','Ja l\'has vist: prem per a marcar-lo com a no vist');
	$('.read-indicator[data-link-id='+link_id+'] span').removeClass('fa-eye-slash');
	$('.read-indicator[data-link-id='+link_id+'] span').addClass('fa-eye');
}

function markLinkAsNotViewed(link_id){
	var current = Cookies.get('viewed_links', cookieOptions);
	if (current){
		var links = current.split(',');
		if (links.includes(link_id)){
			var result = links.filter(function(elem){
				return elem != link_id; 
			});
			Cookies.set('viewed_links', result.join(','), cookieOptions);
		}
	}
	$('.read-indicator[data-link-id='+link_id+']').attr('title','Encara no l\'has vist: prem per a marcar-lo com a vist');
	$('.read-indicator[data-link-id='+link_id+'] span').removeClass('fa-eye');
	$('.read-indicator[data-link-id='+link_id+'] span').addClass('fa-eye-slash');
}

function getSource(method, url){
	var start='<div class="white-popup"><div style="display: flex; height: 100%;">';
	var end='</div></div>';
	if (method=="embed"){
		return start+'<iframe style="flex-grow: 1;" frameborder="0" src="'+url+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>'+end;
	}
	return '<div class="white-popup"><div style="display: flex; height: 100%; justify-content: center; align-items: center;"><div>Mètode de visualització no compatible: '+method+'</div></div></div>';
}

$(document).ready(function() {
	$('#overlay-close').click(function(){
		$('#overlay-content').html('');
		$('#overlay').addClass('hidden');
		$('body').removeClass('no-overflow');
		sendAjaxViewEnd();
	});
	$(".video-player").click(function(){
		$('body').addClass('no-overflow');
		$('#overlay').removeClass('hidden');
		$('#overlay-content').html(getSource($(this).attr('data-method'), atob($(this).attr('data-url'))));
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", '/counter.php?link_id='+$(this).attr('data-link-id')+"&action=open", true);
		xmlHttp.send(null);
		currentLinkId=$(this).attr('data-link-id');
		currentStartTime=Math.floor(new Date().getTime()/1000);
		markLinkAsViewed($(this).attr('data-link-id'));
	});
	$(".read-indicator").click(function(){
		if ($(this).children('.fa').hasClass('fa-eye-slash')){
			markLinkAsViewed($(this).attr('data-link-id'));
		} else {
			markLinkAsNotViewed($(this).attr('data-link-id'));
		}
	});
	$(".version_tab").click(function(){
		$(".version_tab").each(function(){
			$(this).removeClass("version_tab_selected");
		});
		$(".version_content").each(function(){
			$(this).addClass("hidden");
		});
		$(this).addClass("version_tab_selected");
		$("#version_content_"+$(this).attr('data-version-id')).removeClass("hidden");
	});
	$('#search_form').submit(function(){
		if ($('#search_query').val()!=''){
			window.location.href='/cerca/' + encodeURIComponent($('#search_query').val());
		}
		else{
			window.location.href='/';
		}
		return false;
	});
	$('#search_button').click(function(){
		$('#search_form').submit();
	});
	$('#options-button').click(function(){
		$('body').addClass('no-overflow');
		$('#options-overlay').removeClass('hidden');
	});
	$('#options-cancel-button').click(function(){
		$('#options-form').trigger("reset");
		$('#options-overlay').addClass('hidden');
		$('body').removeClass('no-overflow');
	});
	$('#options-save-button').click(function(){
		Cookies.set('show_cancelled', $('#show_cancelled').prop('checked') ? '1' : '0', cookieOptions);
		Cookies.set('show_hentai', $('#show_hentai').prop('checked') ? '1' : '0', cookieOptions);
		var hiddenFansubs = $('#options-fansubs input:not(:checked)');
		var values = [];
		
		for (var i=0;i<hiddenFansubs.length;i++){
			values.push(hiddenFansubs[i].value);
		}
		Cookies.set('hidden_fansubs', values.join(','), cookieOptions);

		location.reload();
	});

	var size = Math.max(parseInt($('.carousel').width()/($(window).width()>650 ? 184 : 122)),1);

	$('.carousel').slick({
		speed: 300,
		infinite: false,
		slidesToShow: size,
		slidesToScroll: size,
		variableWidth: true
	});

	$(window).resize(function() {
		var size = Math.max(parseInt($('.carousel').width()/($(window).width()>650 ? 184 : 122)),1);

		$('.carousel').slick('unslick');
		$('.carousel').slick({
			speed: 300,
			infinite: false,
			slidesToShow: size,
			slidesToScroll: size,
			variableWidth: true
		});
	});

	$(window).on('unload', function() {
		sendBeaconViewEnd();
	});
});

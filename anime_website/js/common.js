var currentLinkId=-1;
var currentStartTime=-1;
var lastWindowWidth=0;

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
	$('.viewed-indicator[data-link-id='+link_id+']').attr('title','Ja l\'has vist: prem per a marcar-lo com a no vist');
	$('.viewed-indicator[data-link-id='+link_id+']').removeClass('not-viewed');
	$('.viewed-indicator[data-link-id='+link_id+']').addClass('viewed');
	$('.viewed-indicator[data-link-id='+link_id+'] span').removeClass('fa-eye-slash');
	$('.viewed-indicator[data-link-id='+link_id+'] span').addClass('fa-eye');
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
	$('.viewed-indicator[data-link-id='+link_id+']').attr('title','Encara no l\'has vist: prem per a marcar-lo com a vist');
	$('.viewed-indicator[data-link-id='+link_id+']').removeClass('viewed');
	$('.viewed-indicator[data-link-id='+link_id+']').addClass('not-viewed');
	$('.viewed-indicator[data-link-id='+link_id+'] span').removeClass('fa-eye');
	$('.viewed-indicator[data-link-id='+link_id+'] span').addClass('fa-eye-slash');
}

function getSource(method, url){
	var start='<div class="white-popup"><div style="display: flex; height: 100%;">';
	var end='</div></div>';
	if (method=="embed"){
		return start+'<iframe style="flex-grow: 1;" frameborder="0" src="'+url+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true" sandbox="allow-forms allow-modals allow-orientation-lock allow-pointer-lock allow-presentation allow-same-origin allow-scripts allow-top-navigation allow-top-navigation-by-user-activation"></iframe>'+end;
	}
	if (method=="direct-video"){
		return start+'<video style="flex-grow: 1; max-width: 100%"  controls autoplay><source src="'+url+'" type="video/mp4">El teu navegador no suporta el vídeo incrustat.</video>'+end;
	}
	return '<div class="white-popup"><div style="display: flex; height: 100%; justify-content: center; align-items: center;"><div>Mètode de visualització no compatible: '+method+'</div></div></div>';
}

function showContactScreen(reason) {
	$('body').addClass('no-overflow');
	$('#contact-overlay').removeClass('hidden');

	if (reason=='version_lost') {
		$('#contact-explanation').text("Hi ha capítols de fansubs antics que sabem que van ser subtitulats, però que actualment no estan disponibles. Si saps on els podem aconseguir, o si ens els pots fer arribar, si us plau, escriu-nos fent servir aquest formulari:");
	} else {
		$('#contact-explanation').text("Per a temes relacionats amb els fansubs, és recomanable que escriguis directament al fansub en qüestió fent servir el seu web o Twitter. En cas contrari, ens pots fer arribar comentaris, avisar-nos d'errors o de qualsevol problema o suggeriment per al web fent servir aquest formulari:");
	}
}

function initTooltip(tooltip, target) {
	var total_width = target.offset().left + tooltip.outerWidth();

	if( total_width > $(window).width()) {
		tooltip.removeClass('tooltip-right');
		tooltip.addClass('tooltip-left');
	} else {
		tooltip.removeClass('tooltip-left');
		tooltip.addClass('tooltip-right');
	}
}

$(document).ready(function() {
	if ($('#embed-page').length==0) {
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
		$(".viewed-indicator").click(function(){
			if ($(this).hasClass('not-viewed')){
				markLinkAsViewed($(this).attr('data-link-id'));
			} else {
				markLinkAsNotViewed($(this).attr('data-link-id'));
			}
		});
		$(".contact-link").click(function(){
			showContactScreen('generic');
		});
		$(".version-lost").click(function(){
			showContactScreen('version_lost');
		});
		$(".version-missing-links-link").click(function(){
			showContactScreen('version_lost');
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
				window.location.href='/cerca/' + encodeURIComponent(encodeURIComponent($('#search_query').val()));
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
			$('#options-tooltip').attr('style','');
			Cookies.set('tooltip_closed', '1', cookieOptions);
		});
		$('#options-tooltip-close').click(function(){
			$('#options-tooltip').attr('style','');
			Cookies.set('tooltip_closed', '1', cookieOptions);
		});
		$('#options-cancel-button').click(function(){
			$('#options-form').trigger("reset");
			$('#options-overlay').addClass('hidden');
			$('body').removeClass('no-overflow');
		});
		$('#options-save-button').click(function(){
			Cookies.set('hide_missing', $('#hide_missing').prop('checked') ? '0' : '1', cookieOptions);
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
		$('#options-select-all').click(function(){
			$('[id^=show_fansub_]').each(function(){
				$(this).prop('checked',true);
			});
		});
		$('#options-unselect-all').click(function(){
			$('[id^=show_fansub_]').each(function(){
				$(this).prop('checked',false);
			});
		});
		$('#contact-cancel-button').click(function(){
			$('#contact-form').trigger("reset");
			$('#contact-overlay').addClass('hidden');
			$('body').removeClass('no-overflow');
		});
		$('#contact-send-button').click(function(){
			if (!/\S+@\S+\.\S+/.test($('#contact_address').val())) {
				alert('Introdueix una adreça de resposta vàlida.');
				return;
			}
			if ($('#contact_message').val()=='') {
				alert('Introdueix un missatge.');
				return;
			}
			$('#contact-send-button').addClass('hidden');
			$('#contact-send-button-loading').removeClass('hidden');
			var xhr = new XMLHttpRequest();
			xhr.open("POST", '/contact.php', true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.onreadystatechange = function() {
				if (this.readyState === XMLHttpRequest.DONE && this.status === 200) {
					$('#contact-send-button-loading').addClass('hidden');
					$('#contact-send-button-done').removeClass('hidden');
					setTimeout(function(){
						$('#contact-send-button-done').addClass('hidden');
						$('#contact-send-button').removeClass('hidden');
						$('#contact-form').trigger("reset");
						$('#contact-overlay').addClass('hidden');
						$('body').removeClass('no-overflow');
					}, 4000);
				} else if (this.readyState === XMLHttpRequest.DONE) {
					alert("S'ha produït un error en enviar el missatge. Torna-ho a provar.");
					$('#contact-send-button-loading').addClass('hidden');
					$('#contact-send-button').removeClass('hidden');
				}
			}
			xhr.send("address="+encodeURIComponent($('#contact_address').val())+"&message="+encodeURIComponent($('#contact_message').val())+"&magic=1714");
		});
		$('.select-genre').click(function(){
			$('.select-genre').removeClass('select-genre-selected');
			$(this).addClass('select-genre-selected');
			var genreId = $(this).attr("data-genre-id");
			if (genreId==-1) {
				$('.catalog > div').removeClass('hidden');
			} else {
				$('.catalog > div').addClass('hidden');
				$('.catalog > div.genre-'+genreId).removeClass('hidden');
			}
		});

		var size = Math.max(parseInt($('.carousel').width()/($(window).width()>650 ? 184 : 122)),1);
		var genresSize = Math.max(parseInt($('.genres-carousel').width()/($(window).width()>650 ? 100 : 100)),1);

		$('.carousel').slick({
			speed: 300,
			infinite: false,
			slidesToShow: size,
			slidesToScroll: size,
			variableWidth: true
		});

		$('.genres-carousel').slick({
			speed: 300,
			infinite: false,
			slidesToShow: genresSize,
			slidesToScroll: genresSize,
			variableWidth: true
		});

		if ($('.synopsis-content').height()>=154) {
			$(".show-more").removeClass('hidden');
			$('.synopsis-content').addClass('expandable-content-hidden');
			$(".show-more a").on("click", function() {
				var linkText = $(this).text();    

				if(linkText === "Mostra'n més..."){
					linkText = "Mostra'n menys";
					$(".synopsis-content").switchClass("expandable-content-hidden", "expandable-content-shown", 400);
				} else {
					linkText = "Mostra'n més...";
					$(".synopsis-content").switchClass("expandable-content-shown", "expandable-content-hidden", 400);
				};

				$(this).text(linkText);
			});
		}

		$(".tooltip-container").click(function () {
			var $title = $(this).find(".tooltip");
			if (!$title.hasClass("hidden")) {
				$title.addClass("hidden");
			} else {
				$(".tooltip").addClass("hidden");
				$title.removeClass("hidden");
			}
		});

		//Clumsy detection for mobile OS... They break tooltips due to bad mouseenter/leave handling
		if(!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
			$(".tooltip-container").mouseenter(function () {
				var $title = $(this).find(".tooltip");
				$title.removeClass("hidden");
			});
			$(".tooltip-container").mouseleave(function () {
				var $title = $(this).find(".tooltip");
				$title.addClass("hidden");
			});
		}

		$(".tooltip").css('max-width', $(window).width()/2);

		$(".tooltip").each(function (){
			initTooltip($(this), $(this).parent());
		});

		if (Cookies.get('tooltip_closed', cookieOptions)!='1') {
			$("#options-tooltip").fadeIn("slow");
		}

		$(window).resize(function() {
			if ($(window).width()!=lastWindowWidth) {
				var size = Math.max(parseInt($('.carousel').width()/($(window).width()>650 ? 184 : 122)),1);
				var genresSize = Math.max(parseInt($('.genres-carousel').width()/($(window).width()>650 ? 100 : 100)),1);

				$('.carousel').slick('unslick');
				$('.carousel').slick({
					speed: 300,
					infinite: false,
					slidesToShow: size,
					slidesToScroll: size,
					variableWidth: true
				});

				$('.genres-carousel').slick('unslick');
				$('.genres-carousel').slick({
					speed: 300,
					infinite: false,
					slidesToShow: genresSize,
					slidesToScroll: genresSize,
					variableWidth: true
				});

				lastWindowWidth=$(window).width();

				$(".tooltip").css('max-width', $(window).width()/2);

				$(".tooltip").each(function (){
					initTooltip($(this), $(this).parent());
				});
			}
		});

		lastWindowWidth=$(window).width();
	} else {
		$('body').addClass('no-overflow');
		$('#overlay-content').html(getSource($('#data-method').val(), atob($('#data-url').val())));
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", '/counter.php?link_id='+$('#data-link-id').val()+"&action=open", true);
		xmlHttp.send(null);
		currentLinkId=$('#data-link-id').val();
		currentStartTime=Math.floor(new Date().getTime()/1000);
		markLinkAsViewed($('#data-link-id').val());
	}

	$(window).on('unload', function() {
		sendBeaconViewEnd();
	});
});

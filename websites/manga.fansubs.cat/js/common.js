var currentFileId=-1;
var currentReadId="";
var currentStartTime=-1;
var currentPagesRead=1;
var lastWindowWidth=0;
var baseUrl='';
var timer;

var cookieOptions = {
	expires: 3650,
	path: baseUrl+'/',
	domain: 'mangav2.fansubs.cat'
};

function getSource(file_id){
	var start='<div class="white-popup"><div style="display: flex; height: 100%;">';
	var end='</div></div>';
	return start+'<iframe style="flex-grow: 1;" frameborder="0" src="/reader.php?file_id='+file_id+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>'+end;
}

function sendAjaxViewEnd(){
	if (currentFileId!=-1){
		clearInterval(timer);
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime)+"&pages_read="+currentPagesRead, true);
		xmlHttp.send(null);
		gtag('event', 'Close manga file', {
			'event_category': "Reader",
			'event_label': currentFileId + " / " + currentPagesRead
		});
		currentFileId=-1;
		currentReadId="";
		currentStartTime=-1;
		currentPagesRead=1;
	}
}

function sendBeaconViewEnd(){
	if (currentFileId!=-1){
		clearInterval(timer);
		navigator.sendBeacon(baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime)+"&pages_read="+currentPagesRead);
		gtag('event', 'Close manga file', {
			'event_category': "Reader",
			'event_label': currentFileId + " / " + currentPagesRead
		});
		currentFileId=-1;
		currentReadId="";
		currentStartTime=-1;
		currentPagesRead=1;
	}
}

function markFileAsViewed(file_id){
	var current = Cookies.get('viewed_files', cookieOptions);
	if (current){
		var files = current.split(',');
		if (!files.includes(file_id)){
			files.push(file_id);
			Cookies.set('viewed_files', files.join(','), cookieOptions);
		}
	} else {
		var files = [];
		files.push(file_id);
		Cookies.set('viewed_files', files.join(','), cookieOptions);
	}
	$('.viewed-indicator[data-file-id='+file_id+']').attr('title','Ja l\'has llegit: prem per a marcar-lo com a no llegit');
	$('.viewed-indicator[data-file-id='+file_id+']').removeClass('not-viewed');
	$('.viewed-indicator[data-file-id='+file_id+']').addClass('viewed');
	$('.viewed-indicator[data-file-id='+file_id+'] span').removeClass('fa-eye-slash');
	$('.viewed-indicator[data-file-id='+file_id+'] span').addClass('fa-eye');
	$('.new-episode[data-file-id='+file_id+']').addClass('hidden');
}

function markFileAsNotViewed(file_id){
	var current = Cookies.get('viewed_files', cookieOptions);
	if (current){
		var files = current.split(',');
		if (files.includes(file_id)){
			var result = files.filter(function(elem){
				return elem != file_id; 
			});
			Cookies.set('viewed_files', result.join(','), cookieOptions);
		}
	}
	$('.viewed-indicator[data-file-id='+file_id+']').attr('title','Encara no l\'has llegit: prem per a marcar-lo com a llegit');
	$('.viewed-indicator[data-file-id='+file_id+']').removeClass('viewed');
	$('.viewed-indicator[data-file-id='+file_id+']').addClass('not-viewed');
	$('.viewed-indicator[data-file-id='+file_id+'] span').removeClass('fa-eye');
	$('.viewed-indicator[data-file-id='+file_id+'] span').addClass('fa-eye-slash');
	$('.new-episode[data-file-id='+file_id+']').removeClass('hidden');
}

function showContactScreen(reason) {
	$('body').addClass('no-overflow');
	$('#contact-overlay').removeClass('hidden');

	if (reason=='version_lost') {
		$('#contact-explanation').text("Hi ha capítols de fansubs antics que sabem que van ser editats, però que actualment no estan disponibles. Si saps on els podem aconseguir, o si ens els pots fer arribar, si us plau, escriu-nos fent servir aquest formulari:");
	} else {
		$('#contact-explanation').text("Per a temes relacionats amb els fansubs, és recomanable que escriguis directament al fansub en qüestió fent servir el seu web o Twitter. En cas contrari, ens pots fer arribar comentaris, avisar-nos d'errors o de qualsevol problema o suggeriment per al web fent servir aquest formulari:");
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
		$(".manga-reader").click(function(){
			$('body').addClass('no-overflow');
			$('#overlay').removeClass('hidden');
			$('#overlay-content').html(getSource($(this).attr('data-file-id')));
			currentFileId=$(this).attr('data-file-id');
			//The chances of collision of this is so low that if we get a collision, it's no problem at all.
			currentReadId=Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5);
			currentStartTime=Math.floor(new Date().getTime()/1000);
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open("GET", baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+$(this).attr('data-file-id')+"&action=open", true);
			xmlHttp.send(null);
			gtag('event', 'Open manga file', {
				'event_category': "Reader",
				'event_label': currentFileId
			});
			markFileAsViewed($(this).attr('data-file-id'));
			timer = setInterval(function myTimer() {
				xmlHttp.open("GET", baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+currentFileId+"&action=notify&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime)+"&pages_read="+currentPagesRead, true);
				xmlHttp.send(null);
			}, 60000);
		});
		$(".viewed-indicator").click(function(){
			if ($(this).hasClass('not-viewed')){
				markFileAsViewed($(this).attr('data-file-id'));
			} else {
				markFileAsNotViewed($(this).attr('data-file-id'));
			}
		});
		$(".contact-link").click(function(){
			showContactScreen('generic');
		});
		$(".fansub-downloads").click(function(){
			window.open(atob($(this).attr('data-url')));
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
				window.location.href=baseUrl+'/cerca/' + encodeURIComponent(encodeURIComponent($('#search_query').val()));
			}
			else{
				window.location.href=baseUrl+'/';
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
			Cookies.set('show_missing', $('#show_missing').prop('checked') ? '1' : '0', cookieOptions);
			Cookies.set('show_cancelled', $('#show_cancelled').prop('checked') ? '1' : '0', cookieOptions);
			Cookies.set('show_hentai', $('#show_hentai').prop('checked') ? '1' : '0', cookieOptions);
			Cookies.set('force_long_strip', $('#force_long_strip').prop('checked') ? '1' : '0', cookieOptions);
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
			xhr.open("POST", baseUrl+'/contact.php', true);
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
			variableWidth: true,
			prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
			nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
		});

		$('.recommendations').slick({
			dots: true,
			appendDots: '.recommendations',
			speed: 600,
			infinite: true,
			autoplay: true,
			autoplaySpeed: 10000,
			slidesToShow: 1,
			slidesToScroll: 1,
			prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
			nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
		});

		$('.genres-carousel').slick({
			speed: 300,
			infinite: false,
			slidesToShow: genresSize,
			slidesToScroll: genresSize,
			variableWidth: true,
			prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
			nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
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

		$("[class*='trackable-']").click(function () {
			var type="";
			var event="";
			var label="";
			if ($(this).hasClass('trackable-oneshots-catalog')){
				type="Click manga";
				event="Click manga on one-shots catalog";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-serialized-catalog')){
				type="Click manga";
				event="Click manga on serialized catalog";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-search-results')){
				type="Click manga";
				event="Click manga on search results";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-featured')){
				type="Click manga";
				event="Click manga on featured section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-latest')){
				type="Click manga";
				event="Click manga on latest updates section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-random')){
				type="Click manga";
				event="Click manga on random section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-popular')){
				type="Click manga";
				event="Click manga on most popular section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-newest')){
				type="Click manga";
				event="Click manga on newest section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-toprated')){
				type="Click manga";
				event="Click manga on top rated section";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-related-anime')){
				type="Click anime";
				event="Click anime on related anime";
				label=$(this).attr('data-manga-id');
			} else if ($(this).hasClass('trackable-related-manga')){
				type="Click manga";
				event="Click manga on related manga";
				label=$(this).attr('data-anime-id');
			} else if ($(this).hasClass('trackable-advent')){
				type="Click advent calendar";
				event="Click advent calendar";
				label="Click advent calendar";
			}
			if (type!='' && event!='' && label!='') {
				gtag('event', event, {
					'event_category': type,
					'event_label': label
				});
			}
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
					variableWidth: true,
					prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
					nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
				});

				$('.genres-carousel').slick('unslick');
				$('.genres-carousel').slick({
					speed: 300,
					infinite: false,
					slidesToShow: genresSize,
					slidesToScroll: genresSize,
					variableWidth: true,
					prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
					nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
				});

				lastWindowWidth=$(window).width();
			}
		});

		lastWindowWidth=$(window).width();
	} else {
		$('body').addClass('no-overflow');
		$('#overlay-content').html(getSource($('#data-file-id')));
		//The chances of collision of this is so low that if we get a collision, it's no problem at all.
		currentReadId=Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5);
		currentStartTime=Math.floor(new Date().getTime()/1000);
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+$('#data-file-id').val()+"&action=open", true);
		xmlHttp.send(null);
		currentFileId=$('#data-file-id').val();
		currentStartTime=Math.floor(new Date().getTime()/1000);
		markFileAsViewed($('#data-file-id').val());
		timer = setInterval(function myTimer() {
			xmlHttp.open("GET", baseUrl+'/counter.php?read_id='+currentReadId+'&file_id='+currentFileId+"&action=notify&time_spent="+(Math.floor(new Date().getTime()/1000)-currentStartTime)+"&pages_read="+currentPagesRead, true);
			xmlHttp.send(null);
		}, 60000);
	}

	$(window).on('unload', function() {
		sendBeaconViewEnd();
	});
});

//Google Analytics
window.dataLayer = window.dataLayer || [];
function gtag(){
	dataLayer.push(arguments)
}
gtag('js', new Date());
gtag('config', 'UA-628107-15', {'transport_type': 'beacon'});

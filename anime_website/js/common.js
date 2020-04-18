function getSource(method, url){
	var start='<div class="white-popup"><div style="display: flex; height: 100%;">';
	var end='</div></div>';
	if (method=="embed"){
		return start+'<iframe style="flex-grow: 1;" frameborder="0" src="'+url+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>'+end;
	}
	return '<div class="white-popup"><div style="display: flex; height: 100%; justify-content: center; align-items: center;"><div>Mètode de visualització no compatible: '+method+'</div></div></div>';
}

$(document).ready(function() {
	//We setup magnific popups for all fansub images
	$(".video-player").each(function(){
		$(this).magnificPopup({
			items: {
				src: getSource($(this).attr('data-method'), atob($(this).attr('data-url'))),
				type: 'inline'
			},
			closeBtnInside: true
		});
		$(this).on('mfpOpen', function(e) {
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open("GET", '/counter.php?link_id='+$(this).attr('data-link-id'), true);
			xmlHttp.send(null);
		});
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
	$("#show_cancelled").change(function() {
		if (this.checked) {
			Cookies.set('show_cancelled', '1', { expires: 3650, path: '/', domain: 'anime.fansubs.cat' });
			$(".status-cancelled").each(function(){
				$(this).parent().removeClass("hidden");
			});
		} else {
			Cookies.set('show_cancelled', '0', { expires: 3650, path: '/', domain: 'anime.fansubs.cat' });
			$(".status-cancelled").each(function(){
				$(this).parent().addClass("hidden");
			});
		}
	});
	$('#search_form').submit(function(){
		if ($('#search_query').val()!=''){
			window.location.href='/cerca/' + $('#search_query').val();
		}
		else{
			window.location.href='/';
		}
		return false;
	});
	$('#search_button').click(function(){
		$('#search_form').submit();
		return true;
	});
});

var currentFileId=-1;
var currentViewId="";
var currentReadStartTime=-1;
var currentPagesRead=1;
var lastWindowWidth=0;
var reportTimer;
//New player
var player = null;
var streamer = null;
var currentMegaFile = null;
var currentVideoTitle = null;
var currentMethod = null;
var currentSourceData = null;
var lastErrorTimestamp = null;
var lastErrorReported = null;
var playedMediaTimer = null;
var playedMediaSeconds = 0;
var enableDebug = false;
var loggedMessages = "";
var pageLoadedDate = Date.now();
var playerWasFullscreen = false;
var lastSearchRequest = null;

nanoid=(t=21)=>{let e="",r=crypto.getRandomValues(new Uint8Array(t));for(;t--;){let n=63&r[t];e+=n<36?n.toString(36):n<62?(n-26).toString(36).toUpperCase():n<63?"_":"-"}return e};

function getNewViewId(){
	if (crypto) {
		return nanoid(24);
	} else {
		return 'JSR-'+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36).substr(2, 5)+Math.random().toString(36);
	}
}

function isEmbedPage(){
	return $('#embed-page').length!=0;
}

function getReaderSource(file_id){
	var start='<div class="white-popup"><div style="display: flex; height: 100%;">';
	var end='</div></div>';
	return start+'<iframe style="flex-grow: 1;" frameborder="0" src="/reader.php?file_id='+file_id+(isEmbedPage() && (window.self==window.top) ? '&hide_close=1' : '')+'" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="true"></iframe>'+end;
}

function sendReadEndAjax(){
	if (currentFileId!=-1){
		clearInterval(reportTimer);
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead, true);
		xmlHttp.send(null);
		currentFileId=-1;
		currentMethod=null;
		currentViewId="";
		currentReadStartTime=-1;
		currentPagesRead=1;
	}
}

function sendReadEndBeacon(){
	if (currentFileId!=-1){
		clearInterval(reportTimer);
		navigator.sendBeacon('/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead);
		currentFileId=-1;
		currentMethod=null;
		currentViewId="";
		currentReadStartTime=-1;
		currentPagesRead=1;
	}
}

function addLog(message){
	console.debug(message);
	var playerTime = '--:--:--';
	try{
		if (player && (player.currentTime() || player.currentTime()===0)) {
			playerTime = Math.floor(player.currentTime());
			var ptHours = Math.floor(player.currentTime() / 3600);
			var ptMinutes = Math.floor(player.currentTime() / 60) - (ptHours * 60);
			var ptSeconds = Math.floor(player.currentTime()) % 60;
			playerTime = ptHours.toString().padStart(2, '0') + ':' + ptMinutes.toString().padStart(2, '0') + ':' + ptSeconds.toString().padStart(2, '0');
		}
	} catch (error) {
		playerTime = '--:--:--';
	}
	loggedMessages+=new Date().toLocaleTimeString()+": ["+playerTime+"] "+message+"\n";
}

function beginVideoTracking(fileId, method){
	markFileAsViewed(fileId);
	currentFileId=fileId;
	currentMethod=method;
	//The chances of collision of this is so low that if we get a collision, it's no problem at all.
	currentViewId=getNewViewId();
	if (!enableDebug) {
		var xhr = new XMLHttpRequest();
		xhr.open("GET", "/counter.php?view_id="+currentViewId+"&file_id="+currentFileId+"&method="+currentMethod+"&action=open", true);
		xhr.send(null);
	} else {
		console.debug('Would have requested: /counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method="+currentMethod+"&action=open");
	}
	reportTimer = setInterval(function tick() {
		if (!enableDebug) {
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "/counter.php?view_id="+currentViewId+"&file_id="+currentFileId+"&method="+currentMethod+"&action=notify&time_spent="+Math.floor(playedMediaSeconds), true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("log="+encodeURIComponent(loggedMessages));
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+"&method="+currentMethod+'&file_id='+currentFileId+"&action=notify&time_spent="+Math.floor(playedMediaSeconds));
		}
	}, 60000);
}

function beginReaderTracking(fileId){
	markFileAsViewed(fileId);
	currentFileId=fileId;
	currentMethod='pages';
	//The chances of collision of this is so low that if we get a collision, it's no problem at all.
	currentViewId=getNewViewId();
	currentReadStartTime=Math.floor(new Date().getTime()/1000);
	var xmlHttp = new XMLHttpRequest();
	xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+fileId+"&method=reader&action=open", true);
	xmlHttp.send(null);
	reportTimer = setInterval(function tick() {
		xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method=reader&action=notify&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead, true);
		xmlHttp.send(null);
	}, 60000);
}

function reportErrorToServer(error_type, error_text){
	if (currentFileId!=-1){
		if (!lastErrorReported || lastErrorReported<=Date.now()-2000) {
			addLog("Error reported");
			lastErrorReported = Date.now();
			var playerTime = 0;
			try {
				if (player && (player.currentTime() || player.currentTime()===0)) {
					playerTime = player.currentTime();
				}
			} catch (error) {
				playerTime = 0;
			}
			if (!enableDebug) {
				var xhr = new XMLHttpRequest();
				xhr.open("POST", '/report_error.php', true);
				xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				xhr.send("file_id="+currentFileId+"&location="+playerTime+"&type="+encodeURIComponent(error_type)+"&text="+encodeURIComponent(error_text));
			} else {
				console.debug('Would have sent error via POST: '+"file_id="+currentFileId+"&location="+playerTime+"&type="+encodeURIComponent(error_type)+"&text="+encodeURIComponent(error_text));
			}
		} else {
			addLog("Error repeated (not reported).");
		}
	}
}

function sendVideoTrackingEndAjax(){
	if (currentFileId!=-1){
		clearInterval(reportTimer);
		if (!enableDebug) {
			var xhr = new XMLHttpRequest();
			xhr.open("POST", "/counter.php?view_id="+currentViewId+"&file_id="+currentFileId+"&method="+currentMethod+"&action=close&time_spent="+Math.floor(playedMediaSeconds), true);
			xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
			xhr.send("log="+encodeURIComponent(loggedMessages));
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method="+currentMethod+"&action=close&time_spent="+Math.floor(playedMediaSeconds));
		}
		currentFileId=-1;
		currentMethod=null;
		currentViewId="";
		lastErrorReported=null;
		loggedMessages="";
		playedMediaSeconds=0;
	}
}

function sendVideoTrackingEndBeacon(){
	if (currentFileId!=-1){
		clearInterval(reportTimer);
		if (!enableDebug) {
			var formData = new FormData();
			formData.append("log", loggedMessages);
			navigator.sendBeacon('/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method="+currentMethod+"&action=close&time_spent="+Math.floor(playedMediaSeconds), formData);
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method="+currentMethod+"&action=close&time_spent="+Math.floor(playedMediaSeconds));
		}
		currentFileId=-1;
		currentMethod=null;
		currentViewId="";
		lastErrorReported=null;
		playedMediaSeconds=0;
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
	$('.viewed-indicator[data-file-id='+file_id+']').attr('title','Ja l\'has vist: prem per a marcar-lo com a no vist');
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
	$('.viewed-indicator[data-file-id='+file_id+']').attr('title','Encara no l\'has vist: prem per a marcar-lo com a vist');
	$('.viewed-indicator[data-file-id='+file_id+']').removeClass('viewed');
	$('.viewed-indicator[data-file-id='+file_id+']').addClass('not-viewed');
	$('.viewed-indicator[data-file-id='+file_id+'] span').removeClass('fa-eye');
	$('.viewed-indicator[data-file-id='+file_id+'] span').addClass('fa-eye-slash');
	$('.new-episode[data-file-id='+file_id+']').removeClass('hidden');
}

function getPlayerErrorEvent() {
	var error = "";
	if (player && player.error() && player.error().code) {
		var message = "";
		if (player.error().message) {
			message = " - "+player.error().message;
		}
		switch (player.error().code) {
			case 1:
				error+='1/ABORTED_BY_USER'+message;
				break;
			case 2:
				error+='2/NETWORK_ERROR'+message;
				break;
			case 3:
				error+='3/DECODER_ERROR'+message;
				break;
			case 4:
				error+='4/NOT_SUPPORTED'+message;
				break;
			default:
				error+=player.error().code+'/UNKNOWN_ERROR'+message;
		}
	} else {
		error+="Error desconegut";
	}
	return error;
}

function replayCurrentVideo() {
	player.play();
}

function hasNextVideo() {
	if (isEmbedPage()) {
		return false;
	}
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) > position;
	});

	if (results.length>0) {
		return true;
	}
	return false;
}

function playNextVideo() {
	playerWasFullscreen = player.isFullscreen();
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player[data-file-id="'+currentFileId+'"]').first().parent().parent().parent().parent().parent().find('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) > position;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		closeOverlay();
		results.first().click();
	}
}

function getTitleForChromecast() {
	if (isEmbedPage()) {
		return $('#data-series').val();
	} else {
		return $('.series_title').first().text();
	}
}

function getSubtitleForChromecast() {
	if (isEmbedPage()) {
		return $('#data-episode-title').val() + " | " + $('#data-fansub').val();
	} else {
		return $('.video-player[data-file-id="'+currentFileId+'"]').first().text() + " | " + $('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-fansub');
	}
}

function getCoverImageUrlForChromecast() {
	if (isEmbedPage()) {
		return $('#data-cover').val();
	} else {
		return $('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-cover');
	}
}

function initializeReader(fileId) {
	$('#overlay-content').html(getReaderSource(fileId));
}

function initializePlayer(title, method, sourceData){
	currentVideoTitle = title;
	currentSourceData = sourceData;
	var sources = JSON.parse(sourceData);
	var start='<div class="white-popup"><div style="display: flex; height: 100%; width: 100%; justify-content: center; align-items: center;">';
	var end='</div></div>';

	if (method=='storage' && Date.now()-pageLoadedDate>=48*3600*1000) {
		parsePlayerError('PAGE_TOO_OLD_ERROR');
	} else {
		switch (method) {
			case 'mega':
				$('#overlay-content').html(start+'<video id="player" playsinline controls disableRemotePlayback class="video-js vjs-default-skin vjs-big-play-centered"></video>'+end);
				break;
			case 'youtube':
			case 'google-drive':
			case 'direct-video':
			case 'storage':
			default:
				var sourcesCode = "";
				for(var i=0;i<sources.length;i++) {
					if (sourcesCode!='') {
						sourcesCode+="\n";
					}
					if (!enableDebug) {
						sourcesCode+='<source type="'+(method=='youtube' ? 'video/youtube' : 'video/mp4')+'" src="'+sources[i].url+(sources[i].url.includes('?') ? '&amp;view_id=' : '?view_id=')+currentViewId+'&amp;file_id='+currentFileId+'" size="'+sources[i].resolution+'"/>';
					} else {
						sourcesCode+='<source type="'+(method=='youtube' ? 'video/youtube' : 'video/mp4')+'" src="'+sources[i].url+'"/>';
					}
				}
				$('#overlay-content').html(start+'<video id="player" playsinline controls disableRemotePlayback class="video-js vjs-default-skin vjs-big-play-centered">'+sourcesCode+'</video>'+end);
				break;
		}
	}

	var highestQuality = 0;
	var allQualities = [];
	for(var i=0;i<sources.length;i++) {
		if (!allQualities.includes(sources[i].resolution)) {
			allQualities.push(parseInt(sources[i].resolution));
		}
		if (sources[i].resolution>highestQuality){
			highestQuality=sources[i].resolution;
		}
	}

	allQualities = allQualities.sort(function(a, b){return b-a});

	var techOrders = ['chromecast', 'html5'];

	if (method=='mega') {
		if (window.chrome && window.chrome.cast && window.cast) {
			cast.framework.CastContext.getInstance().endCurrentSession(true);
		}
		techOrders = ['html5'];
	} else if (method=='youtube') {
		if (window.chrome && window.chrome.cast && window.cast) {
			cast.framework.CastContext.getInstance().endCurrentSession(true);
		}
		techOrders = ['youtube'];
	}

	var options = {
		controls: true,
		language: 'ca',
		errorDisplay: false,
		controlBar: {
			children: [
				"playToggle",
				"progressControl",
				"currentTimeDisplay",
				"timeDivider",
				"durationDisplay",
				"muteToggle",
				"volumeControl",
				"fullscreenToggle"
			]
		},
		techOrder: techOrders,
		chromecast: {
			requestTitleFn: getTitleForChromecast,
			requestSubtitleFn: getSubtitleForChromecast,
			requestCoverImageUrlFn: getCoverImageUrlForChromecast
		},
		youtube: {
			modestbranding: 1,
			iv_load_policy: 3
		},
		plugins: {
			chromecast: {
				buttonPositionIndex: -1
			},
			landscapeFullscreen: {
				fullscreen: {
					enterOnRotate: true,
					alwaysInLandscapeMode: true,
					iOS: false
				}
			},
			hotkeys: {
				enableModifiersForNumbers: false
			}
		}
	};
	$('video').on('contextmenu', function(e) {
		e.preventDefault();
	});
	player = videojs("player", options, function(){
		// Player (this) is initialized and ready.
		if (method=='mega') {
			this.currentSrc = function() {
				return 'mega';
			};
		}
	});

	//Recover from errors if needed
	player.one('canplay', event => {
		if (lastErrorTimestamp) {
			player.currentTime(lastErrorTimestamp);
			lastErrorTimestamp = null;
		}
	});
	setTimeout(function(){
		if (playerWasFullscreen) {
			playerWasFullscreen = false;
			player.requestFullscreen();
		}
	}, 0);
	player.on('ready', function(){
		if ($('.player_extra_upper').length==0) {
			$('<div class="player_extra_upper"><div class="player_extra_title">'+new Option(currentVideoTitle).innerHTML+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close vjs-button" title="Tanca" type="button" onclick="closeOverlay();"><svg aria-hidden="true" focusable="false" height="24" viewBox="4 4 16 16" width="24"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg></button>')+'</div><div class="player_extra_ended"><div id="player_extra_ended_buttons"><button id="player_extra_ended_replay" class="player_extra_ended_button" onclick="replayCurrentVideo();"><span class="fa fa-undo"></span></button>' + (hasNextVideo() ? '<button id="player_extra_ended_next" class="player_extra_ended_button" onclick="playNextVideo();"><span class="fa fa-step-forward"></span></button>' : '') + '</div></div>').appendTo(".video-js");
			if (player.techName_=='Html5') {
				setTimeout(function(){
					if (player) {
						player.play();
					}
				}, 0);
			}
		}
	});
	
	player.on('playing', function(){
		addLog('Playing');
		playedMediaTimer = setInterval(function tick() {
			if (player) {
				playedMediaSeconds+=player.playbackRate();
				//addLog('playedMediaSeconds: '+playedMediaSeconds);
			}
		}, 1000);
	});
	player.on('pause', function(){
		addLog('Paused');
		clearInterval(playedMediaTimer);
	});
	player.on('ended', function(){
		addLog('Ended');
		clearInterval(playedMediaTimer);
	});
	player.on('stalled', function(){
		addLog('Stalled (informative)');
		//Do not clear: on iOS, this is triggered while the video keeps playing...
	});
	player.on('waiting', function(){
		addLog('Waiting');
		clearInterval(playedMediaTimer);
	});
	player.on('error', function(){
		parsePlayerError((currentMethod=='mega' ? 'E_MEGA_PLAYER_ERROR' : 'E_DIRECT_PLAYER_ERROR')+': '+getPlayerErrorEvent());
	});

	if (method=='mega') {
		//No multi-source support: show the first one
		loadMegaStream(sources[0].url);
	}
}

function parsePlayerError(error){
	var title = null;
	var message = null;
	var critical = false;
	var forceRefresh = false;
	switch (true) {
		case /EINTERNAL \(\-1\)/.test(error):
		case /EARGS \(\-2\)/.test(error):
		case /EAGAIN \(\-3\)/.test(error):
		case /ERATELIMIT \(\-4\)/.test(error):
		case /EFAILED \(\-5\)/.test(error):
		case /ETOOMANY \(\-6\)/.test(error):
		case /ERANGE \(\-7\)/.test(error):
		case /EEXPIRED \(\-8\)/.test(error):
		case /ECIRCULAR \(\-10\)/.test(error):
		case /EACCESS \(\-11\)/.test(error):
		case /EEXIST \(\-12\)/.test(error):
		case /EINCOMPLETE \(\-13\)/.test(error):
		case /EKEY \(\-14\)/.test(error):
		case /ESID \(\-15\)/.test(error):
		case /EBLOCKED \(\-16\)/.test(error):
		case /ETEMPUNAVAIL \(\-18\)/.test(error):
			title = "S'ha produït un error";
			message = "S'ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('mega-unknown', error);
			break;
		case /ENOENT \(\-9\)/.test(error):
			critical = true;
			title = "El fitxer no existeix";
			message = "Sembla que el fitxer ja no existeix al proveïdor del vídeo en streaming.<br>Mirarem de corregir-ho ben aviat, disculpa les molèsties.";
			reportErrorToServer('mega-unavailable', error);
			break;
		case /EOVERQUOTA \(\-17\)/.test(error):
		case /Bandwidth limit reached/.test(error):
			forceRefresh = true;
			title = "Límit de MEGA superat";
			message = "Has superat el límit d'ample de banda del proveïdor del vídeo en streaming (MEGA).<br>Segurament estàs provant de mirar un vídeo que s'ha publicat fa molt poc.<br>L'estem copiant automàticament a un servidor alternatiu i d'aquí a poca estona estarà disponible i no veuràs aquest error.<br>Torna a carregar la pàgina d'aquí a una estona i torna-ho a provar.";
			reportErrorToServer('mega-quota-exceeded', error);
			break;
		case /E_MEGA_LOAD_ERROR/.test(error):
			if (/web browser lacks/.test(error) || /Streamer is not defined/.test(error)) {
				critical = true;
				title = "Navegador no compatible";
				message = "Sembla que el teu navegador no és compatible amb el reproductor.<br>Els dispositius iPhone i iPad no admeten la reproducció de vídeos de MEGA.<br>Prova de fer servir un altre navegador o un altre dispositiu.";
				reportErrorToServer('mega-incompatible-browser', error);
			} else if (/NetworkError/.test(error)){
				title = "No hi ha connexió";
				message = "S'ha produït un error de xarxa durant la reproducció del vídeo.<br>Assegura't que tinguis una connexió estable a Internet i torna-ho a provar.";
				reportErrorToServer('mega-connection-error', error);
			} else {
				title = "No s'ha pogut carregar";
				message = "S'ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de recarregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
				reportErrorToServer('mega-load-failed', error);
			}
			break;
		case /PLAYER_ERROR/.test(error):
			switch (true) {
				case /NETWORK_ERROR/.test(error):
					title = "No hi ha connexió";
					message = "S'ha produït un error de xarxa durant la reproducció del vídeo.<br>Assegura't que tinguis una connexió estable a Internet i torna-ho a provar.";
					break;
				case /DECODER_ERROR/.test(error):
					title = "S'ha produït un error";
					message = "S'ha produït un error durant la decodificació del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /NOT_SUPPORTED/.test(error):
					title = "No s'ha pogut carregar";
					message = "S'ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /ABORTED_BY_USER/.test(error):
				default:
					title = "S'ha produït un error";
					message = "S'ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			}
			reportErrorToServer(/E_MEGA_PLAYER_ERROR/.test(error) ? 'mega-player-failed' : 'direct-player-failed', error);
			break;
		case /PAGE_TOO_OLD_ERROR/.test(error):
			forceRefresh = true;
			title = "Cal que actualitzis la pàgina";
			message = "Fa més de 48 hores que vas obrir la pàgina i els enllaços de visualització han caducat.<br>Torna a carregar la pàgina i torna-ho a provar.";
			reportErrorToServer('page-too-old', error);
			break;
		default:
			title = "S'ha produït un error";
			message = "S'ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('unknown', error);
			break;
	}
	lastErrorTimestamp = player ? player.currentTime() : 0;
	shutdownVideoPlayer();
	var start = '<div class="white-popup"><div style="justify-content: center; align-items: center; width: 100%; height: 100%; display: flex; flex-direction: column;" class="video-js"><div class="player_extra_upper" style="box-sizing: border-box;"><div class="player_extra_title">'+new Option(currentVideoTitle).innerHTML+'</div><button class="player_extra_close vjs-button" title="Tanca" type="button" onclick="closeOverlay();"><svg aria-hidden="true" focusable="false" height="24" viewBox="4 4 16 16" width="24"><path d="M0 0h24v24H0z" fill="none"></path><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"></path></svg></button></div>';
	var buttons = forceRefresh ? '<div class="player_error_buttons"><button class="error-close-button" onclick="location.reload();">Torna a carregar la pàgina</button></div>' : (critical ? '<div class="player_error_buttons"><button class="error-close-button" onclick="closeOverlay();">Tanca</button></div>' : '<div class="player_error_buttons"><button class="error-close-button" onclick="initializePlayer(currentVideoTitle, currentMethod, currentSourceData);">Torna-ho a provar</button></div>');
	var end='</div></div>';
	$('#overlay-content').html(start + '<div class="player_error_title"><span class=\"fa fa-exclamation-circle player_error_icon\"></span><br>' + title + '</div><div class="player_error_details">' + message + '</div>' + buttons + '<br><details style="color: #888; font-size: 1.3em; line-height: normal;"><summary style="cursor: pointer;"><strong><u>Detalls tècnics de l\'error</u></strong></summary>' + new Option(error).innerHTML + '<br>Reproducció / Enllaç / Instant: ' + currentViewId + ' / ' + currentFileId + ' / ' + lastErrorTimestamp + '</details>' + end);
}

function loadMegaStream(url){
	currentMegaFile = window.mega.file(url);
	currentMegaFile.loadAttributes((error, file) => {
		if (error){
			parsePlayerError('E_MEGA_LOAD_ERROR: '+error);
		} else {
			addLog('MEGA file loaded: ' + file.name + ', size: ' + file.size);
			streamer = new Streamer(file.downloadId, document.getElementById('player_html5_api'), {type: 'isom'});
			streamer.play();
		}
	});
}

function shutdownVideoPlayer() {
	clearInterval(playedMediaTimer);
	if (player!=null){
		try {
			player.dispose();
		} catch (error) {
			console.log("Error while stopping player: "+error);
		}
		player = null;
	}
	if (streamer!=null){
		streamer.destroy();
		streamer = null;
	}
	$('#overlay-content').html('');
}

function showContactScreen(reason) {
	$('body').addClass('no-overflow');
	$('#contact-overlay').removeClass('hidden');

	if (reason=='version_lost') {
		$('#contact-explanation').text("Hi ha capítols de fansubs antics que sabem que van ser publicats, però que actualment no estan disponibles. Si saps on els podem aconseguir, o si ens els pots fer arribar, si us plau, escriu-nos fent servir aquest formulari:");
	} else {
		$('#contact-explanation').text("Per a temes relacionats amb els fansubs, és recomanable que escriguis directament al fansub en qüestió fent servir el seu web o Twitter. En cas contrari, ens pots fer arribar comentaris, avisar-nos d'errors o de qualsevol problema o suggeriment per al web fent servir aquest formulari:");
	}
}

function showAlert(title, message, showRefresh=false) {
	if (document.fullscreenElement) {
		document.exitFullscreen();
	}
	$('#alert-overlay').removeClass('hidden');
	$('#alert-title').text(title);
	$('#alert-message').text(message);
	if (showRefresh) {
		$('#alert-refresh-button').removeClass('hidden');
		$('#alert-ok-button').text('Ignora');
	} else {
		$('#alert-refresh-button').addClass('hidden');
		$('#alert-ok-button').text('D\'acord');
	}
}

function closeOverlay() {
	addLog('Closed');
	shutdownVideoPlayer();
	sendVideoTrackingEndAjax();
	if (!isEmbedPage()) {
		$('#overlay').addClass('hidden');
		$('body').removeClass('no-overflow');
	} else {
		window.parent.postMessage('embedClosed', '*');
	}
}

function menuOptionUnderlineSetup(element) {
	var target = document.querySelector(".catalogues-underline");
	var links = document.querySelectorAll(".catalogues-navigation a");
	var originalLink = document.querySelector(".catalogue-selected");
	if (!originalLink.classList.contains("catalogue-selected-processed")) {
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
	var maxWidth = $(window).width();
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

function toggleBookmark(seriesSlug){
	var action;
	if ($('.floating-info-bookmark[data-series-id='+seriesSlug+']').hasClass('fas'))	{
		$('.floating-info-bookmark[data-series-id='+seriesSlug+']').removeClass('fas').addClass('far');
		action='remove';
	} else {
		$('.floating-info-bookmark[data-series-id='+seriesSlug+']').removeClass('far').addClass('fas');
		action='add';
	}

	var values = {
		series_slug: seriesSlug,
		action: action
	};

	$.post({
		url: "/do_save_to_my_list.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function initializeCarousels() {
	$('.style-type-catalogue').addClass('has-carousel');

	//Carousel width is equal to the page header in the current design (we use that instead because carousel has not been laid out yet)
	//Element width is the width of .thumbnail-outer plus its margins (1/2 from each side), so we only get one full margin instead
	var carouselWidth = $('.header').width();
	var elementWidth = Math.ceil(window.getComputedStyle(document.querySelector('.thumbnail-outer')).getPropertyValue('width').replace('px',''))
		+ Math.ceil(window.getComputedStyle(document.querySelector('.thumbnail-outer')).getPropertyValue('margin-left').replace('px',''));
	var size = Math.max(parseInt(carouselWidth/elementWidth),1);
	var swipeToSlideSetting = (getComputedStyle(document.documentElement).getPropertyValue('--is-hovering-device')==0);

	$('.carousel').slick({
		speed: 300,
		infinite: false,
		slidesToShow: size,
		slidesToScroll: size,
		swipeToSlide: false,
		variableWidth: true,
		prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
		nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
	});

	$('.recommendations').slick({
		dots: true,
		appendDots: '.recommendations',
		speed: 500,
		fade: true,
		infinite: true,
		autoplay: true,
		autoplaySpeed: 10000,
		slidesToShow: 1,
		slidesToScroll: 1,
		prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
		nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
	});

	$('.recommendations').on('beforeChange', function(event, slick, currentSlide, nextSlide){
		if ((currentSlide==$('.recommendations .slick-dots li').length-1 && nextSlide==0) || nextSlide-currentSlide==1) {
			//Advance
			$('.recommendations .slick-slide[data-slick-index='+currentSlide+'] .infoholder').css({'transition': 'translate .6s ease, opacity .6s ease', 'translate': '-30rem', 'opacity': '0'}).delay(600).queue(function() {
				$(this).css({'transition': 'none', 'translate': '0', 'opacity': '1'});
		 		$(this).dequeue();
			});
			$('.recommendations .slick-slide[data-slick-index='+nextSlide+'] .infoholder').css({'transition': 'none', 'translate': '30rem', 'opacity': '0'}).delay(1).queue(function() {
				$(this).css({'transition': 'translate .6s ease, opacity .6s ease', 'translate': '0', 'opacity': '1'});
				$(this).dequeue();
			});
		} else if ((currentSlide==0 && nextSlide==$('.recommendations .slick-dots li').length-1) || nextSlide-currentSlide==-1) {
			//Go back
			$('.recommendations .slick-slide[data-slick-index='+currentSlide+'] .infoholder').css({'transition': 'translate .6s ease, opacity .6s ease', 'translate': '30rem', 'opacity': '0'}).delay(600).queue(function() {
				$(this).css({'transition': 'none', 'translate': '0', 'opacity': '1'});
		 		$(this).dequeue();
			});
			$('.recommendations .slick-slide[data-slick-index='+nextSlide+'] .infoholder').css({'transition': 'none', 'translate': '-30rem', 'opacity': '0'}).delay(1).queue(function() {
				$(this).css({'transition': 'translate .6s ease, opacity .6s ease', 'translate': '0rem', 'opacity': '1'});
				$(this).dequeue();
			});
		} else {
			//Just fade
		}
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
}

function launchSearch(query) {
	window.location.href=$('.filter-button').attr('href')+(query!='' ? '/'+encodeURIComponent(query) : '');
}

function loadSearchResults() {
	var query = $('#catalogue-search-query').val();
	if (lastSearchRequest==null && query=='') {
		$('.loading-message').text('S’està carregant el catàleg sencer...');
	} else {
		history.replaceState(null, null, $('.search-base-url').val()+(query!='' ? '/'+encodeURIComponent(query) : ''));
		$('.loading-message').text('S’estan carregant els resultats de la cerca...');
	}

	$('.style-type-catalogue').removeClass('has-search-results');
	$('.error-layout').addClass('hidden');
	$('.results-layout').addClass('hidden');
	$('.loading-layout').removeClass('hidden');
	if (lastSearchRequest!=null) {
		lastSearchRequest.abort();
	}
	lastSearchRequest = $.post({
		url: ($('.fa-house-chimney').length>0 ? '/hentai' : '')+"/results.php?search=1&query="+encodeURIComponent($('#catalogue-search-query').val()),
		data: [],
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('.style-type-catalogue').addClass('has-search-results');
		$('.results-layout').html(data);
		$('.loading-layout').addClass('hidden');
		$('.error-layout').addClass('hidden');
		$('.results-layout').removeClass('hidden');
	}).fail(function(xhr, status, error) {
		if (error!='abort') {
			$('.style-type-catalogue').removeClass('has-search-results');
			$('.loading-layout').addClass('hidden');
			$('.results-layout').addClass('hidden');
			$('.error-layout').removeClass('hidden');
		}
	});
}

function loadCatalogueIndex() {
	$('.style-type-catalogue').removeClass('has-carousel');
	$('.loading-layout').removeClass('hidden');
	$('.error-layout').addClass('hidden');
	$('.results-layout').addClass('hidden');
	$.post({
		url: ($('.fa-house-chimney').length>0 ? '/hentai' : '')+"/results.php",
		data: [],
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('.loading-layout').addClass('hidden');
		$('.error-layout').addClass('hidden');
		$('.results-layout').removeClass('hidden');
		$('.results-layout').html(data);
		initializeCarousels();
	}).fail(function(data) {
		$('.style-type-catalogue').removeClass('has-carousel');
		$('.loading-layout').addClass('hidden');
		$('.results-layout').addClass('hidden');
		$('.error-layout').removeClass('hidden');
	});
}

function formatDoubleSliderInput(input, value) {
	var format = $(input).attr('value-formatting');
	if (format=='time' || format=='time-max') {
		//Convert minutes to time
		if (value==120 && format=='time-max') {
			input.innerText='2:00:00+';
		} else {
			input.innerText=Math.floor(value/60)+':'+((value%60)>9 ? (value%60) : '0'+(value%60))+':00';
		}
	} else if (format=='score') {
		//Divide by 10
		input.innerText=Number(value/10).toFixed(1).replaceAll('.',',');
	} else if (format=='rating') {
		if (value==0) {
			input.innerText='TP';
		} else if (value==1) {
			input.innerText='+7';
		} else if (value==2) {
			input.innerText='+13';
		} else if (value==3) {
			input.innerText='+16';
		} else if (value==4) {
			input.innerText='+18';
		} else {
			input.innerText='XXX';
		}
	} else {
		input.innerText=value;
	}
}

function toggleSearchLayout() {
	if ($('.search-layout-toggle-button-visible').length>0) {
		$('.search-layout-toggle-button').removeClass('search-layout-toggle-button-visible');
		$('.search-layout').removeClass('search-layout-visible');
	} else {
		$('.search-layout-toggle-button').addClass('search-layout-toggle-button-visible');
		$('.search-layout').addClass('search-layout-visible');
	}
}

$(document).ready(function() {
	var links = document.querySelectorAll(".catalogues-navigation a");
	var container = document.querySelector(".catalogues-navigation");

	for (let i = 0; i < links.length; i++) {
		links[i].addEventListener("mouseenter", menuOptionMouseEnter);
	}
	container.addEventListener("mouseleave", menuOptionMouseLeave);

	if ($('.absolutely-real').length==0) {
		if ($('.catalogue-index').length==1) {
			loadCatalogueIndex();
		} else if ($('#catalogue-search-query').length==1) {
			loadSearchResults();
		} else {
			initializeCarousels();
		}
	} else {
		if ($('#catalogue-search-query').length==1) {
			$('.style-type-catalogue').addClass('has-search-results');
		} else {			
			initializeCarousels();
		}
	}

	$('#overlay-close').click(function(){
		sendReadEndAjax();
		if (!isEmbedPage()) {
			$('#overlay-content').html('');
			$('#overlay').addClass('hidden');
			$('body').removeClass('no-overflow');
		} else {
			window.parent.postMessage('embedClosed', '*');
		}
	});
	if (!isEmbedPage()) {
		$(".manga-reader").click(function(){
			$('body').addClass('no-overflow');
			$('#overlay').removeClass('hidden');
			beginReaderTracking($(this).attr('data-file-id'));
			initializeReader($(this).attr('data-file-id'));
		});
		$(".video-player").click(function(){
			$('body').addClass('no-overflow');
			$('#overlay').removeClass('hidden');
			beginVideoTracking($(this).attr('data-file-id'), $(this).attr('data-method'));
			initializePlayer($(this).attr('data-title'), $(this).attr('data-method'), atob($(this).attr('data-sources')));
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
			launchSearch($('#search_query').val());
			return false;
		});
		$('#search_button').click(function(){
			$('#search_form').submit();
		});
		$('#options-button').click(function(){
			$('body').addClass('no-overflow');
			$('#options-overlay').removeClass('hidden');
			$('#options-tooltip').attr('style','');
			$('#options-tooltip').addClass('hidden');
			Cookies.set('tooltip_closed', '1', cookieOptions);
		});
		$('#options-tooltip-close').click(function(){
			$('#options-tooltip').attr('style','');
			$('#options-tooltip').addClass('hidden');
			Cookies.set('tooltip_closed', '1', cookieOptions);
		});
		$('#tachiyomi-message-close').click(function(){
			$('#tachiyomi-message').attr('style','display: none;');
			Cookies.set('tachiyomi_message_closed', '1', cookieOptions);
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
			if ($('#force_long_strip').length>0) {
				Cookies.set('force_long_strip', $('#force_long_strip').prop('checked') ? '1' : '0', cookieOptions);
			}
			if ($('#force_reader_ltr').length>0) {
				Cookies.set('force_reader_ltr', $('#force_reader_ltr').prop('checked') ? '1' : '0', cookieOptions);
			}
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
		$('#alert-ok-button').click(function(){
			$('#alert-overlay').addClass('hidden');
		});
		$('#alert-refresh-button').click(function(){
			window.location.reload();
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

		if (Cookies.get('tooltip_closed', cookieOptions)!='1') {
			$("#options-tooltip").removeClass('hidden');
			$("#options-tooltip").fadeIn("slow");
		}

		if (Cookies.get('tachiyomi_message_closed', cookieOptions)=='1') {
			$("#tachiyomi-message").attr('style','display: none;');
		}

		$(window).resize(function() {
			if ($(window).width()!=lastWindowWidth) {
				//Reposition underline
				var target = document.querySelector(".catalogues-underline");
				var active = document.querySelector("a.catalogues-underline-active");
				if (active) {
					const left = active.getBoundingClientRect().left + window.pageXOffset;
					const top = active.getBoundingClientRect().top + window.pageYOffset+2;
					target.style.left = `${left}px`;
					target.style.top = `${top}px`;
				}

				if ($('.has-carousel').length>0) {
					//Recalculate multi-carousels
					//Carousel width is equal to the page header in the current design (we use that instead because carousel has not been laid out yet)
					//Element width is the width of .thumbnail-outer plus its margins (1/2 from each side), so we only get one full margin instead
					var carouselWidth = $('.header').width();
					var elementWidth = Math.ceil(window.getComputedStyle(document.querySelector('.thumbnail-outer')).getPropertyValue('width').replace('px',''))
						+ Math.ceil(window.getComputedStyle(document.querySelector('.thumbnail-outer')).getPropertyValue('margin-left').replace('px',''));
					var size = Math.max(parseInt(carouselWidth/elementWidth),1);
					var swipeToSlideSetting = (getComputedStyle(document.documentElement).getPropertyValue('--is-hovering-device')==0);

					$('.carousel').slick('unslick');
					$('.carousel').slick({
						speed: 300,
						infinite: false,
						slidesToShow: size,
						slidesToScroll: size,
						swipeToSlide: false,
						variableWidth: true,
						prevArrow: '<button data-nosnippet class="slick-prev" aria-label="Anterior" type="button">Anterior</button>',
						nextArrow: '<button data-nosnippet class="slick-next" aria-label="Següent" type="button">Següent</button>'
					});
				}

				lastWindowWidth=$(window).width();
			}
		});

		lastWindowWidth=$(window).width();

		$(window).scroll(function () {
			if ($('.search-layout').length>0) {
				var scroll = $(window).scrollTop();
				var top = $('.main-section')[0].offsetTop;// + (2.4 * parseFloat(getComputedStyle(document.documentElement).fontSize));
				var headerHeight = parseFloat(getComputedStyle(document.documentElement).getPropertyValue('--header-height-catalogue').replace('rem',''))*parseFloat(getComputedStyle(document.documentElement).fontSize);
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

		if ($('.search-layout').length>0) {
			//Duration
			const fromSliderDuration = $('#duration-from-slider')[0];
			const toSliderDuration = $('#duration-to-slider')[0];
			const fromInputDuration = $('#duration-from-input')[0];
			const toInputDuration = $('#duration-to-input')[0];
			fillDoubleSlider(fromSliderDuration, toSliderDuration, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderDuration);
			setDoubleSliderToggleAccessible(toSliderDuration);
			fromSliderDuration.oninput = () => applyDoubleSliderFrom(fromSliderDuration, toSliderDuration, fromInputDuration);
			toSliderDuration.oninput = () => applyDoubleSliderTo(fromSliderDuration, toSliderDuration, toInputDuration);
			
			//Rating
			const fromSliderRating = $('#rating-from-slider')[0];
			const toSliderRating = $('#rating-to-slider')[0];
			const fromInputRating = $('#rating-from-input')[0];
			const toInputRating = $('#rating-to-input')[0];
			fillDoubleSlider(fromSliderRating, toSliderRating, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderRating);
			setDoubleSliderToggleAccessible(toSliderRating);
			fromSliderRating.oninput = () => applyDoubleSliderFrom(fromSliderRating, toSliderRating, fromInputRating);
			toSliderRating.oninput = () => applyDoubleSliderTo(fromSliderRating, toSliderRating, toInputRating);
			
			//Score
			const fromSliderScore = $('#score-from-slider')[0];
			const toSliderScore = $('#score-to-slider')[0];
			const fromInputScore = $('#score-from-input')[0];
			const toInputScore = $('#score-to-input')[0];
			fillDoubleSlider(fromSliderScore, toSliderScore, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderScore);
			setDoubleSliderToggleAccessible(toSliderScore);
			fromSliderScore.oninput = () => applyDoubleSliderFrom(fromSliderScore, toSliderScore, fromInputScore);
			toSliderScore.oninput = () => applyDoubleSliderTo(fromSliderScore, toSliderScore, toInputScore);
		}
	} else {
		$('body').addClass('no-overflow');
		if ($('#data-item-type').val()=='manga') {
			beginReaderTracking($('#data-file-id').val());
			initializeReader($('#data-file-id').val());
		} else {
			beginVideoTracking($('#data-file-id').val(), $('#data-method').val());
			initializePlayer($('#data-title').val(), $('#data-method').val(), atob($('#data-sources').val()));
		}
		window.parent.postMessage('embedInitialized', '*');
	}

	$(window).on('unload', function() {
		addLog('Navigated away');
		if (currentMethod=='pages') {
			sendReadEndBeacon();
		} else {
			sendVideoTrackingEndBeacon();
			shutdownVideoPlayer();
		}
	});
});

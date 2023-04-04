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
var currentVideoDuration = null;
var currentMethod = null;
var currentSourceData = null;
var lastErrorTimestamp = null;
var lastErrorReported = null;
var playedMediaTimer = null;
var playerEndedTimer = null;
var playedMediaSeconds = 0;
var playerEndedMilliseconds = 0;
var enableDebug = true;
var loggedMessages = "";
var pageLoadedDate = Date.now();
var lastSearchRequest = null;
var lastAutocompleteRequest = null;

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
		if (!enableDebug) {
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead, true);
			xmlHttp.send(null);
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead);
		}
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
		if (!enableDebug) {
			navigator.sendBeacon('/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead);
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&action=close&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead);
		}
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
	lastErrorReported=null;
	loggedMessages="";
	playedMediaSeconds=0;
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
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+"&file_id="+currentFileId+"&method="+currentMethod+"&action=notify&time_spent="+Math.floor(playedMediaSeconds));
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
	if (!enableDebug) {
		var xmlHttp = new XMLHttpRequest();
		xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+fileId+"&method=reader&action=open", true);
		xmlHttp.send(null);
	} else {
		console.debug('Would have requested: /counter.php?view_id='+currentViewId+"&file_id="+currentFileId+"&method=reader&action=open");
	}
	reportTimer = setInterval(function tick() {
		if (!enableDebug) {
			var xmlHttp = new XMLHttpRequest();
			xmlHttp.open("GET", '/counter.php?view_id='+currentViewId+'&file_id='+currentFileId+"&method=reader&action=notify&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead, true);
			xmlHttp.send(null);
		} else {
			console.debug('Would have requested: /counter.php?view_id='+currentViewId+"&file_id="+currentFileId+"&method=reader&action=notify&time_spent="+(Math.floor(new Date().getTime()/1000)-currentReadStartTime)+"&pages_read="+currentPagesRead);
		}
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

function hasPrevVideo() {
	if (isEmbedPage()) {
		return false;
	}
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) == position-1;
	});

	if (results.length>0) {
		return true;
	}
	return false;
}

function hasNextVideo() {
	if (isEmbedPage()) {
		return false;
	}
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		return true;
	}
	return false;
}

function playPrevVideo() {
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player[data-file-id="'+currentFileId+'"]').first().parent().parent().parent().parent().parent().find('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) == position-1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		//closeOverlay();
		shutdownVideoStreaming();
		results.first().click();
	}
}

function getNextVideoElement() {
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player[data-file-id="'+currentFileId+'"]').first().parent().parent().parent().parent().parent().find('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		return results.first();
	}
	return null;
}

function playNextVideo() {
	var position  = parseInt($('.video-player[data-file-id="'+currentFileId+'"]').first().attr('data-position'));
	var results = $('.video-player[data-file-id="'+currentFileId+'"]').first().parent().parent().parent().parent().parent().find('.video-player').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		//closeOverlay();
		shutdownVideoStreaming();
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

function initializePlayer(title, method, duration, sourceData){
	currentVideoTitle = title;
	currentSourceData = sourceData;
	currentVideoDuration = duration;
	var sources = JSON.parse(sourceData);
	var start='<div class="player-popup">';
	var end='</div>';
	var sourceUrl;

	if (enableDebug) {
		sourceUrl = sources[0].url;
	} else {
		sourceUrl = sources[0].url+'?view_id='+currentViewId+'&file_id='+currentFileId;
	}

	//Kill Chromecast session if this can not be casted
	if (method=='mega' && window.chrome && window.chrome.cast && window.cast) {
		cast.framework.CastContext.getInstance().endCurrentSession(true);
	}

	//Check for link expiration
	if (method=='storage' && Date.now()-pageLoadedDate>=48*3600*1000) {
		parsePlayerError('PAGE_TOO_OLD_ERROR');
	} else if (player==null) {
		//First play: initialize player
		$('#overlay-content').html(start+'<video id="player" playsinline controls disableRemotePlayback class="video-js vjs-default-skin vjs-big-play-centered">'+(method=='mega' ? '' : ('<source type="video/mp4" src="'+new Option(sourceUrl).innerHTML+'"/>'))+'</video>'+end);
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
			techOrder: ['chromecast', 'html5', 'youtube'],
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
		$('#player').on('contextmenu', function(e) {
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

		player.bigPlayButton.on('click', function(){
			if (player.hasClass('vjs-casting') && player.hasClass('vjs-playing')) {
				player.pause();
			}
		});

		//Recover from errors if needed
		player.one('canplay', event => {
			if (lastErrorTimestamp) {
				player.currentTime(lastErrorTimestamp);
				lastErrorTimestamp = null;
			}
		});
		player.on('ready', function(){
			if ($('.player_extra_upper').length==0) {
				$('<div class="player_extra_upper"><div class="player_extra_title">'+new Option(currentVideoTitle).innerHTML+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-fw fa-times vjs-button" title="Tanca" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo(".video-js");
				var nextVideo = getNextVideoElement();
				$('<div class="player_extra_ended'+(nextVideo==null ? ' hidden' : '')+'"><div class="player_extra_ended_episode"><div class="player_extra_ended_header">Següent capítol:</div><div class="player_extra_ended_title">'+new Option(nextVideo==null ? '' : nextVideo.attr('data-title-short')).innerHTML+'</div><div class="player_extra_ended_thumbnail"><a onclick="playNextVideo();">Reprodueix-lo ara</a><img src="'+(nextVideo==null ? '' : nextVideo.attr('data-thumbnail'))+'" alt=""><div class="player_extra_ended_timer"></div></div></div>').appendTo(".video-js");
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
			hideEndCard();
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
		player.on('seeking', function(){
			hideEndCard();
		});
		player.on('ended', function(){
			addLog('Ended');
			clearInterval(playedMediaTimer);
			playerEndedMilliseconds = 0;
			if (hasNextVideo()) {
				$('.player_extra_ended')[0].style.display='flex';
				playerEndedTimer = setInterval(function tick() {
					if (player) {
						playerEndedMilliseconds+=33;
						$('.player_extra_ended_thumbnail div')[0].style.width=parseFloat(playerEndedMilliseconds/15000)*100+'%';
						if (playerEndedMilliseconds>=15100) {
							playNextVideo();
						}
					} else {
						hideEndCard();
					}
				}, 33);
			}
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
	} else {
		//Player already initialized: reuse it and setup new video
		player.src(sourceUrl);
		$('.player_extra_title')[0].innerHTML=new Option(currentVideoTitle).innerHTML;
		var nextVideo = getNextVideoElement();
		if (nextVideo!=null) {
			$('.player_extra_ended').removeClass('hidden');
			$('.player_extra_ended_title')[0].innerHTML=new Option(getNextVideoElement().attr('data-title-short')).innerHTML;
			$('.player_extra_ended_thumbnail img')[0].src=getNextVideoElement().attr('data-thumbnail');
		} else {
			$('.player_extra_ended').addClass('hidden');
			$('.player_extra_ended_title')[0].innerHTML=new Option('').innerHTML;
			$('.player_extra_ended_thumbnail img')[0].src='';
		}
	}

	//Sync prev/next buttons
	player.controlBar.removeChild('PrevButton');
	player.controlBar.removeChild('NextButton');
	player.controlBar.removeChild('PrevButtonDisabled');
	player.controlBar.removeChild('NextButtonDisabled');
	player.controlBar.addChild(hasPrevVideo() ? "PrevButton" : "PrevButtonDisabled", {}, 5);
	player.controlBar.addChild(hasNextVideo() ? "NextButton" : "NextButtonDisabled", {}, 6);

	//We only support one source for now
	if (method=='mega') {
		loadMegaStream(sources[0].url);
	} else {
		player.play();
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
			title = "S’ha produït un error";
			message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('mega-unknown', error);
			break;
		case /ENOENT \(\-9\)/.test(error):
			critical = true;
			title = "El fitxer no existeix";
			message = "Sembla que el fitxer ja no existeix al proveïdor del vídeo en streaming.<br>Mirarem de corregir-ho ben aviat. Disculpa les molèsties.";
			reportErrorToServer('mega-unavailable', error);
			break;
		case /EOVERQUOTA \(\-17\)/.test(error):
		case /Bandwidth limit reached/.test(error):
			forceRefresh = true;
			title = "Límit de MEGA superat";
			message = "Has superat el límit d’ample de banda del proveïdor del vídeo en streaming (MEGA).<br>Segurament estàs provant de mirar un vídeo que s’ha publicat fa molt poc.<br>L’estem copiant automàticament a un servidor alternatiu i d’aquí a poca estona estarà disponible i no veuràs aquest error.<br>Torna a carregar la pàgina d’aquí a una estona i torna-ho a provar.";
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
				message = "S’ha produït un error de xarxa durant la reproducció del vídeo.<br>Assegura’t que tinguis una connexió estable a Internet i torna-ho a provar.";
				reportErrorToServer('mega-connection-error', error);
			} else {
				title = "No s’ha pogut carregar";
				message = "S’ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de recarregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
				reportErrorToServer('mega-load-failed', error);
			}
			break;
		case /PLAYER_ERROR/.test(error):
			switch (true) {
				case /NETWORK_ERROR/.test(error):
					title = "No hi ha connexió";
					message = "S’ha produït un error de xarxa durant la reproducció del vídeo.<br>Assegura’t que tinguis una connexió estable a Internet i torna-ho a provar.";
					break;
				case /DECODER_ERROR/.test(error):
					title = "S’ha produït un error";
					message = "S’ha produït un error durant la decodificació del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /NOT_SUPPORTED/.test(error):
					title = "No s’ha pogut carregar";
					message = "S’ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /ABORTED_BY_USER/.test(error):
				default:
					title = "S’ha produït un error";
					message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
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
			title = "S’ha produït un error";
			message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tornar a carregar la pàgina.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('unknown', error);
			break;
	}
	lastErrorTimestamp = player ? player.currentTime() : 0;
	shutdownVideoPlayer();
	var start = '<div class="video-js player-error"><div class="player_extra_upper" style="box-sizing: border-box;"><div class="player_extra_title">'+new Option(currentVideoTitle).innerHTML+'</div><button class="player_extra_close fa fa-fw fa-times vjs-button" title="Tanca" type="button" onclick="closeOverlay();"></button></div>';
	var buttons = forceRefresh ? '<div class="player_error_buttons"><button class="error-close-button" onclick="location.reload();">Torna a carregar la pàgina</button></div>' : (critical ? '<div class="player_error_buttons"><button class="error-close-button" onclick="closeOverlay();">Tanca</button></div>' : '<div class="player_error_buttons"><button class="error-close-button" onclick="initializePlayer(currentVideoTitle, currentMethod, currentSourceData);">Torna-ho a provar</button></div>');
	var end='</div>';
	$('#overlay-content').html(start + '<div class="player_error_title"><span class=\"fa fa-exclamation-circle player_error_icon\"></span><br>' + title + '</div><div class="player_error_details">' + message + '</div>' + buttons + '<br><details class="player-error-technical-details"><summary style="cursor: pointer;"><strong><u>Detalls tècnics de l\'error</u></strong></summary>' + new Option(error).innerHTML + '<br>' + currentViewId + ' / ' + currentFileId + ' / ' + lastErrorTimestamp + '</details>' + end);
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

function shutdownVideoStreaming() {
	sendVideoTrackingEndAjax();
	clearInterval(playerEndedTimer);
	clearInterval(playedMediaTimer);
	if (streamer!=null){
		streamer.destroy();
		streamer = null;
	}
	hideEndCard();
}

function shutdownVideoPlayer() {
	shutdownVideoStreaming();
	if (player!=null){
		try {
			player.dispose();
		} catch (error) {
			console.log("Error while stopping player: "+error);
		}
		player = null;
	}
	$('#overlay-content').html('');
}

function hideEndCard() {
	clearInterval(playerEndedTimer);
	if ($('.player_extra_ended').length>0) {
		$('.player_extra_ended')[0].style.display='';
		$('.player_extra_ended_thumbnail div')[0].style.width='0%';
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
	if (!isEmbedPage()) {
		$('#overlay').addClass('hidden');
		$('html').removeClass('page-no-overflow');
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

function bookmarkRemoved(seriesId) {
	//Just ignore it, this is a callback not used in the catalogue section
}

function removeFromContinueWatching(element, fileId){
	var slide = $(element).parent().parent().parent().parent();
	var carousel = slide.parent().parent().parent();
	var index = carousel.find('.slick-slide').index(slide);
	carousel.slick('slickRemove', index);
	if (carousel.find('.slick-slide').length==0) {
		//This was the last element: remove the carousel too
		carousel.slick('unslick');
		carousel.parent().remove();
	}

	var values = {
		file_id: fileId
	};

	$.post({
		url: "/do_remove_from_continue_watching.php",
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

			if(linkText === "Mostra’n més..."){
				linkText = "Mostra’n menys";
				$(".synopsis-content").switchClass("expandable-content-hidden", "expandable-content-shown", 400);
			} else {
				linkText = "Mostra’n més...";
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
	var query = $('#catalogue-search-query').val().trim();
	history.replaceState(null, null, $('.search-base-url').val()+(query!='' ? '/'+encodeURIComponent(query) : ''));
	if (lastSearchRequest==null && query=='' && !$('body').hasClass('has-search-results')) {
		$('.loading-message').text('S’està carregant el catàleg...');
	} else {
		$('.loading-message').text('S’estan carregant els resultats de la cerca...');
	}

	$('.style-type-catalogue').removeClass('has-search-results');
	$('.error-layout').addClass('hidden');
	$('.results-layout').addClass('hidden');
	$('.loading-layout').removeClass('hidden');
	if (lastSearchRequest!=null) {
		lastSearchRequest.abort();
	}

	var statuses = Array();
	for (element of $('.search-status input:checked')) {
		statuses.push($(element).attr('data-id'));
	}

	var demographics = Array();
	for (element of $('.search-demographics input:checked')) {
		demographics.push($(element).attr('data-id'));
	}

	var includedGenres = Array();
	for (element of $('.tristate-genres .tristate-include.tristate-selected')) {
		includedGenres.push($(element).parent().attr('data-id'));
	}

	var excludedGenres = Array();
	for (element of $('.tristate-genres .tristate-exclude.tristate-selected')) {
		excludedGenres.push($(element).parent().attr('data-id'));
	}

	var values = {
		'min_duration': $('#duration-from-slider').val(),
		'max_duration': $('#duration-to-slider').val(),
		'min_rating': $('#rating-from-slider').val(),
		'max_rating': $('#rating-to-slider').val(),
		'min_score': $('#score-from-slider').val(),
		'max_score': $('#score-to-slider').val(),
		'min_year': $('#year-from-slider').val(),
		'max_year': $('#year-to-slider').val(),
		'fansub': $('#catalogue-search-fansub').val(),
		'full_catalogue': $('#catalogue-search-include-full-catalogue').is(':checked') ? 1 : 0,
		'hide_lost_content': $('#catalogue-search-include-lost').is(':checked') ? 0 : 1,
		'type': $('#catalogue-search-type .singlechoice-selected').attr('data-value'),
		'status[]': statuses,
		'demographics[]': demographics,
		'genres_include[]': includedGenres,
		'genres_exclude[]': excludedGenres
	};

	lastSearchRequest = $.post({
		url: ($('.catalogues-explicit-category').length>0 ? '/hentai' : '')+"/results.php?search=1&query="+encodeURIComponent($('#catalogue-search-query').val()),
		data: values,
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

function loadAutocompleteResults() {
	if (lastAutocompleteRequest!=null) {
		lastAutocompleteRequest.abort();
	}

	lastAutocompleteRequest = $.post({
		url: ($('.catalogues-explicit-category').length>0 ? '/hentai' : '')+"/autocomplete.php?query="+encodeURIComponent($('#search_query').val().trim()),
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('#search_query_autocomplete').html(data);
	}).fail(function(xhr, status, error) {
		if (error!='abort') {
			$('#search_query_autocomplete').html('<i class="fa-xl fas fa-circle-exclamation"></i>');
		}
	});
}

function loadCatalogueIndex() {
	$('.style-type-catalogue').removeClass('has-carousel');
	$('.loading-layout').removeClass('hidden');
	$('.error-layout').addClass('hidden');
	$('.results-layout').addClass('hidden');
	$.post({
		url: ($('.catalogues-explicit-category').length>0 ? '/hentai' : '')+"/results.php",
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
	} else if (format=='pages' || format=='pages-max') {
		if (value==100 && format=='pages-max') {
			input.innerText='100+ pàg.';
		} else {
			input.innerText=value+' pàg.';
		}
	} else if (format=='score') {
		//Divide by 10
		if (value==0) {
			input.innerText="-";
		} else {
			input.innerText=Number(value/10).toFixed(1).replaceAll('.',',');
		}
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
	} else if (format=='year') {
		if (value==1950) {
			input.innerText="-";
		} else {
			input.innerText=value;
		}
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

function tristateChange(element) {
	if (!$(element).hasClass('tristate-selected')) {
		$(element).parent().find('.tristate-button').removeClass("tristate-selected");
		$(element).addClass("tristate-selected");
		loadSearchResults();
	} else {
		$(element).parent().find('.tristate-button').removeClass("tristate-selected");
		loadSearchResults();
	}
}

function singlechoiceChange(element) {
	if (!$(element).hasClass('singlechoice-selected')) {
		$(element).parent().find('.singlechoice-button').removeClass("singlechoice-selected");
		$(element).addClass("singlechoice-selected");
		loadSearchResults();
	}
}

function initializeSearchAutocomplete() {
	$('#search_query').on('click', function(e) {
		if (this.value.trim()!='') {
			if ($('#search_query_autocomplete').hasClass('hidden')) {
				$('#search_query_autocomplete').removeClass('hidden');
				$('#search_query_autocomplete').html('<i class="fa-xl fas fa-circle-notch fa-spin"></i>');
				loadAutocompleteResults();
			}
		}
	});
	$('#search_query').on('input', function(e) {
		if (this.value.trim()!='') {
			if ($('#search_query_autocomplete').hasClass('hidden')) {
				$('#search_query_autocomplete').removeClass('hidden');
				$('#search_query_autocomplete').html('<i class="fa-xl fas fa-circle-notch fa-spin"></i>');
			}
			loadAutocompleteResults();
		} else {
			$('#search_query_autocomplete').addClass('hidden');
		}
	});
	$(document).on('click', function (e) {
		var autocomplete = $('#search_query_autocomplete');
		if (!((e.target && e.target.id && e.target.id=='search_query') || (e.target.parentNode && e.target.parentNode.parentNode && e.target.parentNode.parentNode.parentNode && e.target.parentNode.parentNode.parentNode.id && e.target.parentNode.parentNode.parentNode.id=='search_query_autocomplete'))) {
			autocomplete.addClass('hidden');
		}
	});
}

$(document).ready(function() {
	const Button = videojs.getComponent('Button');

	class NextButton extends Button {
		constructor(player, options) {
			super(player, options);
			this.controlText('Capítol següent');
		}
		handleClick() {
			playNextVideo();
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-next-button`;
		}
	}
	class PrevButton extends Button {
		constructor(player, options) {
			super(player, options);
			this.controlText('Capítol anterior');
		}
		handleClick() {
			playPrevVideo();
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-prev-button`;
		}
	}
	class NextButtonDisabled extends Button {
		constructor(player, options) {
			super(player, options);
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-next-button vjs-button-disabled`;
		}
	}
	class PrevButtonDisabled extends Button {
		constructor(player, options) {
			super(player, options);
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-prev-button vjs-button-disabled`;
		}
	}

	videojs.registerComponent('NextButton', NextButton);
	videojs.registerComponent('NextButtonDisabled', NextButtonDisabled);
	videojs.registerComponent('PrevButton', PrevButton);
	videojs.registerComponent('PrevButtonDisabled', PrevButtonDisabled);

	videojs.time.setFormatTime( (seconds, guide) => {
		guide = currentVideoDuration;
		seconds = seconds < 0 ? 0 : seconds;
		let s = Math.floor(seconds % 60);
		let m = Math.floor(seconds / 60 % 60);
		let h = Math.floor(seconds / 3600);
		const gs = Math.floor(guide % 60);
		const gm = Math.floor(guide / 60 % 60);
		const gh = Math.floor(guide / 3600);

		// handle invalid times
		if (isNaN(seconds) || seconds === Infinity) {
			h = gh;
			m = gm;
			s = gs;
		}

		// Check if we need to show hours
		h = h > 0 || gh > 0 ? h + ':' : '';

		// If hours are showing, we may need to add a leading zero.
		// Always show at least one digit of minutes.
		m = ((h || gm >= 10) && m < 10 ? '0' + m : m) + ':';

		// Check if leading zero is need for seconds
		s = s < 10 ? '0' + s : s;
		return h + m + s;
	});

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
			if (!$('body').hasClass('has-search-results')) {
				loadSearchResults();
			}
		} else {
			initializeCarousels();
		}
	} else {
		if ($('.catalogue-index').length==1) {			
			initializeCarousels();
		}
	}

	$('#overlay-close').click(function(){
		sendReadEndAjax();
		if (!isEmbedPage()) {
			$('#overlay-content').html('');
			$('#overlay').addClass('hidden');
			$('html').removeClass('page-no-overflow');
		} else {
			window.parent.postMessage('embedClosed', '*');
		}
	});
	if (!isEmbedPage()) {
		$(".manga-reader").click(function(){
			$('html').addClass('page-no-overflow');
			$('#overlay').removeClass('hidden');
			beginReaderTracking($(this).attr('data-file-id'));
			initializeReader($(this).attr('data-file-id'));
		});
		$(".video-player").click(function(){
			$('html').addClass('page-no-overflow');
			$('#overlay').removeClass('hidden');
			beginVideoTracking($(this).attr('data-file-id'), $(this).attr('data-method'));
			initializePlayer($(this).attr('data-title'), $(this).attr('data-method'), $(this).attr('data-duration'), atob($(this).attr('data-sources')));
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
			$('html').addClass('page-no-overflow');
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
			$('html').removeClass('page-no-overflow');
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
		$('#alert-ok-button').click(function(){
			$('#alert-overlay').addClass('hidden');
		});
		$('#alert-refresh-button').click(function(){
			window.location.reload();
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

		history.replaceState(null, null, window.location.pathname);
		if ($('#autoopen_file_id').length>0 && $('#autoopen_file_id').val()!='') {
			$('a[data-file-id="'+$('#autoopen_file_id').val()+'"]')[0].scrollIntoView();
			$('a[data-file-id="'+$('#autoopen_file_id').val()+'"]').click();
		}

		initializeSearchAutocomplete();

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
			//Duration
			const fromSliderDuration = $('#duration-from-slider')[0];
			const toSliderDuration = $('#duration-to-slider')[0];
			const fromInputDuration = $('#duration-from-input')[0];
			const toInputDuration = $('#duration-to-input')[0];
			fillDoubleSlider(fromSliderDuration, toSliderDuration, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderDuration);
			setDoubleSliderToggleAccessible(fromSliderDuration, toSliderDuration);
			fromSliderDuration.oninput = () => applyDoubleSliderFrom(fromSliderDuration, toSliderDuration, fromInputDuration);
			toSliderDuration.oninput = () => applyDoubleSliderTo(fromSliderDuration, toSliderDuration, toInputDuration);
			
			//Rating
			if ($('#rating-from-slider').length>0) {
				const fromSliderRating = $('#rating-from-slider')[0];
				const toSliderRating = $('#rating-to-slider')[0];
				const fromInputRating = $('#rating-from-input')[0];
				const toInputRating = $('#rating-to-input')[0];
				fillDoubleSlider(fromSliderRating, toSliderRating, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderRating);
				setDoubleSliderToggleAccessible(fromSliderRating, toSliderRating);
				fromSliderRating.oninput = () => applyDoubleSliderFrom(fromSliderRating, toSliderRating, fromInputRating);
				toSliderRating.oninput = () => applyDoubleSliderTo(fromSliderRating, toSliderRating, toInputRating);
			}
			
			//Score
			const fromSliderScore = $('#score-from-slider')[0];
			const toSliderScore = $('#score-to-slider')[0];
			const fromInputScore = $('#score-from-input')[0];
			const toInputScore = $('#score-to-input')[0];
			fillDoubleSlider(fromSliderScore, toSliderScore, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderScore);
			setDoubleSliderToggleAccessible(fromSliderScore, toSliderScore);
			fromSliderScore.oninput = () => applyDoubleSliderFrom(fromSliderScore, toSliderScore, fromInputScore);
			toSliderScore.oninput = () => applyDoubleSliderTo(fromSliderScore, toSliderScore, toInputScore);
			
			//Year
			const fromSliderYear = $('#year-from-slider')[0];
			const toSliderYear = $('#year-to-slider')[0];
			const fromInputYear = $('#year-from-input')[0];
			const toInputYear = $('#year-to-input')[0];
			fillDoubleSlider(fromSliderYear, toSliderYear, 'rgb(var(--neutral-color))', 'rgb(var(--primary-color))', toSliderYear);
			setDoubleSliderToggleAccessible(fromSliderYear, toSliderYear);
			fromSliderYear.oninput = () => applyDoubleSliderFrom(fromSliderYear, toSliderYear, fromInputYear);
			toSliderYear.oninput = () => applyDoubleSliderTo(fromSliderYear, toSliderYear, toInputYear);

			var temp = $('#catalogue-search-query').val();
			$('#catalogue-search-query').focus().val('').val(temp);
		}
	} else {
		$('html').addClass('page-no-overflow');
		if ($('#data-item-type').val()=='manga') {
			beginReaderTracking($('#data-file-id').val());
			initializeReader($('#data-file-id').val());
		} else {
			beginVideoTracking($('#data-file-id').val(), $('#data-method').val());
			initializePlayer($('#data-title').val(), $('#data-method').val(), $('#data-duration').val(), atob($('#data-sources').val()));
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

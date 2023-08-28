//General variables
var lastSearchRequest = null;
var lastAutocompleteRequest = null;
//Player/Reader variables
var player = null;
var streamer = null;
var currentTechOrders = [];
var currentSourceData = null;
var lastRequestedFileId=null;
var lastErrorTimestamp = null;
var lastErrorReported = null;
var playerEndedTimer = null;
var playerEndedMilliseconds = 0;
var enableDebug = false;
var enableDebugUrl = true;
var loggedMessages = "";
var lastTimeUpdate = 0;
var hasBeenCasted = false;
var hasJumpedToInitialPosition = false;
var lastDoubleClickStart = 0;
var isCheckingAsSeenProgrammatically = false;

//Accordion class from: https://css-tricks.com/how-to-animate-the-details-element-using-waapi/
class Accordion {
  constructor(el) {
    // Store the <details> element
    this.el = el;
    // Store the <summary> element
    this.summary = el.querySelector('summary');
    // Store the <div class="content"> element
    this.content = el.querySelector('.division-container');

    // Store the animation object (so we can cancel it if needed)
    this.animation = null;
    // Store if the element is closing
    this.isClosing = false;
    // Store if the element is expanding
    this.isExpanding = false;
    // Detect user clicks on the summary element
    this.summary.addEventListener('click', (e) => this.onClick(e));
  }

  onClick(e) {
    // Stop default behaviour from the browser
    e.preventDefault();
    // Add an overflow on the <details> to avoid content overflowing
    this.el.style.overflow = 'hidden';
    // Check if the element is being closed or is already closed
    if (this.isClosing || !this.el.open) {
      this.open();
    // Check if the element is being openned or is already open
    } else if (this.isExpanding || this.el.open) {
      this.shrink();
    }
  }

  shrink() {
    // Set the element as "being closed"
    this.isClosing = true;
    $(this.el).addClass('closing');
    
    // Store the current height of the element
    const startHeight = `${this.el.offsetHeight}px`;
    // Calculate the height of the summary
    const endHeight = `${this.summary.offsetHeight}px`;
    
    // If there is already an animation running
    if (this.animation) {
      // Cancel the current animation
      this.animation.cancel();
    }
    
    // Start a WAAPI animation
    this.animation = this.el.animate({
      // Set the keyframes from the startHeight to endHeight
      height: [startHeight, endHeight]
    }, {
      duration: 300,
      easing: 'ease-out'
    });
    
    // When the animation is complete, call onAnimationFinish()
    this.animation.onfinish = () => this.onAnimationFinish(false);
    // If the animation is cancelled, isClosing variable is set to false
    this.animation.oncancel = () => {
        this.isClosing = false; 
        $(this.el).removeClass('closing');
    };
  }

  open() {
    // Apply a fixed height on the element
    this.el.style.height = `${this.el.offsetHeight}px`;
    // Force the [open] attribute on the details element
    this.el.open = true;
    // Wait for the next frame to call the expand function
    window.requestAnimationFrame(() => this.expand());
  }

  expand() {
    // Set the element as "being expanding"
    this.isExpanding = true;
    // Get the current fixed height of the element
    const startHeight = `${this.el.offsetHeight}px`;
    // Calculate the open height of the element (summary height + content height)
    const endHeight = `${this.summary.offsetHeight + this.content.offsetHeight}px`;
    
    // If there is already an animation running
    if (this.animation) {
      // Cancel the current animation
      this.animation.cancel();
    }
    
    // Start a WAAPI animation
    this.animation = this.el.animate({
      // Set the keyframes from the startHeight to endHeight
      height: [startHeight, endHeight]
    }, {
      duration: 300,
      easing: 'ease-out'
    });
    // When the animation is complete, call onAnimationFinish()
    this.animation.onfinish = () => this.onAnimationFinish(true);
    // If the animation is cancelled, isExpanding variable is set to false
    this.animation.oncancel = () => this.isExpanding = false;
  }

  onAnimationFinish(open) {
    // Set the open attribute based on the parameter
    this.el.open = open;
    // Clear the stored animation
    this.animation = null;
    // Reset isClosing & isExpanding
    this.isClosing = false;
    $(this.el).removeClass('closing');
    this.isExpanding = false;
    // Remove the overflow hidden and the fixed height
    this.el.style.height = this.el.style.overflow = '';
  }
}

function showAlert(title, desc) {
	showCustomDialog(title, desc, null, true, true, [
		{
			text: 'D’acord',
			class: 'normal-button',
			onclick: function(){
				closeCustomDialog();
			}
		}
	]);
}

function isEmbedPage(){
	return $('.style-type-embed').length!=0;
}

function formatTime(seconds, guide) {
	guide = currentSourceData.length;
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

function reportErrorToServer(error_type, error_text){
	if (!lastErrorReported || lastErrorReported<=Date.now()-2000) {
		addLog("Error reported");
		lastErrorReported = Date.now();
		var formData = new FormData();
		formData.append("view_id", currentSourceData.view_id);
		formData.append("file_id", currentSourceData.file_id);
		formData.append("position", getDisplayerCurrentPosition());
		formData.append("type", error_type);
		formData.append("text", error_text);
		var url = getBaseUrl()+'/report_error.php';
		if (!enableDebug) {
			navigator.sendBeacon(url, formData);
		} else {
			console.debug('Would have requested: '+url);
		}
	} else {
		addLog("Error repeated (not reported).");
	}
}

function getDisplayerCurrentPosition() {
	if (currentSourceData.method=='pages') {
		return getReaderCurrentPage();
	} else {
		return getPlayerCurrentTime();
	}
}

function getDisplayerCurrentProgress() {
	if (currentSourceData.method=='pages') {
		return getReaderReadPages();
	} else {
		return getPlayerPlayedSeconds();
	}
}

function getDisplayerMarkAsSeenPosition() {
	if (currentSourceData.method=='pages') {
		return Math.max(1, Math.floor(currentSourceData.length*0.85), currentSourceData.length-5);
	} else {
		return Math.max(1, Math.floor(currentSourceData.length*0.85), currentSourceData.length-600);
	}
}

function getPlayerCurrentTime() {
	if (player!=null) {
		return Math.floor(player.currentTime());
	} else {
		return 0;
	}
}

function getPlayerPlayedSeconds() {
	var total = 0;
	if (player!=null) {
		for (var i=0; i<player.played().length;i++) {
			total+=(player.played().end(i)-player.played().start(i));
		}
	}
	return Math.floor(total);
}

function getReaderCurrentPage() {
	//TODO IMPLEMENT READER
	return 0;
}

function getReaderReadPages() {
	//TODO IMPLEMENT READER
	return 0;
}

function sendCurrentFileTracking(){
	if (currentSourceData!=null) {
		var position = getDisplayerCurrentPosition();
		var progress = currentSourceData.initial_progress+getDisplayerCurrentProgress();
		var markAsSeenPosition = getDisplayerMarkAsSeenPosition();
		if (position>=markAsSeenPosition && !currentSourceData.is_seen) {
			markAsSeen(currentSourceData.file_id, true);
			currentSourceData.is_seen=true;
		}
		var formData = new FormData();
		//formData.append("log", loggedMessages);
		formData.append("view_id", currentSourceData.view_id);
		formData.append("is_casted", hasBeenCasted ? 1 : 0);
		formData.append("position", position);
		formData.append("progress", progress);
		var url = getBaseUrl()+'/report_file_status.php';
		if (!enableDebug) {
			navigator.sendBeacon(url, formData);
		} else {
			console.debug('Would have requested: '+url);
		}
	}
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

function hasPrevFile() {
	if (isEmbedPage()) {
		return false;
	}
	var position  = parseInt($('.file-launcher[data-file-id="'+currentSourceData.file_id+'"]').first().attr('data-position'));
	var results = $('#version-content-'+currentSourceData.version_id+' .file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) == position-1;
	});

	if (results.length>0) {
		return true;
	}
	return false;
}

function hasNextFile() {
	if (isEmbedPage()) {
		return false;
	}
	var position  = parseInt($('.file-launcher[data-file-id="'+currentSourceData.file_id+'"]').first().attr('data-position'));
	var results = $('#version-content-'+currentSourceData.version_id+' .file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		return true;
	}
	return false;
}

function playPrevFile() {
	var position  = parseInt($('.file-launcher[data-file-id="'+currentSourceData.file_id+'"]').first().attr('data-position'));
	var results = $('#version-content-'+currentSourceData.version_id).first().find('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) == position-1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		sendCurrentFileTracking();
		shutdownFileStreaming();
		results.first().click();
	}
}

function getNextFileElement() {
	var position  = parseInt($('.file-launcher[data-file-id="'+currentSourceData.file_id+'"]').first().attr('data-position'));
	var results = $('#version-content-'+currentSourceData.version_id).first().find('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		return results.first();
	}
	return null;
}

function playNextFile() {
	var position  = parseInt($('.file-launcher[data-file-id="'+currentSourceData.file_id+'"]').first().attr('data-position'));
	var results = $('#version-content-'+currentSourceData.version_id).first().find('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) == position+1;
	});

	if (results.length>0) {
		//In case of multiple files for one episode, only the first will be played
		sendCurrentFileTracking();
		shutdownFileStreaming();
		results.first().click();
	}
}

function getTitleForChromecast() {
	hasBeenCasted=true;
	return currentSourceData.series;
}

function getSubtitleForChromecast() {
	return currentSourceData.title_short;
}

function getCoverImageUrlForChromecast() {
	return currentSourceData.cover;
}

function initializeReader(type) {
	var pagesCode = '';
	for (var i=0; i<currentSourceData.pages.length;i++) {
		pagesCode+='<div class="manga-page swiper-slide"><img src="'+currentSourceData.pages[i]+'" loading="lazy"></div>';
	}
	$('<div class="player-popup swiper manga-reader manga-reader-'+type+'" dir="'+(type=='rtl' ? 'rtl' : 'ltr')+'"><div class="swiper-wrapper">'+pagesCode+'</div><div class="swiper-pagination"></div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>').appendTo('#overlay-content');
	new Swiper('.player-popup', {
		slidesPerView: type=='webtoon' ? 'auto' : 1,
		direction: type=='webtoon' ? 'vertical' : 'horizontal',
		freeMode: type=='webtoon' ? true : false,
		effect: 'slide',
		mousewheel: {
			enabled: true,
			forceToAxis: true,
		},
		keyboard: {
			enabled: true,
			onlyInViewport: false,
		},
		speed: 300,
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
			type: "progressbar",
			renderCustom: function (swiper, current, total) {
				return '<div class="manga-bar"><div>AAA</div>'+current + ' of ' + total+'<div>BBBBB</div></div>';
			},
			renderProgressbar: function (progressbarFillClass) {
				return '<span class="' + progressbarFillClass + '"></span>';
			},
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});
}

function initializeFileDisplayer(){
	if (currentSourceData.method=='pages') {
		initializeReader(currentSourceData.reader_type);
	} else {
		initializePlayer();
	}
}

function initializePlayer(){
	var start='<div class="player-popup">';
	var end='</div>';
	var sourceUrl;
	var techOrders = ['chromecast', 'html5'];

	//Hide previous errors
	$('.player-error').remove();

	if (enableDebugUrl) {
		sourceUrl = currentSourceData.data_sources[0].url;
	} else {
		sourceUrl = currentSourceData.data_sources[0].url+(currentSourceData.data_sources[0].url.includes('?') ? '&' : '?')+'view_id='+currentSourceData.view_id;
	}

	if (currentSourceData.method=='mega') {
		techOrders = ['html5'];
	}
	
	if (techOrders.length!=currentTechOrders.length) {
		//Kill Chromecast session, we are changing techs
		if (window.chrome && window.chrome.cast && window.cast) {
			cast.framework.CastContext.getInstance().endCurrentSession(true);
		}
		shutdownFileDisplayer(false);
		currentTechOrders=techOrders;
	}

	//Reset error state
	lastErrorReported=null;
	loggedMessages="";

	//Check for link expiration
	if (player==null) {
		//First play: initialize player
		$(start+'<video id="player" playsinline controls disableRemotePlayback class="video-js vjs-default-skin vjs-big-play-centered">'+(currentSourceData.method=='mega' ? '' : ('<source type="video/mp4" src="'+new Option(sourceUrl).innerHTML+'"/>'))+'</video>'+end).appendTo('#overlay-content');
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
					"volumePanel",
					"fullscreenToggle"
				],
				volumePanel: {
					vertical: true,
					inline: false
				}
			},
			techOrder: techOrders,
			chromecast: {
				requestTitleFn: getTitleForChromecast,
				requestSubtitleFn: getSubtitleForChromecast,
				requestCoverImageUrlFn: getCoverImageUrlForChromecast
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
			if (currentSourceData.method=='mega') {
				this.currentSrc = function() {
					return 'mega';
				};
			}
		});

		//Allow dragging the seek bar and display its time
		//Let's thank this person for providing this answer: https://stackoverflow.com/a/60748866/1254846
		const SeekBar = videojs.getComponent('SeekBar');
		SeekBar.prototype.getPercent = function getPercent() {
			const time = this.player_.currentTime()
			const percent = time / this.player_.duration()
			return percent >= 1 ? 1 : percent
		}
		SeekBar.prototype.handleMouseMove = function handleMouseMove(event) {
			let newTime = this.calculateDistance(event) * this.player_.duration()
			if (newTime === this.player_.duration()) {
				newTime = newTime - 0.1
			}
			this.player_.currentTime(newTime);
			this.update();
			//Changed the following line to use the proper function
			player.controlBar.currentTimeDisplay.updateTextNode_(player.currentTime());
		}

		player.bigPlayButton.on('click', function(){
			if (player.hasClass('vjs-casting') && player.hasClass('vjs-playing')) {
				player.pause();
			}
		});

		player.on('canplay', event => {
			//Recover from errors if needed
			if (lastErrorTimestamp) {
				player.currentTime(lastErrorTimestamp);
				lastErrorTimestamp = null;
			} else if (!hasBeenCasted && !hasJumpedToInitialPosition) {
				hasJumpedToInitialPosition = true;
				player.currentTime(currentSourceData.initial_position);
			}
		});
		player.on('ready', function(){
			if (player.techName_=='Html5') {
				setTimeout(function(){
					if (player) {
						player.play().catch(error => {
							console.log("Autoplay blocked, setting has-started manually");
							player.addClass('vjs-has-started');
						});
					}
				}, 1);
			}

			//Install double click on sides to FF/RW on touch devices
			if (player.el_.classList.contains("vjs-touch-enabled")) {
				document.querySelector(".vjs-text-track-display").style.pointerEvents = "auto";
				document.querySelector(".vjs-text-track-display").addEventListener("click", function (e) {
					if (lastDoubleClickStart == 0) {
						lastDoubleClickStart = new Date().getTime();
					} else {
						if (((new Date().getTime()) - lastDoubleClickStart) < 500) {
							lastDoubleClickStart = 0;
							const playerWidth = document.querySelector("#player").getBoundingClientRect().width;
							if (0.66 * playerWidth < e.offsetX) {
								if ((player.currentTime()+10)<player.duration()) {
									player.currentTime(player.currentTime() + 10);
									clearTimeout($('.player_extra_backward').stop().data('timer'));
									$('.player_extra_backward').css({'transition': 'none', 'opacity': '0'});
									$('.player_extra_backward_time').html('0');
									clearTimeout($('.player_extra_forward').stop().data('timer'));
									$('.player_extra_forward_time').html(parseInt($('.player_extra_forward_time').text())+10);
									$('.player_extra_forward').css({'transition': 'opacity .6s ease', 'opacity': '1'});
									$('.player_extra_forward').data('timer', setTimeout(function() {
										$('.player_extra_forward').css({'transition': 'none', 'opacity': '0'});
										$('.player_extra_forward_time').html('0');
									}, 1000));
								}
							} else if (e.offsetX < 0.33 * playerWidth) {
								if ((player.currentTime()-10)>=0) {
									player.currentTime(player.currentTime() - 10);
									clearTimeout($('.player_extra_forward').stop().data('timer'));
									$('.player_extra_forward').css({'transition': 'none', 'opacity': '0'});
									$('.player_extra_forward_time').html('0');
									clearTimeout($('.player_extra_backward').stop().data('timer'));
									$('.player_extra_backward_time').html(parseInt($('.player_extra_backward_time').text())+10);
									$('.player_extra_backward').css({'transition': 'opacity .6s ease', 'opacity': '1'});
									$('.player_extra_backward').data('timer', setTimeout(function() {
										$('.player_extra_backward').css({'transition': 'none', 'opacity': '0'});
										$('.player_extra_backward_time').html('0');
									}, 1000));
								}
							} else if (player.paused()) {
								player.play();
							} else {
								player.pause();
							}
						} else {
							lastDoubleClickStart = new Date().getTime();
						}
					}
				});
			}
		});
		player.on('loadstart', function(){
			$('#overlay-content > .player_extra_upper').addClass('hidden');
			//Install the top, movement and ended bar
			if ($('.video-js .player_extra_upper').length==0) {
				$('<div class="player_extra_upper"><div class="player_extra_title">'+new Option(currentSourceData.title).innerHTML+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-fw fa-times vjs-button" title="Tanca" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo(".video-js");
				$('<div class="player_extra_movement"><div class="player_extra_backward"><i class="fas fa-backward"></i><span><span class="player_extra_backward_time">0</span> s</span></div><div class="player_extra_forward"><i class="fas fa-forward"></i><span><span class="player_extra_forward_time">0</span> s</span></div></div>').appendTo(".video-js");
			} else {
				$('.player_extra_title').html(currentSourceData.title);
				$('.player_extra_backward_time').html('0');
				$('.player_extra_forward_time').html('0');
			}
			var nextFile = getNextFileElement();
			if ($('.video-js .player_extra_ended').length==0) {
				$('<div class="player_extra_ended'+(nextFile==null ? ' hidden' : '')+'"><div class="player_extra_ended_episode"><div class="player_extra_ended_header">Següent capítol:</div><div class="player_extra_ended_title">'+new Option(nextFile==null ? '' : nextFile.attr('data-title-short')).innerHTML+'</div><div class="player_extra_ended_thumbnail"><a onclick="playNextFile();">Reprodueix-lo ara</a><img src="'+(nextFile==null ? '' : nextFile.attr('data-thumbnail'))+'" alt=""><div class="player_extra_ended_timer"></div></div></div>').appendTo(".video-js");
			} else  if (nextFile!=null) {
				$('.player_extra_ended').removeClass('hidden');
				$('.player_extra_ended_title')[0].innerHTML=new Option(getNextFileElement().attr('data-title-short')).innerHTML;
				$('.player_extra_ended_thumbnail img')[0].src=getNextFileElement().attr('data-thumbnail');
			} else {
				$('.player_extra_ended').addClass('hidden');
				$('.player_extra_ended_title')[0].innerHTML=new Option('').innerHTML;
				$('.player_extra_ended_thumbnail img')[0].src='';
			}
		});
		
		player.on('playing', function(){
			addLog('Playing');
			hideEndCard();
		});
		player.on('pause', function(){
			addLog('Paused');
		});
		player.on('seeking', function(){
			hideEndCard();
		});
		player.on('ended', function(){
			addLog('Ended');
			if (hasNextFile()) {
				$('.player_extra_ended')[0].style.display='flex';
				playerEndedTimer = setInterval(function tick() {
					if (player) {
						playerEndedMilliseconds+=33;
						$('.player_extra_ended_thumbnail div')[0].style.width=parseFloat(playerEndedMilliseconds/15000)*100+'%';
						if (playerEndedMilliseconds>=15100) {
							playerEndedMilliseconds=0;
							playNextFile();
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
		});
		player.on('error', function(){
			parsePlayerError((currentSourceData.method=='mega' ? 'E_MEGA_PLAYER_ERROR' : 'E_DIRECT_PLAYER_ERROR')+': '+getPlayerErrorEvent());
		});
		player.on('timeupdate', function(){
			if (Math.abs(player.currentTime()-lastTimeUpdate) >= 30) {
				lastTimeUpdate=player.currentTime();
				sendCurrentFileTracking();
			}
		});
	} else {
		//Player already initialized: reuse it and setup new video
		if (currentSourceData.method!='mega') {
			player.src(sourceUrl);
		}
	}

	//Sync prev/next buttons
	player.controlBar.removeChild('PrevButton');
	player.controlBar.removeChild('NextButton');
	player.controlBar.removeChild('PrevButtonDisabled');
	player.controlBar.removeChild('NextButtonDisabled');
	if (!isEmbedPage()) {
		player.controlBar.addChild(hasPrevFile() ? "PrevButton" : "PrevButtonDisabled", {}, 5);
		player.controlBar.addChild(hasNextFile() ? "NextButton" : "NextButtonDisabled", {}, 6);
	}

	//We only support one source for now
	if (currentSourceData.method=='mega') {
		loadMegaStream(sourceUrl);
	} else {
		player.play();
	}
}

function reinitializeFile(retryFullProcess){
	if (currentSourceData!=null && !retryFullProcess) {
		initializeFileDisplayer();
	} else {
		$('.player-error').remove();
		requestFileData(lastRequestedFileId);
	}
}

function parsePlayerError(error){
	var title = null;
	var message = null;
	var critical = false;
	var retryFullProcess = false;
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
			retryFullProcess = true;
			title = "S’ha produït un error";
			message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('mega-unknown', error);
			break;
		case /ENOENT \(\-9\)/.test(error):
			retryFullProcess = true;
			title = "El fitxer no existeix";
			message = "El fitxer ja no existeix al proveïdor del vídeo en streaming.<br>Mirarem de corregir-ho ben aviat. Disculpa les molèsties.";
			reportErrorToServer('mega-unavailable', error);
			break;
		case /EOVERQUOTA \(\-17\)/.test(error):
		case /Bandwidth limit reached/.test(error):
			retryFullProcess = true;
			title = "Límit de MEGA superat";
			message = "Has superat el límit d’ample de banda del proveïdor del vídeo en streaming (MEGA).<br>Segurament has provat de mirar un vídeo que s’ha publicat fa molt poc.<br>L’estem copiant automàticament a un servidor alternatiu i d’aquí a una estona estarà disponible i no hauries de veure aquest error. Torna-ho a provar més tard.";
			reportErrorToServer('mega-quota-exceeded', error);
			break;
		case /E_MEGA_LOAD_ERROR/.test(error):
			retryFullProcess = true;
			if (/web browser lacks/.test(error) || /Streamer is not defined/.test(error)) {
				title = "Navegador no compatible";
				message = "Sembla que el teu navegador no és compatible amb el sistema de reproducció d’aquest vídeo (MEGA).<br>Alguns dispositius iPhone i iPad no admeten la reproducció de vídeos de MEGA.<br>Segurament has provat de mirar un vídeo que s’ha publicat fa molt poc.<br>L’estem copiant automàticament a un servidor alternatiu i d’aquí a una estona estarà disponible i no hauries de veure aquest error. Torna-ho a provar més tard o fes servir un dispositiu diferemt.";
				reportErrorToServer('mega-incompatible-browser', error);
			} else if (/NetworkError/.test(error)){
				title = "No hi ha connexió";
				message = "S’ha produït un error de xarxa durant la reproducció del vídeo.<br>Assegura’t que tinguis una connexió estable a Internet i torna-ho a provar.";
				reportErrorToServer('mega-connection-error', error);
			} else {
				title = "No s’ha pogut carregar";
				message = "S’ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
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
					message = "S’ha produït un error durant la decodificació del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /NOT_SUPPORTED/.test(error):
					title = "No s’ha pogut carregar";
					message = "S’ha produït un error durant la càrrega del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
					break;
				case /ABORTED_BY_USER/.test(error):
				default:
					title = "S’ha produït un error";
					message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			}
			retryFullProcess = /E_MEGA_PLAYER_ERROR/.test(error);
			reportErrorToServer(/E_MEGA_PLAYER_ERROR/.test(error) ? 'mega-player-failed' : 'direct-player-failed', error);
			break;
		case /FAILED_TO_LOAD_ERROR/.test(error):
			retryFullProcess = true;
			title = "No s’ha pogut carregar";
			message = "No s’ha pogut carregar el fitxer. Comprova que tinguis connexió i torna-ho a provar.";
			//reportErrorToServer('file-load-failed', error);
			break;
		default:
			retryFullProcess = true;
			title = "S’ha produït un error";
			message = "S’ha produït un error desconegut durant la reproducció del vídeo.<br>Torna-ho a provar, i si continua sense funcionar, prova de tancar el navegador i tornar-lo a obrir.<br>Si el problema persisteix, contacta amb nosaltres, si us plau.";
			reportErrorToServer('unknown', error);
			break;
	}
	lastErrorTimestamp = player ? player.currentTime() : 0;
	var start = '<div class="player-error">';
	var buttons = (critical ? '<div class="player_error_buttons"><button class="normal-button" onclick="closeOverlay();">Tanca</button></div>' : '<div class="player_error_buttons"><button class="normal-button" onclick="reinitializeFile('+retryFullProcess+');">Torna-ho a provar</button></div>');
	var end='</div>';
	//Remove previous errors
	$('.player-error').remove();
	shutdownFileDisplayer(false);
	$('#overlay-content > .player_extra_upper').removeClass('hidden');
	$(start + '<div class="player_error_title"><span class=\"fa fa-exclamation-circle player_error_icon\"></span><br>' + title + '</div><div class="player_error_details">' + message + '</div>' + buttons + '<br><details class="player-error-technical-details"><summary style="cursor: pointer;"><strong><u>Detalls tècnics de l\'error</u></strong></summary>' + new Option(error).innerHTML + '<br>VID: ' + (currentSourceData!=null ? currentSourceData.view_id : '(null)') + ' / FID: ' + (currentSourceData!=null ? currentSourceData.file_id : '(null)') + ' / TSP: ' + lastErrorTimestamp + '</details>' + end).appendTo('#overlay-content');
}

function loadMegaStream(url){
	window.mega.file(url).loadAttributes((error, file) => {
		if (error){
			parsePlayerError('E_MEGA_LOAD_ERROR: '+error);
		} else {
			addLog('MEGA file loaded: ' + file.name + ', size: ' + file.size);
			streamer = new Streamer(file.downloadId, document.getElementById('player_html5_api'), {type: 'isom'});
			streamer.play();
		}
	});
}

function shutdownFileStreaming() {
	if (player!=null && player.techName_=='Html5') {
		player.pause();
	}
	clearInterval(playerEndedTimer);
	if (streamer!=null){
		streamer.destroy();
		streamer = null;
	}
	hideEndCard();
}

function shutdownFileDisplayer(clearSourceData) {
	shutdownFileStreaming();
	if (player!=null){
		try {
			player.dispose();
		} catch (error) {
			console.log("Error while stopping player: "+error);
		}
		player = null;
	}
	$('.player-popup').remove();
	if (clearSourceData) {
		currentSourceData = null;
	}
}

function hideEndCard() {
	playerEndedMilliseconds=0;
	clearInterval(playerEndedTimer);
	if ($('.player_extra_ended').length>0) {
		$('.player_extra_ended')[0].style.display='';
		$('.player_extra_ended_thumbnail div')[0].style.width='0%';
	}
}

function closeOverlay() {
	addLog('Closed');
	sendCurrentFileTracking();
	shutdownFileDisplayer(true);
	if (!isEmbedPage()) {
		$('#overlay').addClass('hidden');
		$('html').removeClass('page-no-overflow');
		var url = new URL(window.location);
		url.searchParams.delete('f');
		history.replaceState(null, null, url);
	} else {
		window.parent.postMessage('embedClosed', '*');
	}
}

function getPreviousUnreadEpisodes(fileId) {
	var position  = parseInt($('.file-launcher[data-file-id="'+fileId+'"]').first().attr('data-position'));
	return $('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) < position && $(this).find('.episode-seen-cell input[type="checkbox"]:checked').length==0;
	});
}

function setSeenBehavior(value) {
	$('#seen_behavior').val(value);
	var values = {
		'previous_chapters_read_behavior': value,
		'only_read_behavior' : 1
	};
	$.post({
		url: USERS_URL+"/do_save_settings.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function toggleFileSeen(checkbox, fileId) {
	if (!isCheckingAsSeenProgrammatically) {
		if (!$(checkbox).is(':checked')) {
			//Remove from seen
			executeMarkAsSeen([fileId], false);
		} else {
			//Add to seen (and ask for previous if applicable)
			markAsSeen(fileId, false);
		}
	}
}

function markAsSeen(fileId, dontAsk) {
	var previouslyUnreadEpisodes = getPreviousUnreadEpisodes(fileId);
	if (!dontAsk && $('#seen_behavior').val()==0 && previouslyUnreadEpisodes.length>0) {
		showCustomDialog('Vols marcar també els capítols anteriors com a vistos?', 'La decisió que prenguis s’aplicarà automàticament a partir d’ara.', 'Podràs canviar-la a la configuració d’usuari.', false, true, [
			{
				text: 'Sí',
				class: 'normal-button',
				onclick: function(){
					setSeenBehavior(1);
					markAsSeen(fileId, true);
					closeCustomDialog();
				}
			},
			{
				text: 'No',
				class: 'normal-button',
				onclick: function(){
					setSeenBehavior(2);
					markAsSeen(fileId, true);
					closeCustomDialog();
				}
			}
		]);
	} else if ($('#seen_behavior').val()==1) {
		//1: Mark as seen INCLUDING all unread episodes previous to the current one
		var previouslyUnreadEpisodeIds = previouslyUnreadEpisodes.get().map(a => $(a).attr('data-file-id'));

		isCheckingAsSeenProgrammatically = true;
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-seen-cell input[type="checkbox"]').prop('checked', true);
		for (var i=0;i<previouslyUnreadEpisodeIds.length;i++) {
			$('.file-launcher[data-file-id="'+previouslyUnreadEpisodeIds[i]+'"]').find('.episode-seen-cell input[type="checkbox"]').prop('checked', true);
		}
		isCheckingAsSeenProgrammatically = false;
		executeMarkAsSeen(previouslyUnreadEpisodeIds.concat([fileId]), true);
	} else if ($('#seen_behavior').val()==-1) {
		//-1: User logged out, do nothing
		if (!dontAsk) {
			showAlert('Cal iniciar la sessió', 'Per a poder fer un seguiment dels capítols, cal estar registrat a Fansubs.cat.<br>Pots registrar-t’hi a la part superior dreta del web.');
		}
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-seen-cell input[type="checkbox"]').prop('checked', false);
	} else {
		//2 (or 0 with dontAsk): Mark only the current file
		isCheckingAsSeenProgrammatically = true;
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-seen-cell input[type="checkbox"]').prop('checked', true);
		isCheckingAsSeenProgrammatically = false;
		executeMarkAsSeen([fileId], true);
	}
}

function executeMarkAsSeen(fileIds, isSeen) {
	var formData = new FormData();
	formData.append("action", isSeen ? 'add' : 'remove');
	for (var i=0;i<fileIds.length;i++) {
		formData.append("file_id[]", fileIds[i]);
	}
	var url = getBaseUrl()+'/mark_as_seen.php';
	if (!enableDebug) {
		navigator.sendBeacon(url, formData);
	} else {
		console.debug('Would have requested: '+url);
	}
}

function bookmarkRemoved(seriesId) {
	//Just ignore it, this is a callback not used in the catalogue section
}

function toggleBookmarkFromSeriesPage(){
	if ($('body.user-logged-in').length==0) {
		showAlert('Cal iniciar la sessió', 'Per a poder afegir elements a la teva llista, cal estar registrat a Fansubs.cat.<br>Pots registrar-t’hi a la part superior dreta del web.');
		return;
	}
	var action;
	var seriesId = $('#series_id').val();
	if ($('.remove-from-my-list').length>0)	{
		$('.remove-from-my-list').addClass('add-to-my-list').removeClass('remove-from-my-list').html('<i class="far fa-fw fa-bookmark"></i> Afegeix a la meva llista');
		action='remove';
		bookmarkRemoved(seriesId);
	} else {
		$('.add-to-my-list').addClass('remove-from-my-list').removeClass('add-to-my-list').html('<i class="fas fa-fw fa-bookmark"></i> A la meva llista');
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

function removeFromContinueWatching(element, fileId){
	var slide = $(element).parent().parent().parent().parent();
	var wrapper = slide.parent();
	var index = wrapper.children().index(slide);
	wrapper.parent().get(0).swiper.removeSlide(index);
	if (wrapper.children().length==0) {
		wrapper.parent().parent().remove();
	}

	var values = {
		file_id: fileId
	};

	$.post({
		url: getBaseUrl()+"/remove_from_continue_watching.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function initializeCarousels() {
	$('.style-type-catalogue').addClass('has-carousel');
	new Swiper('.recommendations', {
		slidesPerView: 1,
		direction: 'horizontal',
		effect: 'fade',
		speed: 500,
		loop: true,
		parallax: true,
		pagination: {
			el: '.swiper-pagination',
			clickable: true,
		},
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
		autoplay: {
			delay: 10000,
			disableOnInteraction: true,
		},
		fadeEffect: {
			crossFade: true,
		},
	});

	new Swiper('.carousel', {
		slidesPerView: "auto",
		slidesPerGroupAuto: true,
		maxBackfaceHiddenSlides: 0,
		direction: 'horizontal',
		navigation: {
			nextEl: '.swiper-button-next',
			prevEl: '.swiper-button-prev',
		},
	});

	//UGLY HACK! slidesPerViewDynamic are wrongly computed, which results in this issue: https://github.com/nolimits4web/swiper/issues/4964
	//We override the function in order to use our logic. It works, but this assumes that all elements are the same width and WILL BREAK otherwise.
	for (element of $('.carousel')) {
		element.swiper.slidesPerViewDynamic = function(){
			var totalWidth = $(this.wrapperEl).width();
			var elementWidth = $($(this.wrapperEl).find('.swiper-slide')[0]).width();
			return Math.floor(totalWidth / elementWidth);
		};
	}

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

function formatCatalogueSearchQueryString(values) {
	var queryString = "?";
	queryString+='min_duration='+values.min_duration+'&max_duration='+values.max_duration;
	queryString+='&min_rating='+values.min_rating+'&max_rating='+values.max_rating;
	queryString+='&min_score='+values.min_score+'&max_score='+values.max_score;
	queryString+='&min_year='+values.min_year+'&max_year='+values.max_year;
	if (values.fansub!='-1') {
		queryString+='&fansub='+values.fansub;
	}
	if (values.full_catalogue=='1') {
		queryString+='&full_catalogue=1';
	}
	if (values.show_lost_content=='1') {
		queryString+='&show_lost_content=1';
	}
	if (values.type!='all') {
		queryString+='&type='+values.type;
	}
	for(var i=0;i<values['status[]'].length;i++) {
		queryString+='&status[]='+values['status[]'][i];
	}
	for(var i=0;i<values['demographics[]'].length;i++) {
		queryString+='&demographics[]='+values['demographics[]'][i];
	}
	for(var i=0;i<values['genres_include[]'].length;i++) {
		queryString+='&genres_include[]='+values['genres_include[]'][i];
	}
	for(var i=0;i<values['genres_exclude[]'].length;i++) {
		queryString+='&genres_exclude[]='+values['genres_exclude[]'][i];
	}
	return queryString;
}

function loadSearchResults() {
	var query = $('#catalogue-search-query').val().trim();
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
		'min_rating': $('#rating-from-slider').length>0 ? $('#rating-from-slider').val() : 0,
		'max_rating': $('#rating-to-slider').length>0 ? $('#rating-to-slider').val() : 4,
		'min_score': $('#score-from-slider').val(),
		'max_score': $('#score-to-slider').val(),
		'min_year': $('#year-from-slider').val(),
		'max_year': $('#year-to-slider').val(),
		'fansub': $('#catalogue-search-fansub').val(),
		'full_catalogue': $('#catalogue-search-include-full-catalogue').is(':checked') ? 1 : 0,
		'show_lost_content': $('#catalogue-search-include-lost').is(':checked') ? 1 : 0,
		'type': $('#catalogue-search-type .singlechoice-selected').attr('data-value'),
		'status[]': statuses,
		'demographics[]': demographics,
		'genres_include[]': includedGenres,
		'genres_exclude[]': excludedGenres
	};

	history.replaceState(null, null, $('.search-base-url').val()+(query!='' ? '/'+encodeURIComponent(query) : '')+formatCatalogueSearchQueryString(values));

	lastSearchRequest = $.post({
		url: getBaseUrl()+"/results.php?search=1&query="+encodeURIComponent($('#catalogue-search-query').val()),
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
		url: getBaseUrl()+"/autocomplete.php?query="+encodeURIComponent($('#search_query').val().trim()),
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
		url: getBaseUrl()+"/results.php",
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
	var format = $(input).attr('data-value-formatting');
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

function acceptHentaiWarning() {
	Cookies.set('hentai_warning_accepted', '1', cookieOptions);
	$('#warning-overlay').remove();
	$('html').removeClass('page-no-overflow');
}

function requestFileData(fileId) {
	lastRequestedFileId = fileId;
	hasBeenCasted = false;
	hasJumpedToInitialPosition = false
	lastTimeUpdate = 0;
	var values = {
		file_id: fileId
	};

	$.post({
		url: getBaseUrl()+"/get_file_data.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		var response = JSON.parse(data);
		if (response.result=='ok') {
			currentSourceData = response.data;
			initializeFileDisplayer();
		} else {
			parsePlayerError('FAILED_TO_LOAD_ERROR');
		}
	}).fail(function(data) {
		parsePlayerError('FAILED_TO_LOAD_ERROR');
	});
}

function applyVersionRating(pressedButton, oppositeButton, ratingClicked) {
	var value;
	if (pressedButton.hasClass("version-fansub-rating-selected")) {
		//rated this way, mark as unrated
		pressedButton.removeClass("version-fansub-rating-selected");
		value = 0;
	} else {
		//not rated this way, mark as rated
		pressedButton.addClass("version-fansub-rating-selected");
		value = ratingClicked;
		//remove other button
		if (oppositeButton.hasClass("version-fansub-rating-selected")) {
			oppositeButton.removeClass("version-fansub-rating-selected");
		}
	}

	var values = {
		version_id: +$('.version-tab-selected').attr('data-version-id'),
		rating: value
	};

	$.post({
		url: getBaseUrl()+"/rate_version.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	});
}

function resizeSynopsisHeight() {
	var maxSynopsisHeight = $('.series-synopsis-real').length>0 ? parseFloat(1.2 * 5 * parseFloat(getComputedStyle($('.series-synopsis-real')[0]).fontSize)) : 0;

	if ($('.series-synopsis-real').hasClass('expandable-content-default')) {
		$('.series-synopsis-real').removeClass('expandable-content-default');
	}

	if ($('.series-synopsis-real').height()>maxSynopsisHeight) {
		$(".show-more").removeClass('hidden');
		$(".show-more").addClass('has-been-shown');
		if (!$('.series-synopsis-real').hasClass('expandable-content-shown')) {
			$('.series-synopsis-real').addClass('expandable-content-hidden');
		}
	} else if (!$(".show-more").hasClass('has-been-shown')) {
		$(".show-more").addClass('hidden');
		$('.series-synopsis-real').removeClass('expandable-content-hidden');
	}
}

$(document).ready(function() {
	$(".show-more").on("click", function() {
		if($('.series-synopsis-real').hasClass('expandable-content-hidden')){
			linkText = '<span class="fa fa-fw fa-caret-up"></span> Mostra’n menys <span class="fa fa-fw fa-caret-up"></span>';
			$(".series-synopsis-real").removeClass("expandable-content-hidden");
			$(".series-synopsis-real").addClass("expandable-content-shown");
		} else {
			linkText = '<span class="fa fa-fw fa-caret-down"></span> Mostra’n més <span class="fa fa-fw fa-caret-down"></span>';
			$(".series-synopsis-real").removeClass("expandable-content-shown");
			$(".series-synopsis-real").addClass("expandable-content-hidden");
		};

		$(this).html(linkText);
	});
	resizeSynopsisHeight();

	const Button = videojs.getComponent('Button');

	class NextButton extends Button {
		constructor(player, options) {
			super(player, options);
			this.controlText('Capítol següent');
		}
		handleClick() {
			playNextFile();
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
			playPrevFile();
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

	videojs.time.setFormatTime(formatTime);

	if ($('.robo-message').length==0) {
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
	if (!isEmbedPage()) {
		document.querySelectorAll('details').forEach((el) => {
			new Accordion(el);
		});
		$(".file-launcher").click(function(){
			$('html').addClass('page-no-overflow');
			$('#overlay').removeClass('hidden');
			
			if ($('#overlay-content > .player_extra_upper').length==0) {
				$('<div class="player_extra_upper"><div class="player_extra_title">'+$(this).attr('data-title')+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-fw fa-times vjs-button" title="Tanca" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo("#overlay-content");
			} else {
				$('#overlay-content > .player_extra_upper').removeClass('hidden');
				$('.player_extra_title').html($(this).attr('data-title'));
			}
			//Remove previous errors
			$('.player-error').remove();
			requestFileData($(this).attr('data-file-id'));
			var url = new URL(window.location);
			url.searchParams.set('f', $(this).attr('data-file-id'));
			history.replaceState(null, null, url);
		});

		$(".remove-from-my-list, .add-to-my-list").click(function(){
			toggleBookmarkFromSeriesPage();
		});

		$(".empty-divisions").click(function(){
			$(this).parent().parent().find('.division').removeClass('hidden');
			$(this).parent().parent().find('.empty-divisions').addClass('hidden');
		});

		$(".fansub-downloads").click(function(){
			window.open(atob($(this).attr('data-url')));
		});
		$(".version-tab").click(function(){
			$(".version-tab").each(function(){
				$(this).removeClass("version-tab-selected");
			});
			$(".version-content").each(function(){
				$(this).addClass("hidden");
			});
			$(this).addClass("version-tab-selected");
			$("#version-content-"+$(this).attr('data-version-id')).removeClass("hidden");
			if ($(".version-tab").length>1) {
				var url = new URL(window.location);
				url.searchParams.set('v', $(this).attr('data-version-id'));
				history.replaceState(null, null, url);
			}
		});
		$(".version-fansub-rating-positive").click(function(){
			var oppositeButton = $(this).parent().find('.version-fansub-rating-negative');
			applyVersionRating($(this), oppositeButton, 1);
		});
		$(".version-fansub-rating-negative").click(function(){
			var oppositeButton = $(this).parent().find('.version-fansub-rating-positive');
			applyVersionRating($(this), oppositeButton, -1);
		});

		//Search form
		$('#search_form').submit(function(){
			launchSearch($('#search_query').val());
			return false;
		});
		$('#search_button').click(function(){
			$('#search_form').submit();
		});
		initializeSearchAutocomplete();

		//Autoopen according to parameters
		if ($('#autoopen_file_id').length>0 && $('#autoopen_file_id').val()!='') {
			//Select version
			if ($('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('.version-content').length>0) {
				var versionTab = $('.version-tab[data-version-id="'+$('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('.version-content')[0].id.split('-').pop()+'"]');
				versionTab.click();
				//Select season
				if ($('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('details').length>0) {
					$($('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('details')[0]).attr('open', true);
				}
				//Scroll and click file
				$('[data-file-id="'+$('#autoopen_file_id').val()+'"]')[0].scrollIntoView();
				$('[data-file-id="'+$('#autoopen_file_id').val()+'"]').click();
			}
		}

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

		//Search page logic
		if ($('#search_query').length>0 && $('.is-series-page').length==0) {
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
		//This is an embed
		$('html').addClass('page-no-overflow');
		$('#overlay').removeClass('hidden');
		
		if ($('#overlay-content > .player_extra_upper').length==0) {
			$('<div class="player_extra_upper"><div class="player_extra_title">'+$('.embed-data').attr('data-title')+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-fw fa-times vjs-button" title="Tanca" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo("#overlay-content");
		} else {
			$('#overlay-content > .player_extra_upper').removeClass('hidden');
			$('.player_extra_title').html($('.embed-data').attr('data-title'));
		}
		//Remove previous errors
		$('.player-error').remove();
		requestFileData($('.embed-data').attr('data-file-id'));
		window.parent.postMessage('embedInitialized', '*');
	}

	$(window).on('visibilitychange', function() {
		if (document.visibilityState!="visible") {
			addLog('Page is now hidden from user sight');
			sendCurrentFileTracking();
		}
	});
});

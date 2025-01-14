//General variables
var lastSearchRequest = null;
var lastAutocompleteRequest = null;
//Player/Reader variables
var player = null;
var streamer = null;
var currentMegaFile = null;
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
var pagesRead = [];
var stripImagesLoaded = 0;
var stripImagesLoadedReqNo = 0;
var isUserActive = true;
//Used for tracking user activity in manga reader
var mouseInProgress;
var lastMoveX;
var lastMoveY;
var inactivityTimeout;
var activityCheckInterval;
var currentPlayRate = 1;
var spoilerCheckedAutomatically = false;

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

function isEmbedPage(){
	return $('.style-type-embed').length!=0;
}

//Taken from: https://gist.github.com/codeguy/6684588
function string_to_slug(str) {
	str = str.replace(/^\s+|\s+$/g, ''); // trim
	str = str.toLowerCase();

	// remove accents, swap ñ for n, etc
	var from = "àáäâèéëêìíïîòóöôùúüûñç·/_,:;'’";
	var to   = "aaaaeeeeiiiioooouuuunc--------";
	for (var i=0, l=from.length ; i<l ; i++) {
		str = str.replace(new RegExp(from.charAt(i), 'g'), to.charAt(i));
	}

	str = str.replace(/[^a-z0-9 -]/g, '') // remove invalid chars
		.replace(/\s+/g, '-') // collapse whitespace and replace by -
		.replace(/-+/g, '-'); // collapse dashes

	return str;
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

function refreshRandomResults() {
	$('.sort-order').find('.fa-refresh').addClass('fa-spin');
	$.post({
		url: getBaseUrl()+"/random_results.php",
		data: null,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		$('.sort-order').parent().parent().find('.carousel')[0].outerHTML=data;
		new Swiper($('.sort-order').parent().parent().find('.carousel')[0], {
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
		for (element of $('.sort-order').parent().parent().find('.carousel')) {
			element.swiper.slidesPerViewDynamic = function(){
				var totalWidth = $(this.wrapperEl).width();
				var elementWidth = $($(this.wrapperEl).find('.swiper-slide')[0]).width();
				return Math.floor(totalWidth / elementWidth);
			};
		}
		$('.sort-order').find('.fa-refresh').removeClass('fa-spin');
	}).fail(function(xhr, status, error) {
		$('.sort-order').find('.fa-refresh').removeClass('fa-spin');
	});
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
			if (navigator.sendBeacon === "function") {
				navigator.sendBeacon(url, formData);
			} else {
				//User has probably disabled sendBeacon, use alternative
				$.ajax({
					url: url,
					data: formData,
					processData: false,
					contentType: false,
					type: 'POST'
				});
			}
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
	var elements = $('.player-popup');
	if (elements.length>0) {
		if (currentSourceData.reader_type=='strip') {
			var parentBottom = $('.strip-images')[0].scrollTop+$('.strip-images')[0].offsetHeight;
			var firstPage, lastPage;
			$(".manga-page").each(function() {
				if (this.getBoundingClientRect().top >= 0 && this.getBoundingClientRect().top<=$('.strip-images')[0].offsetHeight) {
					firstPage = $(this).attr('data-page-number');
				}
			});
			$(".manga-page").each(function() {
				if (this.getBoundingClientRect().top <= $('.strip-images')[0].offsetHeight) {
					lastPage = $(this).attr('data-page-number');
				}
			});
			if ($('.strip-images')[0].scrollTop==0) {
				return 1;
			} else {
				return lastPage;
			}
		} else {
			return elements[0].swiper.activeIndex+1;
		}
	} else {
		return 0;
	}
}

function setReaderCurrentPage(page) {
	page = parseInt(page);
	//console.log("setReaderCurrentPage "+page);
	//This accepts:
	// - Strip readers: value from 0 to 100000, representing a relative value from top to bottom
	// - Other readers: value from 0 to currentSourceData.length-1, representing a page (must be +1'd)
	var elements = $('.player-popup');
	if (elements.length>0) {
		if (currentSourceData.reader_type=='strip') {
			$('.strip-images')[0].scrollTo(0, ($('.strip-images')[0].scrollHeight-$('.strip-images')[0].offsetHeight)*(page/100000));
		} else {
			elements[0].swiper.slideTo(page-1);
		}
	}
}

function setSeekCurrentPage(page, total, isFromInput) {
	if (isFromInput) {		
		reportUserActivity();
	}
	page = parseInt(page);
	//console.log("setSeekCurrentPage "+page);
	$('.manga-slider-bar').val(page);
	$('.manga-fake-slider-bar').width((page-1)/(total-1)*100+'%');
	if (currentSourceData.reader_type=='strip') {
		$('.vjs-current-time').text(getReaderCurrentPage());
	} else {
		$('.vjs-current-time').text(page);
	}
	pagesRead[getReaderCurrentPage()-1]=true;
}

function setSeekCurrentPageOnScroll(element) {
	//Only used for strip readers: calculate value from current scroll
	var value = element.scrollTop/(element.scrollHeight-element.offsetHeight)*100; 
	$('.manga-slider-bar').val(value*1000);
	$('.manga-fake-slider-bar').width(value+'%');
	var previousPage = $('.vjs-current-time').text();
	var currentPage = getReaderCurrentPage();
	if (currentPage!=previousPage) {
		$('.vjs-current-time').text(currentPage);
		pagesRead[getReaderCurrentPage()-1]=true;
		//Send to server
		sendCurrentFileTracking();
	}
}

function getReaderReadPages() {
	var result = 0;
	for (var i=0;i<pagesRead.length;i++) {
		if (pagesRead[i]) {
			result++;
		}
	}
	return result;
}

function sendCurrentFileTracking(){
	if (currentSourceData!=null) {
		var position = getDisplayerCurrentPosition();
		var progress = currentSourceData.initial_progress+getDisplayerCurrentProgress();
		var markAsSeenPosition = getDisplayerMarkAsSeenPosition();
		if ((position>=markAsSeenPosition || hasBeenCasted) && !currentSourceData.is_seen) {
			markAsSeen(currentSourceData.file_id, true);
			currentSourceData.is_seen=true;
		}
		//Update bar
		$('[data-file-id="'+currentSourceData.file_id+'"] .progress').attr('style', 'width: '+(position/currentSourceData.length)*100+'%;');;
		var formData = new FormData();
		//formData.append("log", loggedMessages);
		formData.append("view_id", currentSourceData.view_id);
		formData.append("is_casted", hasBeenCasted ? 1 : 0);
		formData.append("position", position);
		formData.append("progress", progress);
		var url = getBaseUrl()+'/report_file_status.php';
		if (!enableDebug) {
			if (navigator.sendBeacon === "function") {
				navigator.sendBeacon(url, formData);
			} else {				
				//User has probably disabled sendBeacon, use alternative
				$.ajax({
					url: url,
					data: formData,
					processData: false,
					contentType: false,
					type: 'POST'
				});
			}
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
		error+="Unknown error";
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

function imageLoaded(image) {
	var element = $(image);
	element.css({"width": "auto", "height": "auto"});
	element.parent().find('.image-loading').addClass('hidden');
	element.parent().find('.image-error').addClass('hidden');
}

function stripImageLoaded(reqNo) {
	if (currentSourceData!=null && reqNo==stripImagesLoadedReqNo) {
		stripImagesLoaded++;
		$('.strip-images-loading-progress').text(stripImagesLoaded+' / '+currentSourceData.length);
		if (stripImagesLoaded==currentSourceData.length) {
			stripSyncAndShowImages();
		}
	}
}

function stripImageError(reqNo) {
	if (currentSourceData!=null && reqNo==stripImagesLoadedReqNo) {
		$('.strip-images-loading').addClass('hidden');
		$('.strip-images-error').removeClass('hidden');
		$('.strip-images').addClass('hidden');
	}
}

function stripImagesReload() {
	$('.strip-images-loading').removeClass('hidden');
	$('.strip-images-error').addClass('hidden');
	$('.strip-images').addClass('hidden');
	initializeReader('strip');
}

function stripSyncAndShowImages() {
	$('.strip-images-loading').addClass('hidden');
	$('.strip-images-error').addClass('hidden');
	$('.strip-images').removeClass('hidden');
	$('.swiper-pagination').removeClass('hidden');
	if (currentSourceData.initial_position>0 && currentSourceData.initial_position<currentSourceData.length) {
		$('[data-page-number="'+currentSourceData.initial_position+'"]')[0].scrollIntoView({ behavior: "instant", block: "start", inline: "nearest" });
	}
	toggleMangaMusic();
}

function imageError(image) {
	var element = $(image);
	element.css({"width": "0", "height": "0"});
	element.parent().find('.image-loading').addClass('hidden');
	element.parent().find('.image-error').removeClass('hidden');
}

function imageReload(button) {
	var element = $(button);
	var image = element.parent().parent().find('img')[0];
	$(image).css({"width": "0", "height": "0"});
	$(image).parent().find('.image-loading').removeClass('hidden');
	$(image).parent().find('.image-error').addClass('hidden');
	image.src=image.src;
}

function mangaHasMusic() {
	return currentSourceData.music!=null;
}

function toggleMangaMusic() {
	if (mangaHasMusic()) {
		if ($('#manga-music')[0].paused) {
			$('#manga-music')[0].play().catch(error => {
				console.log("Autoplay for bg music blocked, setting to muted");
				$('.vjs-mute-control').removeClass('vjs-vol-3');
				$('.vjs-mute-control').addClass('vjs-vol-0');
			});
			$('.vjs-mute-control').removeClass('vjs-vol-0');
			$('.vjs-mute-control').addClass('vjs-vol-3');
		} else {
			$('#manga-music')[0].pause();
			$('.vjs-mute-control').removeClass('vjs-vol-3');
			$('.vjs-mute-control').addClass('vjs-vol-0');
		}
	}
}

function requestMangaReaderFullscreen() {
	if (document.fullscreenElement==$('.main-container')[0]) {
		document.exitFullscreen();
	} else {
		$('.main-container')[0].requestFullscreen();
	}
}

function handleMangaReaderFullscreen(e) {
	if (document.fullscreenElement==$('.main-container')[0]) {
		$('.manga-bar').addClass('vjs-fullscreen');
	} else {
		$('.manga-bar').removeClass('vjs-fullscreen');
	}
}

function applyMangaReaderType(type) {
	currentSourceData.user_reader_preference = type;
	//Save to server
	if ($('body.user-logged-in').length==0) {
		//Set cookie preference
		Cookies.set('manga_reader_type', type, cookieOptions);
	} else {
		//Update on server
		var values = {
			'manga_reader_type': type,
			'only_manga_reader_type' : 1
		};
		$.post({
			url: USERS_URL+"/do_save_settings.php",
			data: values,
			xhrFields: {
				withCredentials: true
			},
		});
	}

	//Process possible changes
	var newReaderType;
	if (type==0) {
		newReaderType=currentSourceData.default_reader_type;
	} else if (type==1) {
		newReaderType='rtl';
	} else if (type==2) {
		newReaderType='ltr';
	} else if (type==3) {
		newReaderType='strip';
	}

	if (newReaderType!=currentSourceData.reader_type) {
		sendCurrentFileTracking(); //sync with server
		//Update this like if they came from the server
		currentSourceData.initial_position = getDisplayerCurrentPosition();
		currentSourceData.initial_progress = currentSourceData.initial_progress+getDisplayerCurrentProgress();
		currentSourceData.reader_type=newReaderType;
		stopListeningForUserActivityInMangaReader();

		initializeReader(newReaderType);
	}
}

function showMangaReaderConfig() {
	var disabled = (currentSourceData.default_reader_type=='strip');
	showCustomDialog(lang('js.catalogue.reader.header'), '<div class="reader-settings-data-element"><div class="reader-settings-data-header"><div class="reader-settings-data-header-title">'+lang('js.catalogue.reader.select')+'</div><div class="reader-settings-data-header-subtitle">'+(disabled ? lang('js.catalogue.reader.always_vertical') : lang('js.catalogue.reader.explanation'))+'</div></div><select id="reader-type" class="settings-combo"'+(disabled ? ' disabled' : '')+'><option value="0"'+(currentSourceData.user_reader_preference==0 ? ' selected' : '')+'>'+lang('js.catalogue.reader.recommended_option')+'</option><option value="1"'+(currentSourceData.user_reader_preference==1 ? ' selected' : '')+'>'+lang('js.catalogue.reader.eastern_style')+'</option><option value="2"'+(currentSourceData.user_reader_preference==2 ? ' selected' : '')+'>'+lang('js.catalogue.reader.western_style')+'</option><option value="3"'+(disabled || currentSourceData.user_reader_preference==3 ? ' selected' : '')+'>'+lang('js.catalogue.reader.long_strip')+'</option></select></div><br><hr><br>'+lang('js.catalogue.reader.tachiyomi_help').replaceAll("%s", SITE_NAME), null, true, true, [
		{
			text: lang('js.dialog.ok'),
			class: 'normal-button',
			onclick: function(){
				if (!$('#reader-type').prop('disabled')) {
					applyMangaReaderType($('#reader-type').val());
				}
				closeCustomDialog();
			}
		},
		{
			text: lang('js.dialog.cancel'),
			class: 'cancel-button',
			onclick: function(){
				closeCustomDialog();
			}
		}
	], false, true);
}

function reportUserActivity() {
	isUserActive = true;
}

function toggleUserActivity() {
	//console.log('toggleUserActivity');
	if ($('.player_extra_upper').hasClass('vjs-user-inactive')) {
		reportUserActivity();
	} else {
		isUserActive = false;
		$('.player_extra_upper').addClass('vjs-user-inactive');
	}
}

function listenForUserActivityInMangaReader() {
	//Function stolen from videojs: player.js / listenForUserActivity_

	const handleMouseMove = function(e) {
		// #1068 - Prevent mousemove spamming
		// Chrome Bug: https://code.google.com/p/chromium/issues/detail?id=366970
		if (e.screenX !== lastMoveX || e.screenY !== lastMoveY) {
			lastMoveX = e.screenX;
			lastMoveY = e.screenY;
			reportUserActivity();
		}
	};

	const handleMouseDown = function() {
		reportUserActivity();
		// For as long as the they are touching the device or have their mouse down,
		// we consider them active even if they're not moving their finger or mouse.
		// So we want to continue to update that they are active
		clearInterval(mouseInProgress);
		// Setting userActivity=true now and setting the interval to the same time
		// as the activityCheck interval (250) should ensure we never miss the
		// next activityCheck
		mouseInProgress = setInterval(reportUserActivity, 250);
	};

	const handleMouseUpAndMouseLeave = function(event) {
		reportUserActivity();
		// Stop the interval that maintains activity if the mouse/touch is down
		clearInterval(mouseInProgress);
	};

	// Any mouse movement will be considered user activity
	//$('#overlay-content').on('mousedown', handleMouseDown);
	$('#overlay-content').on('mousemove', handleMouseMove);
	//$('#overlay-content').on('mouseup', handleMouseUpAndMouseLeave);
	$('#overlay-content').on('mouseleave', handleMouseUpAndMouseLeave);
	$('#overlay-content .swiper-wrapper').on('click', toggleUserActivity);

/*	const controlBar = this.getChild('controlBar');

	// Fixes bug on Android & iOS where when tapping progressBar (when control bar is displayed)
	// controlBar would no longer be hidden by default timeout.
	if (controlBar && !browser.IS_IOS && !browser.IS_ANDROID) {
		controlBar.on('mouseenter', function(event) {
			if (this.player().options_.inactivityTimeout !== 0) {
				this.player().cache_.inactivityTimeout = this.player().options_.inactivityTimeout;
			}
			this.player().options_.inactivityTimeout = 0;
		});

		controlBar.on('mouseleave', function(event) {
			this.player().options_.inactivityTimeout = this.player().cache_.inactivityTimeout;
		});
	}*/

	// Listen for keyboard navigation
	// Shouldn't need to use inProgress interval because of key repeat
	//$('#overlay-content').on('keydown', reportUserActivity);
	//$('#overlay-content').on('keyup', reportUserActivity);

	// Run an interval every 250 milliseconds instead of stuffing everything into
	// the mousemove/touchmove function itself, to prevent performance degradation.
	// `this.reportUserActivity` simply sets this.userActivity_ to true, which
	// then gets picked up by this loop
	// http://ejohn.org/blog/learning-from-twitter/

	/** @this Player */
	const activityCheck = function() {
		// Check to see if mouse/touch activity has happened
		if (!isUserActive) {
			return;
		}

		// Reset the activity tracker
		isUserActive = false;

		// If the user state was inactive, set the state to active
		$('.player_extra_upper').removeClass('vjs-user-inactive');

		// Clear any existing inactivity timeout to start the timer over
		clearTimeout(inactivityTimeout);

		// In <timeout> milliseconds, if no more activity has occurred the
		// user will be considered inactive
		inactivityTimeout = setTimeout(function() {
			// Protect against the case where the inactivityTimeout can trigger just
			// before the next user activity is picked up by the activity check loop
			// causing a flicker
			if (!isUserActive) {
				$('.player_extra_upper').addClass('vjs-user-inactive');
			}
		}, 2000);

	};

	activityCheckInterval = setInterval(activityCheck, 250);
}

function stopListeningForUserActivityInMangaReader() {
	clearInterval(activityCheckInterval);
	clearInterval(mouseInProgress);
	clearTimeout(inactivityTimeout);
}

function buildMangaReaderBar(current, total, type) {
	var c = '<div class="manga-bar video-js vjs-has-started vjs-playing vjs-default-skin vjs-big-play-centered vjs-controls-enabled vjs-workinghover vjs-v8 vjs-has-started player-dimensions'+(document.fullscreenElement==$('.main-container')[0] ? ' vjs-fullscreen' : '')+'">';
	c += '		<div class="vjs-control-bar" dir="ltr">';
	if (type=='strip') {
		c += '			<div class="vjs-progress-control vjs-control"><div tabindex="0" class="vjs-progress-holder vjs-slider vjs-slider-horizontal" role="slider"><div class="vjs-play-progress vjs-slider-bar manga-fake-slider-bar" aria-hidden="true" style="width: 0%;"></div><input type="range" class="vjs-play-progress vjs-slider-bar manga-slider-bar" aria-hidden="true" min="0" max="100000" value="0" oninput="setReaderCurrentPage(this.value);setSeekCurrentPage(this.value, 100000, true);"></input></div></div>';
	} else {
		c += '			<div class="vjs-progress-control vjs-control"><div tabindex="0" class="vjs-progress-holder vjs-slider vjs-slider-horizontal" role="slider"><div class="vjs-play-progress vjs-slider-bar manga-fake-slider-bar" aria-hidden="true" style="width: '+(parseFloat((current-1)/(total-1)*100))+'%;"></div><input type="range" class="vjs-play-progress vjs-slider-bar manga-slider-bar" aria-hidden="true" min="1" max="'+total+'" value="'+current+'" oninput="setSeekCurrentPage(this.value, '+total+', true);setReaderCurrentPage(this.value);"></input></div></div>';
	}
	if (!isEmbedPage()) {
		if (type=='rtl') {
			if (hasNextFile()) {
				c += '		<button class="vjs-control vjs-button vjs-prev-button" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.next_chapter')+'" onclick="playNextFile();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.next_chapter')+'</span></button>';
			} else {
				c += '		<button class="vjs-control vjs-button vjs-prev-button vjs-button-disabled" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.no_next_chapter')+'"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.no_next_chapter')+'</span></button>';
			}
			if (hasPrevFile()) {
				c += '		<button class="vjs-control vjs-button vjs-next-button" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.prev_chapter')+'" onclick="playPrevFile();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.prev_chapter')+'</span></button>';
			} else {
				c += '		<button class="vjs-control vjs-button vjs-next-button vjs-button-disabled" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.no_prev_chapter')+'"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.no_prev_chapter')+'</span></button>';
			}
		} else {
			if (hasPrevFile()) {
				c += '		<button class="vjs-control vjs-button vjs-prev-button" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.prev_chapter')+'" onclick="playPrevFile();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.prev_chapter')+'</span></button>';
			} else {
				c += '		<button class="vjs-control vjs-button vjs-prev-button vjs-button-disabled" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.no_prev_chapter')+'"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.no_prev_chapter')+'</span></button>';
			}
			if (hasNextFile()) {
				c += '		<button class="vjs-control vjs-button vjs-next-button" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.next_chapter')+'" onclick="playNextFile();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.next_chapter')+'</span></button>';
			} else {
				c += '		<button class="vjs-control vjs-button vjs-next-button vjs-button-disabled" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.no_next_chapter')+'"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.no_next_chapter')+'</span></button>';
			}
		}
	}
	c += '			<div class="vjs-current-time vjs-time-control vjs-control">'+current+'</div>';
	c += '			<div class="vjs-time-control vjs-time-divider"><div><span>/</span></div></div>';
	c += '			<div class="vjs-duration vjs-time-control vjs-control">'+total+'</div>';
	if (mangaHasMusic()) {
		c += '		<button class="vjs-mute-control vjs-control vjs-button vjs-vol-0" type="button" title="'+lang('js.catalogue.reader.commute_music')+'" aria-disabled="false" onclick="toggleMangaMusic();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.commute_music')+'</span></button><audio id="manga-music" loop><source src="'+currentSourceData.music+'" type="audio/mpeg"></audio>';
	}
	c += '			<button class="vjs-config-button vjs-control vjs-button" type="button" aria-disabled="false" title="'+lang('js.catalogue.reader.options')+'" onclick="showMangaReaderConfig();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.options')+'</span></button>';
	c += '			<button class="vjs-fullscreen-control vjs-control vjs-button" type="button" title="'+lang('js.catalogue.reader.full_screen')+'" aria-disabled="false" onclick="requestMangaReaderFullscreen();"><span class="vjs-icon-placeholder" aria-hidden="true"></span><span class="vjs-control-text" aria-live="polite">'+lang('js.catalogue.reader.full_screen')+'</span></button>';
	c += '		</div>';
	c += '	</div>';

	return c;
}

function initializeReader(type) {
	var pagesCode = '';
	pagesRead = new Array(currentSourceData.length);
	if (type=='strip') {
		stripImagesLoadedReqNo++;
		stripImagesLoaded = 0;
		pagesCode+='<div class="swiper-wrapper"><div class="strip-images-loading"><i class="fa-3x fas fa-circle-notch fa-spin"></i><span class="strip-images-loading-progress">0 / '+currentSourceData.length+'</span></div><div class="strip-images-error hidden">'+lang('js.catalogue.reader.error_loading_all_images')+'<br><button class="normal-button" onclick="stripImagesReload();">'+lang('js.catalogue.reader.retry')+'</button></div><div class="strip-images hidden" onscroll="setSeekCurrentPageOnScroll(this);">';
	} else {
		pagesCode+='<div class="swiper-wrapper">';
	}
	var initialPosition = 1;
	if (currentSourceData.initial_position>0 && currentSourceData.initial_position<currentSourceData.length) {
		initialPosition = currentSourceData.initial_position;
	}
	pagesRead[initialPosition-1]=true;
	for (var i=0; i<currentSourceData.length;i++) {
		if (type=='strip') {
			pagesCode+='<div class="manga-page" data-page-number="'+(i+1)+'"><img src="'+currentSourceData.pages[i]+'" draggable="false" onload="stripImageLoaded('+stripImagesLoadedReqNo+');" onerror="stripImageError('+stripImagesLoadedReqNo+');"></div>';
		} else {
			var isLazy = !(i==initialPosition-1 || i==initialPosition-2 || i==initialPosition);
			pagesCode+='<div class="manga-page swiper-slide"><img src="'+currentSourceData.pages[i]+'" style="width: 0; height: 0;" loading="'+(isLazy ? 'lazy' : '')+'" draggable="false" onload="imageLoaded(this);" onerror="imageError(this);"><div class="image-loading"><i class="fa-3x fas fa-circle-notch fa-spin"></i></div><div class="image-error hidden">'+lang('js.catalogue.reader.error_loading_one_image')+'<br><button class="normal-button" onclick="imageReload(this);">'+lang('js.catalogue.reader.retry')+'</button></div></div>';
		}
		pagesRead[i]=false;
	}
	if (type=='strip') {
		pagesCode+='</div></div>';
	} else {
		pagesCode+='</div>';
	}
	$('.player_extra_upper').removeClass('vjs-user-inactive');
	$('.player_extra_title').html(currentSourceData.title);
	$('#overlay-content .manga-reader').remove();
	$('<div class="player-popup swiper manga-reader manga-reader-'+type+'" dir="'+(type=='rtl' ? 'rtl' : 'ltr')+'">'+pagesCode+'<div class="swiper-pagination swiper-pagination-custom swiper-pagination-horizontal'+(type=='strip' ? ' hidden' : '')+'">'+buildMangaReaderBar(initialPosition,currentSourceData.length, type)+'</div><div class="swiper-button-prev"></div><div class="swiper-button-next"></div></div>').appendTo('#overlay-content');
	$('.main-container').on('fullscreenchange', (e) => handleMangaReaderFullscreen(e));
	
	if (type!='strip') {
		new Swiper('.player-popup', {
			initialSlide: initialPosition-1,
			slidesPerView: type=='strip' ? 'auto' : 1,
			direction: type=='strip' ? 'vertical' : 'horizontal',
			freeMode: type=='strip' ? {enabled: true, minimumVelocity: 0, momentum: true} : false,
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
			pagination: false,
			navigation: {
				nextEl: '.swiper-button-next',
				prevEl: '.swiper-button-prev',
			},
			on: {
				slideChange: function (swiper) {
					//console.log("slideChange "+(swiper.activeIndex+1));
					setSeekCurrentPage(swiper.activeIndex+1, currentSourceData.length, false);
					pagesRead[swiper.activeIndex]=true;
					//Send to server
					sendCurrentFileTracking();
					setTimeout(function(){
						$('.manga-page.swiper-slide-active img').attr('loading','');
						$('.manga-page.swiper-slide-prev img').attr('loading','');
						$('.manga-page.swiper-slide-next img').attr('loading','');
					}, 1);
				},
			},
		});
		toggleMangaMusic();
	}
	listenForUserActivityInMangaReader();
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
					"pictureInPictureToggle",
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
			console.log('Canplay');
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
			console.log('Ready');
			if (player.techName_=='Html5') {
				setTimeout(function(){
					if (player) {
						player.play().catch(error => {
							console.log("Autoplay blocked, setting has-started manually");
							if (player) {
								player.addClass('vjs-has-started');
							}
						});
					}
				}, 1);
			}

			//Install double click on sides to FF/RW on touch devices
			if (player.el_.classList.contains("vjs-touch-enabled")) {
				document.querySelector("#player").addEventListener("touchstart", function (e) {
					if (lastDoubleClickStart == 0) {
						lastDoubleClickStart = new Date().getTime();
					} else {
						if (((new Date().getTime()) - lastDoubleClickStart) < 500) {
							lastDoubleClickStart = 0;
							const playerWidth = document.querySelector("#player").getBoundingClientRect().width;
							if (0.66 * playerWidth < e.touches[0].pageX) {
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
							} else if (e.touches[0].pageX < 0.33 * playerWidth) {
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
			$('#overlay-content > .player_extra_upper').addClass('hidden');
			installVideoPlayerTopBarAndNextFile();
		});
		player.on('loadstart', function(){
			console.log('Loadstart');
		});
		
		player.on('playing', function(){
			player.playbackRate(window.currentPlayRate);
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
		installVideoPlayerTopBarAndNextFile();
	}

	//Sync prev/next buttons
	player.controlBar.removeChild('PrevButton');
	player.controlBar.removeChild('NextButton');
	player.controlBar.removeChild('PrevButtonDisabled');
	player.controlBar.removeChild('NextButtonDisabled');
	player.controlBar.removeChild('PlaySpeedButton');
	if (!isEmbedPage()) {
		player.controlBar.addChild(hasPrevFile() ? "PrevButton" : "PrevButtonDisabled", {}, 2);
		player.controlBar.addChild(hasNextFile() ? "NextButton" : "NextButtonDisabled", {}, 3);
	}
	player.controlBar.addChild('PlaySpeedButton', {}, 8);

	//We only support one source for now
	if (currentSourceData.method=='mega') {
		loadMegaStream(sourceUrl);
	} else {
		player.play();
	}
}

function installVideoPlayerTopBarAndNextFile() {
	//Install the top, movement and ended bar
	if ($('.video-js .player_extra_upper').length==0) {
		$('<div class="player_extra_upper"><div class="player_extra_title">'+new Option(currentSourceData.title).innerHTML+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-times vjs-button" title="'+lang('js.dialog.close')+'" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo(".video-js");
		$('<div class="player_extra_movement"><div class="player_extra_backward"><i class="fas fa-backward"></i><span><span class="player_extra_backward_time">0</span> s</span></div><div class="player_extra_forward"><i class="fas fa-forward"></i><span><span class="player_extra_forward_time">0</span> s</span></div></div>').appendTo(".video-js");
	} else {
		$('.player_extra_title').html(currentSourceData.title);
		$('.player_extra_backward_time').html('0');
		$('.player_extra_forward_time').html('0');
	}
	var nextFile = getNextFileElement();
	if ($('.video-js .player_extra_ended').length==0) {
		$('<div class="player_extra_ended'+(nextFile==null ? ' hidden' : '')+'"><div class="player_extra_ended_episode"><div class="player_extra_ended_header">'+lang('js.catalogue.player.next_chapter_loading')+'</div><div class="player_extra_ended_title">'+new Option(nextFile==null ? '' : nextFile.attr('data-title-short')).innerHTML+'</div><div class="player_extra_ended_thumbnail"><a onclick="playNextFile();">'+lang('js.catalogue.player.next_chapter_play_now')+'</a><img src="'+(nextFile==null ? '' : nextFile.attr('data-thumbnail'))+'" alt=""><div class="player_extra_ended_timer"></div></div></div>').appendTo(".video-js");
	} else  if (nextFile!=null) {
		$('.player_extra_ended').removeClass('hidden');
		$('.player_extra_ended_title')[0].innerHTML=new Option(getNextFileElement().attr('data-title-short')).innerHTML;
		$('.player_extra_ended_thumbnail img')[0].src=getNextFileElement().attr('data-thumbnail');
	} else {
		$('.player_extra_ended').addClass('hidden');
		$('.player_extra_ended_title')[0].innerHTML=new Option('').innerHTML;
		$('.player_extra_ended_thumbnail img')[0].src='';
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
	var isManga = $('#catalogue_type').length>0 && $('#catalogue_type').val()=='manga';
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
			title = lang('js.catalogue.player.error.generic.title');
			message = lang('js.catalogue.player.error.generic.description');
			reportErrorToServer('mega-unknown', error);
			break;
		case /ENOENT \(\-9\)/.test(error):
			retryFullProcess = true;
			title = lang('js.catalogue.player.error.mega.unavailable.title');
			message = lang('js.catalogue.player.error.mega.unavailable.description');
			reportErrorToServer('mega-unavailable', error);
			break;
		case /EOVERQUOTA \(\-17\)/.test(error):
		case /Bandwidth limit reached/.test(error):
			retryFullProcess = true;
			title = lang('js.catalogue.player.error.mega.limit.title');
			message = lang('js.catalogue.player.error.mega.limit.description');
			reportErrorToServer('mega-quota-exceeded', error);
			break;
		case /E_MEGA_LOAD_ERROR/.test(error):
			retryFullProcess = true;
			if (/web browser lacks/.test(error) || /Streamer is not defined/.test(error)) {
				title = lang('js.catalogue.player.error.mega.incompatible.title');
				message = lang('js.catalogue.player.error.mega.incompatible.description');
				reportErrorToServer('mega-incompatible-browser', error);
			} else if (/NetworkError/.test(error)){
				title = lang('js.catalogue.player.error.connection.title');
				message = lang('js.catalogue.player.error.connection.description');
				reportErrorToServer('mega-connection-error', error);
			} else {
				title = lang('js.catalogue.player.error.mega.load.title');
				message = lang('js.catalogue.player.error.mega.load.description');
				reportErrorToServer('mega-load-failed', error);
			}
			break;
		case /PLAYER_ERROR/.test(error):
			switch (true) {
				case /NETWORK_ERROR/.test(error):
					title = lang('js.catalogue.player.error.connection.title');
					message = lang('js.catalogue.player.error.connection.description');
					break;
				case /DECODER_ERROR/.test(error):
					title = lang('js.catalogue.player.error.decode.title');
					message = lang('js.catalogue.player.error.decode.description');
					break;
				case /NOT_SUPPORTED/.test(error):
					title = lang('js.catalogue.player.error.not_supported.title');
					message = lang('js.catalogue.player.error.not_supported.description');
					break;
				case /ABORTED_BY_USER/.test(error):
				default:
					title = lang('js.catalogue.player.error.generic.title');
					message = lang('js.catalogue.player.error.generic.description');
			}
			retryFullProcess = /E_MEGA_PLAYER_ERROR/.test(error);
			reportErrorToServer(/E_MEGA_PLAYER_ERROR/.test(error) ? 'mega-player-failed' : 'direct-player-failed', error);
			break;
		case /FAILED_TO_LOAD_ERROR/.test(error):
			retryFullProcess = true;
			title = lang('js.catalogue.player.error.load.title');
			message = lang('js.catalogue.player.error.load.description');
			//reportErrorToServer('file-load-failed', error);
			break;
		default:
			retryFullProcess = true;
			title = lang('js.catalogue.player.error.generic.title');
			message = lang('js.catalogue.player.error.generic.description');
			reportErrorToServer('unknown', error);
			break;
	}
	lastErrorTimestamp = player ? player.currentTime() : 0;
	var start = '<div class="player-error">';
	var buttons = (critical ? '<div class="player_error_buttons"><button class="normal-button" onclick="closeOverlay();">'+lang('js.dialog.close')+'</button></div>' : '<div class="player_error_buttons"><button class="normal-button" onclick="reinitializeFile('+retryFullProcess+');">'+lang('js.catalogue.reader.retry')+'</button></div>');
	var end='</div>';
	//Remove previous errors
	$('.player-error').remove();
	shutdownFileDisplayer(false);
	$('#overlay-content > .player_extra_upper').removeClass('hidden');
	$(start + '<div class="player_error_title"><span class=\"fa fa-exclamation-circle player_error_icon\"></span><br>' + title + '</div><div class="player_error_details">' + message + '</div>' + buttons + '<br><details class="player-error-technical-details"><summary style="cursor: pointer;"><strong><u>'+lang('js.catalogue.player.error.details')+'</u></strong></summary>' + new Option(error).innerHTML + '<br>VID: ' + (currentSourceData!=null ? currentSourceData.view_id : '(null)') + ' / FID: ' + (currentSourceData!=null ? currentSourceData.file_id : '(null)') + ' / TSP: ' + lastErrorTimestamp + '</details>' + end).appendTo('#overlay-content');
}

function loadMegaStream(url){
	//Workaround for CORS issue on Firefox:
	window.mega.API.getGlobalApi().userAgent=null;

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
	isDisplayerClosed = true;
	stopListeningForUserActivityInMangaReader();
	if (document.fullscreenElement==$('.main-container')[0]) {
		document.exitFullscreen();
	}
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
	var isSpecial = $('.file-launcher[data-file-id="'+fileId+'"]').first().attr('data-is-special')=='true';
	if (isSpecial) {
		return $([]);
	}
	var position = parseInt($('.file-launcher[data-file-id="'+fileId+'"]').first().attr('data-position'));
	return $('.file-launcher[data-file-id="'+fileId+'"]').closest('.version-content').find('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) < position && $(this).find('.episode-info-seen-cell input[type="checkbox"]:checked').length==0 && $(this).attr('data-is-special')!='true';
	});
}

function getNextReadEpisodes(fileId) {
	var isSpecial = $('.file-launcher[data-file-id="'+fileId+'"]').first().attr('data-is-special')=='true';
	if (isSpecial) {
		return $([]);
	}
	var position = parseInt($('.file-launcher[data-file-id="'+fileId+'"]').first().attr('data-position'));
	return $('.file-launcher[data-file-id="'+fileId+'"]').closest('.version-content').find('.file-launcher').filter(function(){
		return parseInt($(this).attr('data-position')) > position && $(this).find('.episode-info-seen-cell input[type="checkbox"]:checked').length==1 && $(this).attr('data-is-special')!='true';
	});
}

function setSeenBehaviorInServer(value) {
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
			var nextReadEpisodes = getNextReadEpisodes(fileId);
			if (nextReadEpisodes.length>0) {
				var text;
				if (nextReadEpisodes.length==1) {
					text = lang('js.catalogue.remove_seen.explanation_one');
				} else {
					text = lang('js.catalogue.remove_seen.explanation_many').replaceAll('%d', nextReadEpisodes.length);
				}
				showCustomDialog(lang('js.catalogue.remove_seen.title'), text, null, false, true, [
					{
						text: lang('js.dialog.yes'),
						class: 'normal-button',
						onclick: function(){
							//Remove from seen
							var nextReadEpisodeIds = nextReadEpisodes.get().map(a => $(a).attr('data-file-id'));
							isCheckingAsSeenProgrammatically = true;
							$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', false);
							$('.file-launcher[data-file-id="'+fileId+'"]').find('.progress').attr('style', 'width: 0%;');
							for (var i=0;i<nextReadEpisodeIds.length;i++) {
								$('.file-launcher[data-file-id="'+nextReadEpisodeIds[i]+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', false);
								$('.file-launcher[data-file-id="'+nextReadEpisodeIds[i]+'"]').find('.progress').attr('style', 'width: 0%;');
							}
							isCheckingAsSeenProgrammatically = false;
							executeMarkAsSeen(nextReadEpisodeIds.concat([fileId]), false);
							closeCustomDialog();
						}
					},
					{
						text: lang('js.dialog.no'),
						class: 'normal-button',
						onclick: function(){
							//Remove from seen
							executeMarkAsSeen([fileId], false);
							$('.file-launcher[data-file-id="'+fileId+'"]').find('.progress').attr('style', 'width: 0%;');
							closeCustomDialog();
						}
					}
				]);
			} else {
				//Remove from seen
				executeMarkAsSeen([fileId], false);
				$('.file-launcher[data-file-id="'+fileId+'"]').find('.progress').attr('style', 'width: 0%;');
			}
		} else {
			//Add to seen (and ask for previous if applicable)
			markAsSeen(fileId, false);
		}
	}
}

function markAsSeen(fileId, dontAsk) {
	var previouslyUnreadEpisodes = getPreviousUnreadEpisodes(fileId);
	if (!dontAsk && $('#seen_behavior').val()==0 && previouslyUnreadEpisodes.length>0) {
		var text;
		if (previouslyUnreadEpisodes.length==1) {
			text = lang('js.catalogue.add_seen.explanation_one');
		} else {
			text = lang('js.catalogue.add_seen.explanation_many').replaceAll('%d', previouslyUnreadEpisodes.length);
		}
		showCustomDialog(lang('js.catalogue.add_seen.title'), text, '<div id="dialog-center-checkbox"><input type="checkbox" id="seen-behavior-dont-ask"><label for="seen-behavior-dont-ask">'+lang('js.catalogue.add_seen.dont_ask')+'</label></div>', false, true, [
			{
				text: lang('js.dialog.yes'),
				class: 'normal-button',
				onclick: function(){
					$('#seen_behavior').val(1);
					markAsSeen(fileId, true);
					if ($('#seen-behavior-dont-ask').prop('checked')) {
						setSeenBehaviorInServer(1);
					} else {
						$('#seen_behavior').val(0);
					}
					closeCustomDialog();
				}
			},
			{
				text: lang('js.dialog.no'),
				class: 'normal-button',
				onclick: function(){
					$('#seen_behavior').val(2);
					markAsSeen(fileId, true);
					if ($('#seen-behavior-dont-ask').prop('checked')) {
						setSeenBehaviorInServer(2);
					} else {
						$('#seen_behavior').val(0);
					}
					closeCustomDialog();
				}
			}
		]);
	} else if ($('#seen_behavior').val()==1) {
		//1: Mark as seen INCLUDING all unread episodes previous to the current one
		var previouslyUnreadEpisodeIds = previouslyUnreadEpisodes.get().map(a => $(a).attr('data-file-id'));

		isCheckingAsSeenProgrammatically = true;
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', true);
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.progress').attr('style', 'width: 100%;');
		for (var i=0;i<previouslyUnreadEpisodeIds.length;i++) {
			$('.file-launcher[data-file-id="'+previouslyUnreadEpisodeIds[i]+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', true);
			$('.file-launcher[data-file-id="'+previouslyUnreadEpisodeIds[i]+'"]').find('.progress').attr('style', 'width: 100%;');
		}
		isCheckingAsSeenProgrammatically = false;
		executeMarkAsSeen(previouslyUnreadEpisodeIds.concat([fileId]), true);
	} else if ($('#seen_behavior').val()==-1) {
		//-1: User logged out, do nothing
		if (!dontAsk) {
			showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.mark_seen'));
		}
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', false);
	} else {
		//2 (or 0 with dontAsk): Mark only the current file
		isCheckingAsSeenProgrammatically = true;
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.episode-info-seen-cell input[type="checkbox"]').prop('checked', true);
		$('.file-launcher[data-file-id="'+fileId+'"]').find('.progress').attr('style', 'width: 100%;');
		isCheckingAsSeenProgrammatically = false;
		executeMarkAsSeen([fileId], true);
	}
}

function executeMarkAsSeen(fileIds, isSeen) {
	var formData = new FormData();
	formData.append("action", isSeen ? 'add' : 'remove');
	for (var i=0;i<fileIds.length;i++) {
		formData.append("file_id[]", fileIds[i]);
		toggleCommentsWithSpoilersForSeenFile(fileIds[i], isSeen);
	}
	var url = getBaseUrl()+'/mark_as_seen.php';
	if (!enableDebug) {
		if (navigator.sendBeacon === "function") {
			navigator.sendBeacon(url, formData);
		} else {
			//User has probably disabled sendBeacon, use alternative
			$.ajax({
				url: url,
				data: formData,
				processData: false,
				contentType: false,
				type: 'POST'
			});
		}
	} else {
		console.debug('Would have requested: '+url);
	}
}

function toggleCommentsWithSpoilersForSeenFile(fileId, isSeen) {
	var episodeId = $($('.file-launcher[data-file-id="'+fileId+'"]').get(0)).attr('data-episode-id');
	
	for (element of $('.comment[data-episode-id="'+episodeId+'"][data-version-id="'+$('.version-tab-selected').attr('data-version-id')+'"')) {
		if (isSeen) {
			$(element).find('.comment-with-spoiler .comment-spoiler-warning').addClass('hidden');
			$(element).find('.comment-with-spoiler').addClass('comment-with-spoiler-shown');
		} else {
			$(element).find('.comment-with-spoiler .comment-spoiler-warning').removeClass('hidden');
			$(element).find('.comment-with-spoiler').removeClass('comment-with-spoiler-shown');
			$(element).find('.comment-with-spoiler .spoiler-show-button').removeClass('hidden');
		}
	}

}

function bookmarkRemoved(seriesId) {
	//Just ignore it, this is a callback not used in the catalogue section
}

function toggleBookmarkFromSeriesPage(){
	if ($('body.user-logged-in').length==0) {
		showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.add_list'));
		return;
	}
	var action;
	var seriesId = $('#series_id').val();
	if ($('.remove-from-my-list').length>0)	{
		$('.remove-from-my-list').addClass('add-to-my-list').removeClass('remove-from-my-list').html('<i class="far fa-fw fa-bookmark"></i> '+lang('js.catalogue.series.add_to_my_list'));
		action='remove';
		bookmarkRemoved(seriesId);
	} else {
		$('.add-to-my-list').addClass('remove-from-my-list').removeClass('add-to-my-list').html('<i class="fas fa-fw fa-bookmark"></i> '+lang('js.catalogue.series.in_my_list'));
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

function toggleCommentSpoiler(element) {
	$(element).parent().parent().parent().addClass('comment-with-spoiler-shown');
	$(element).parent().addClass('hidden');
}

function checkCommentPossible(element) {
	if ($('body.user-logged-in').length==0) {
		showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.comment'));
		return;
	} else if ($('#show_comment_warning').val()==1) {
		showCustomDialog(lang('js.catalogue.leave_comment.title'), lang('js.catalogue.leave_comment.description'), null, true, true, [
			{
				text: lang('js.dialog.ok'),
				class: 'normal-button',
				onclick: function(){
					$('#show_comment_warning').val(0);
					closeCustomDialog();
					element.focus();
				}
			}
		]);
	}
}

function sendUserComment(button) {
	button.prop('disabled', true);
	button.html('<i class="fas fa-fw fa-spinner fa-spin"></i>');

	var values = {
		text: $(button.parent().find('textarea').get(0)).val(),
		version_id: $('.version-tab-selected').attr('data-version-id'),
		has_spoilers: (button.parent().find('.comment-has-spoiler').length>0 ? $(button.parent().find('.comment-has-spoiler').get(0)).is(':checked') : false)
	};

	$.post({
		url: USERS_URL+"/do_leave_comment.php",
		data: values,
		xhrFields: {
			withCredentials: true
		},
	}).done(function(data) {
		var response = JSON.parse(data);
		$(button.parent().find('textarea').get(0)).val('');
		button.parent().find('textarea').get(0).parentNode.dataset.replicatedValue=this.value;
		button.closest('.comment-fake').after('<div class="comment"><img class="comment-avatar" src="'+$('.comment-fake .comment-avatar').attr('src')+'"><div class="comment-message">'+response.text+'<div class="comment-author"><span class="comment-user">'+response.username+'</span>&nbsp;•&nbsp;<span class="comment-date">'+lang('js.date.now')+'</span>'+(response.episode_title!=null ? '&nbsp;•&nbsp;'+response.episode_title : '')+(response.has_spoilers ? '&nbsp;<span class="fa fa-warning" title="'+lang('js.catalogue.leave_comment.marked_by_user_as_spoiler')+'"></span>' : '')+'</div></div></div>');
		button.prop('disabled', false);
		button.parent().find('.comment-has-spoiler').prop('checked', false);
		spoilerCheckedAutomatically = false;
		button.html('<i class="fa fa-fw fa-paper-plane"></i>');
	}).fail(function(data) {
		try {
			var response = JSON.parse(data.responseText);
			if (response.code==3) {
				showAlert(lang('js.catalogue.leave_comment.error.title'), lang('js.catalogue.leave_comment.error.too_soon'));
			} else {
				showAlert(lang('js.catalogue.leave_comment.error.title'), lang('js.catalogue.leave_comment.error.generic'));
			}
		} catch(e) {
			showAlert(lang('js.catalogue.leave_comment.error.title'), lang('js.catalogue.leave_comment.error.generic'));
		}
		button.prop('disabled', false);
		button.html('<i class="fa fa-fw fa-paper-plane"></i>');
	});
}

function checkForAutoSpoilers(textarea) {
	var text = textarea.value.toLocaleLowerCase();
	if (!spoilerCheckedAutomatically) {
		var words = lang('js.catalogue.leave_comment.spoiler_words').split(', ');
		for (var i=0; i<words.length; i++) {
			if (text.includes(words[i])) {
				spoilerCheckedAutomatically = true;
				$(textarea).parent().find('.comment-has-spoiler').prop('checked', true);
				return;
			}
		}
	}
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
	if ($('.swiper').length>0) {
		$('.style-type-catalogue').addClass('has-carousel');
	}
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
	for(var i=0;i<values['content_types[]'].length;i++) {
		queryString+='&content_types[]='+values['content_types[]'][i];
	}
	for(var i=0;i<values['origins[]'].length;i++) {
		queryString+='&origins[]='+values['origins[]'][i];
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
		$('.loading-message').text(lang('js.catalogue.loading_results'));
	} else {
		$('.loading-message').text(lang('js.catalogue.loading_results.search'));
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

	var contentTypes = Array();
	for (element of $('.search-content_types input:checked')) {
		contentTypes.push($(element).attr('data-id'));
	}

	var origins = Array();
	for (element of $('.search-origins input:checked')) {
		origins.push($(element).attr('data-id'));
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
		'content_types[]': contentTypes,
		'origins[]': origins,
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
			input.innerText=lang('js.catalogue.search.more_than_max_pages');
		} else {
			input.innerText=lang('js.catalogue.search.number_of_pages').replaceAll('%d', value);
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

function requestFileData(fileId) {
	lastRequestedFileId = fileId;
	hasBeenCasted = false;
	hasJumpedToInitialPosition = false
	lastTimeUpdate = 0;
	isDisplayerClosed = false;
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
		if (!isDisplayerClosed) {
			var response = JSON.parse(data);
			if (response.result=='ok') {
				currentSourceData = response.data;
				initializeFileDisplayer();
			} else {
				parsePlayerError('FAILED_TO_LOAD_ERROR');
			}
		}
	}).fail(function(data) {
		if (!isDisplayerClosed) {
			parsePlayerError('FAILED_TO_LOAD_ERROR');
		}
	});
}

function applyVersionRating(pressedButton, oppositeButton, ratingClicked) {
	if ($('body.user-logged-in').length==0) {
		showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.rate'));
		return;
	}
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
		version_id: $('.version-tab-selected').attr('data-version-id'),
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

function resizeSynopsisHeight(force) {
	var maxSynopsisHeight = $('.series-synopsis-real').length>0 ? parseFloat(1.2 * 5 * parseFloat(getComputedStyle($('.series-synopsis-real')[0]).fontSize)) : 0;
	
	if (force) {
		$(".show-more").html('<span class="fa fa-fw fa-caret-down"></span> '+lang('js.catalogue.series.show_more')+' <span class="fa fa-fw fa-caret-down"></span>');
		$('.series-synopsis-real').addClass('expandable-content-default');
		$('.series-synopsis-real').removeClass('expandable-content-hidden');
		$('.series-synopsis-real').removeClass('expandable-content-shown');
		$(".show-more").addClass('hidden');
		$(".show-more").removeClass('has-been-shown');
	}

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
			linkText = '<span class="fa fa-fw fa-caret-up"></span> '+lang('js.catalogue.series.show_less')+' <span class="fa fa-fw fa-caret-up"></span>';
			$(".series-synopsis-real").removeClass("expandable-content-hidden");
			$(".series-synopsis-real").addClass("expandable-content-shown");
		} else {
			linkText = '<span class="fa fa-fw fa-caret-down"></span> '+lang('js.catalogue.series.show_more')+' <span class="fa fa-fw fa-caret-down"></span>';
			$(".series-synopsis-real").removeClass("expandable-content-shown");
			$(".series-synopsis-real").addClass("expandable-content-hidden");
		};

		$(this).html(linkText);
	});
	resizeSynopsisHeight(false);

	const Button = videojs.getComponent('Button');

	class NextButton extends Button {
		constructor(player, options) {
			super(player, options);
			this.controlText(lang('js.catalogue.reader.next_chapter'));
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
			this.controlText(lang('js.catalogue.reader.prev_chapter'));
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
			this.controlText(lang('js.catalogue.reader.no_next_chapter'));
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-next-button vjs-button-disabled`;
		}
	}
	class PrevButtonDisabled extends Button {
		constructor(player, options) {
			super(player, options);
			this.controlText(lang('js.catalogue.reader.no_prev_chapter'));
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-prev-button vjs-button-disabled`;
		}
	}

	class PlaySpeedButton extends Button {
		constructor(player, options) {
			super(player, options);
			this.applyValue();
		}
		handleClick() {
			if (window.currentPlayRate==1) {
				window.currentPlayRate=1.25;
			} else if (window.currentPlayRate==1.25) {
				window.currentPlayRate=1.5;
			} else if (window.currentPlayRate==1.5) {
				window.currentPlayRate=0.5;
			} else if (window.currentPlayRate==0.5) {
				window.currentPlayRate=0.75;
			} else {
				window.currentPlayRate=1;
			}
			this.applyValue();
		}
		applyValue() {
			this.player().playbackRate(window.currentPlayRate);
			this.removeClass('fsc-play-speed-ultrafast');
			this.removeClass('fsc-play-speed-fast');
			this.removeClass('fsc-play-speed-normal');
			this.removeClass('fsc-play-speed-slow');
			this.removeClass('fsc-play-speed-ultraslow');
			if (window.currentPlayRate==1) {
				this.addClass('fsc-play-speed-normal');
				this.controlText(lang('js.player.speed.normal'));
			} else if (window.currentPlayRate==1.25) {
				this.addClass('fsc-play-speed-fast');
				this.controlText(lang('js.player.speed.1.25x'));
			} else if (window.currentPlayRate==1.5) {
				this.addClass('fsc-play-speed-ultrafast');
				this.controlText(lang('js.player.speed.1.5x'));
			} else if (window.currentPlayRate==0.5) {
				this.addClass('fsc-play-speed-ultraslow');
				this.controlText(lang('js.player.speed.0.5x'));
			} else {
				this.addClass('fsc-play-speed-slow');
				this.controlText(lang('js.player.speed.0.75x'));
			}
		}
		buildCSSClass() {
			return `${super.buildCSSClass()} vjs-play-speed-button`;
		}
	}

	videojs.registerComponent('NextButton', NextButton);
	videojs.registerComponent('NextButtonDisabled', NextButtonDisabled);
	videojs.registerComponent('PrevButton', PrevButton);
	videojs.registerComponent('PrevButtonDisabled', PrevButtonDisabled);
	videojs.registerComponent('PlaySpeedButton', PlaySpeedButton);

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
			//Select season - this will only be effective when invoking play next/prev
			if ($(this).closest('.division-container').length>0) {
				var seasonId = $(this).closest('.division-container')[0].id.split('-').pop();
				$(this).closest('.section-content').find('.season-chooser').val(seasonId).trigger('change');
			}
			$('html').addClass('page-no-overflow');
			$('#overlay').removeClass('hidden');
			
			if ($('#overlay-content > .player_extra_upper').length==0) {
				$('<div class="player_extra_upper"><div class="player_extra_title">'+$(this).attr('data-title')+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-times vjs-button" title="'+lang('js.dialog.close')+'" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo("#overlay-content");
			} else {
				if ($('.video-js .player_extra_upper').length==0) {
					//Only if player is not loaded already
					$('#overlay-content > .player_extra_upper').removeClass('hidden');
				}
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
			if (!$(this).hasClass("version-tab-selected")) {
				$(".version-tab").each(function(){
					$(this).removeClass("version-tab-selected");
				});
				$(".version-content").each(function(){
					$(this).addClass("hidden");
				});
				$(this).addClass("version-tab-selected");
				$("#version-content-"+$(this).attr('data-version-id')).removeClass("hidden");
				
				//Change URL
				var url = new URL(window.location);
				url.pathname=$(this).attr('data-version-slug');
				history.replaceState(null, null, url);
				
				//Change cover and featured image
				$('.series-thumbnail').attr('src', $('.series-thumbnail').attr('src').replace(/\d+/, $(this).attr('data-version-id')));
				$('.background').attr('src', $('.background').attr('src').replace(/\d+/, $(this).attr('data-version-id')));
				
				//Change synopsis
				$('.series-synopsis-real').html($(this).attr('data-version-synopsis'));
				resizeSynopsisHeight(true);
				$('.series-title').text($(this).attr('data-version-title'));
				var oldTitle = document.title.split('|');
				oldTitle.shift();
				document.title=$(this).attr('data-version-title')+' | '+oldTitle.join('|');
				if ($(this).attr('data-version-alternate-titles')!='') {
					$('.series-alternate-names').text($(this).attr('data-version-alternate-titles'));
					$('.series-alternate-names').removeClass('hidden');
				} else {
					$('.series-alternate-names').addClass('hidden');
				}
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
		$(".season-chooser").on('change', function(){
			var versionId = $(this).closest('.version-content')[0].id.split('-').pop();
			$('#version-content-'+versionId+' .division-container').addClass('hidden');
			$('#division-container-'+versionId+'-'+$(this).val()).removeClass('hidden');
			$(this).removeClass('season-unavailable');
			if ($(this).find('option[value="'+$(this).val()+'"].season-unavailable').length>0) {
				$(this).addClass('season-unavailable');
			}
		});
		$(".sort-order").click(function(){
			var userSetting = 0;
			if ($(".sort-ascending").length>0) {
				//Sort all descending
				userSetting = 1;
				$(".sort-order").each(function(){
					$(this).removeClass("sort-ascending");
					$(this).addClass("sort-descending");
					$(this).find('.fa-fw').removeClass('fa-arrow-down-short-wide');
					$(this).find('.fa-fw').addClass('fa-arrow-down-wide-short');
					$(this).find('.sort-description').text(lang('js.catalogue.series.sort_last_to_first'));
				});
			} else {
				//Sort all ascending
				userSetting = 0;
				$(".sort-order").each(function(){
					$(this).removeClass("sort-descending");
					$(this).addClass("sort-ascending");
					$(this).find('.fa-fw').removeClass('fa-arrow-down-wide-short');
					$(this).find('.fa-fw').addClass('fa-arrow-down-short-wide');
					$(this).find('.sort-description').text(lang('js.catalogue.series.sort_first_to_last'));
				});
			}

			if ($('body.user-logged-in').length==0) {
				//Set cookie preference
				Cookies.set('episode_sort_order', userSetting, cookieOptions);
			} else {
				//Update on server
				var values = {
					'episode_sort_order': userSetting,
					'only_episode_sort_order' : 1
				};
				$.post({
					url: USERS_URL+"/do_save_settings.php",
					data: values,
					xhrFields: {
						withCredentials: true
					},
				});
			}

			$('.episode-table').each(function(){
				var episodes = $(this).find('.episode');
				episodes = episodes.get().reverse();
				for (var i = 0; i<episodes.length; i++) {
					$(episodes[i]).detach().appendTo($(this));
				}
			});
			$('.division-list').each(function(){
				var divisions = $(this).find('.division:not([id$="-altres"]):not([id$="-extras"]), .empty-divisions');
				var specialDivisions = $(this).find('.division[id$="-altres"], .division[id$="-extras"]');
				divisions = divisions.get().reverse();
				specialDivisions = specialDivisions.get();
				for (var i = 0; i<divisions.length; i++) {
					$(divisions[i]).detach().appendTo($(this));
				}
				for (var i = 0; i<specialDivisions.length; i++) {
					$(specialDivisions[i]).detach().appendTo($(this));
				}
			});

			$('.series-file-lists').each(function(){
				if ($(this).hasClass('series-file-lists-reversed')) {
					$(this).removeClass('series-file-lists-reversed');
				} else {
					$(this).addClass('series-file-lists-reversed');
				}
			});
		});

		$(".load-all-comments").click(function(){
			$(this).parent().find('.comment.hidden').removeClass('hidden');
			$(this).addClass('hidden');
		});

		$(".comment-send").click(function(){
			if ($('body.user-logged-in').length==0) {
				showAlert(lang('js.login_required.header'), lang('js.login_required.explanation.comment'));
			}
			else if ($($(this).parent().find('textarea').get(0)).val()!='') {
				sendUserComment($(this));
			}
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
				if ($('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('.division-container').length>0) {
					var seasonId = $('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('.division-container')[0].id.split('-').pop();
					$('[data-file-id="'+$('#autoopen_file_id').val()+'"]').closest('.section-content').find('.season-chooser').val(seasonId).trigger('change');
				}
				//Scroll and click file
				$('[data-file-id="'+$('#autoopen_file_id').val()+'"]')[0].scrollIntoView(false);
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
		
		if ($('.is-series-page').length>0 && window.location.hash!='') {
			$('.season-chooser option').each(function(){
				if ('#'+string_to_slug($(this).attr('data-title'))==window.location.hash) {
					$('.season-chooser').val($(this).val());
					$('.season-chooser').trigger('change');
					$('.season-chooser')[0].scrollIntoView();
				}
			});
			$('.division-list .division').each(function(){
				if ('#'+string_to_slug($(this).attr('data-title'))==window.location.hash) {
					$(this)[0].scrollIntoView();
				}
			});
			window.history.replaceState(null, null,  window.location.href.split('#')[0]);
		}
	} else {
		//This is an embed
		$('html').addClass('page-no-overflow');
		$('#overlay').removeClass('hidden');
		
		if ($('#overlay-content > .player_extra_upper').length==0) {
			$('<div class="player_extra_upper"><div class="player_extra_title">'+$('.embed-data').attr('data-title')+'</div>'+((isEmbedPage() && self==top) ? '' : '<button class="player_extra_close fa fa-times vjs-button" title="'+lang('js.dialog.close')+'" type="button" onclick="closeOverlay();"></button>')+'</div>').appendTo("#overlay-content");
		} else {
			if ($('.video-js .player_extra_upper').length==0) {
				//Only if player is not loaded already
				$('#overlay-content > .player_extra_upper').removeClass('hidden');
			}
			$('.player_extra_title').html($('.embed-data').attr('data-title'));
		}
		//Remove previous errors
		$('.player-error').remove();
		requestFileData($('.embed-data').attr('data-file-id'));
		window.parent.postMessage('embedInitialized', '*');
	}

	$(window).on('visibilitychange', function() {
		if (document.visibilityState!="visible") {
			sendCurrentFileTracking();
		}
	});
});

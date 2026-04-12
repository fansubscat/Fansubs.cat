var chatShouldStickToBottom = true;
var chatContainer;
var resizeObserver;
var pendingSentMessages = [];

function showForumDropdown() {
	$('#user-dropdown').removeClass('dropdown-show');
	$('#forum-dropdown').toggleClass('dropdown-show');
}

function showChatUsersDropdown() {
	$('#mchat-whois').toggleClass('show-user-list');
}

function exitChat() {
	mChat.refresh('/surt', shortUUID16()).always(function() {
		window.location.href="/";
	});
}

function addPendingSentMessage(originalInputValue, sentUUID) {
	pendingSentMessages.push({text: originalInputValue, uuid: sentUUID});
	syncPendingMessages();
}

function getFirstPendingSetMessage() {
	if (pendingSentMessages.length==0) {
		return null;
	} else {
		return pendingSentMessages[0];
	}
}

function removePendingMessage(uuid) {
	pendingSentMessages = pendingSentMessages.filter(p => p.uuid !== uuid);	
}

function syncPendingMessages() {
	$('.mchat-pending-message').remove();
	pendingSentMessages.forEach(function(pendingMessage) {
		var color = mChat.chatColor;
		if (!color) {
			color = '808080';
		}
		var username = $('#user-dropdown .dropdown-title').html();
		var avatarUrl = $('.dropdown-menu .avatar').attr('src');
		
		var el = document.createElement('div');
		el.textContent = pendingMessage.text;
		var escapedText = el.innerHTML;
		
		var message = '<li class="row mchat-message mchat-pending-message"><div class="mchat-avatar"><span><img title="Missatge pendent d’enviar: comprova la connexió" class="avatar" src="'+avatarUrl+'" width="42" alt="Imatge de perfil de l’usuari"></span></div><div class="mchat-message-wrapper"><ul class="mchat-buttons"><li><i title="Missatge pendent d’enviar: comprova la connexió" class="icon fa-warning"></i></li></ul><div class="mchat-message-header"><a href="./memberlist.php?mode=viewprofile&amp;u='+$('#my-user-id').val()+'" style="color: #6aa0f8;" class="username-coloured" target="_blank">'+username+'</a> • <span class="mchat-time">Missatge pendent d’enviar</span></div><div class="mchat-text"><span style="color:#'+color+'"> '+escapedText+' </span></div></div></li>';

		if (mChat.messageTop) {
			mChat.cached('messages').prepend(message);
		} else {
			mChat.cached('messages').append(message);
		}
	});
	
	document.querySelectorAll('.mchat-message').forEach(elem => {
		//console.log('Added to observer: '+elem.id);
		resizeObserver.observe(elem);
	});
}

function addTargetToExternalLinks() {
	$('.postlink').each(function() {
		var hostname = window.location.hostname;
		try {
			hostname = new URL($(this).attr('href')).hostname
		} catch(e){
			console.log('Invalid URL: '+$(this).attr('href'));
		}
		if ($('#mchat-body').length>0 || hostname!=window.location.hostname) {
			$(this).attr("target", '_blank');
		}
	});
}

function previewChatColor(color) {
	$('.chat-choose-color-wrapper span').css('color', color);
}

function extractColorFromCode(code) {
	const regex = /^\[color="#([A-Fa-f0-9]{6})"\]\s*(.*?)\s*\[\/color\]$/;
	const match = code.match(regex);

	if (match) {
		return {
			color: match[1],
			message: match[2]
		};
	}

	// No color tag
	return {
		color: '808080',
		message: code
	};
}

function showChatSettings() {
	var currentSound = mChat.chatSound;
	if (!currentSound) {
		currentSound = 'default';
	}
	var currentColor = mChat.chatColor;
	if (!currentColor) {
		currentColor = '808080';
	}
	var code = '<div class="settings-section-data"><div class="settings-section-data-switch"><div class="settings-section-data-header"><div class="settings-section-data-header-title">So de les notificacions</div><div class="settings-section-data-header-subtitle">Tria el so de notificacions que vols fer servir per als missatges rebuts o silencia’l.</div></div><select id="chat-sound" class="settings-combo" onchange="mChat.sound(\'add\', this.value);"><option value="default"'+ (currentSound=='default' ? ' selected' : '') +'>Per defecte</option><option value="adara"'+ (currentSound=='adara' ? ' selected' : '') +'>Adara</option><option value="msn"'+ (currentSound=='msn' ? ' selected' : '') +'>Messenger</option><option value="tutturu"'+ (currentSound=='tutturu' ? ' selected' : '') +'>Tutturu</option><option value="silence"'+ (currentSound=='silence' ? ' selected' : '') +'>Silenci</option></select></div></div>';
	code += '<div class="settings-section-data"><div class="settings-section-data-switch"><div class="settings-section-data-header"><div class="settings-section-data-header-title">Color dels missatges</div><div class="settings-section-data-header-subtitle">Tria el color per defecte per als missatges que enviïs al xat.</div></div><div class="chat-choose-color-wrapper"><div title="Previsualització: s’hauria de veure bé amb fons blanc i negre"><span class="dark" style="color: #'+currentColor+'">Aa</span><span class="light" style="color: #'+currentColor+'">Aa</span></div><input id="chat-color" type="color" value="#'+currentColor+'" oninput="previewChatColor(this.value)" /></div></div></div>';
	
	showCustomDialog(lang('js.community.chat_options.title'), code, null, true, true, [
		{
			text: lang('js.community.chat_edit_message.save'),
			class: 'normal-button',
			onclick: function(){
				saveChatSettings();
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
	]);
}

function saveChatSettings() {
	var newSound = $('#chat-sound').val();
	var newColor = $('#chat-color').val().replaceAll('#','').toUpperCase();
	mChat.ajaxRequest('preferences', true, {
		chat_color: newColor,
		chat_sound: newSound
	})
	mChat.chatColor = newColor;
	mChat.chatSound=newSound;
}

function addChatMention(username) {
	var mention = "[mention]"+username+"[/mention]";
	if ($('#mchat-input').val()!='') {
		$('#mchat-input').val($('#mchat-input').val()+" "+mention);
	} else {
		$('#mchat-input').val(mention);
	}
	$('#mchat-input').focus();
}

function isUserAtBottom(container) {
	var scrollLeeway = 100;
	var scrollTop = container.scrollTop();
	var scrollHeight = container[0].scrollHeight;
	var height = container.outerHeight();
	console.log('Scrolled: User at bottom? '+((scrollHeight - height - scrollTop <= scrollLeeway) ? 'yes' : 'no')+', scrollTop='+scrollTop+', scrollHeight='+scrollHeight+', height='+height+', value='+(scrollHeight - height - scrollTop));
	return scrollHeight - height - scrollTop <= scrollLeeway;
}

function scrollToBottom(container) {
	container.scrollTop(container[0].scrollHeight);
	//console.log('Scrolled to bottom');
}

function shortUUID16() {
	return [...crypto.getRandomValues(new Uint8Array(8))]
		.map(b => b.toString(16).padStart(2, '0'))
		.join('');
}

$(document).ready(function() {
	var flair_tooltip = undefined;
	var flair = $('.flair-icon');
	flair.mouseenter(function(e) {
		var x = (e.pageX + 16);
		var y = (e.pageY + 16);

		flair_tooltip = document.createElement('div');
		flair_tooltip.className = 'flair-tooltip';
		flair_tooltip.style.left = x+'px';
		flair_tooltip.style.top = y+'px';

		var icon = document.createElement('img');
		icon.src = this.dataset.image;
		flair_tooltip.appendChild(icon);

		var title = document.createElement('h4');
		title.innerHTML = this.dataset.title;
		flair_tooltip.appendChild(title);

		var description = document.createElement('div');
		description.innerHTML = this.dataset.description;
		flair_tooltip.appendChild(description);

		document.body.appendChild(flair_tooltip);

	}).mousemove(function(e) {
		var x = (e.pageX + 16);
		var y = (e.pageY + 16);

		var rect = document.body.getBoundingClientRect();
		var max_x = rect.width - (400 + 16);
		if (x > max_x) {
			x = max_x;
		}

		flair_tooltip.style.left = x + 'px';
		flair_tooltip.style.top = y + 'px';

	}).mouseleave(function(e) {
		if (flair_tooltip !== undefined) {
			flair_tooltip.parentNode.removeChild(flair_tooltip);
		}
	});
	addTargetToExternalLinks();
	
	chatContainer = $('#mchat-messages');
	
	chatContainer.on(('onscrollend' in window ? 'scrollend' : 'scroll'), function () {
		chatShouldStickToBottom = isUserAtBottom(chatContainer);
	})
	resizeObserver = new ResizeObserver(() => {
		//console.log('Resize observer called!');
		if (chatShouldStickToBottom) {
			scrollToBottom(chatContainer);
		}
	});
	
	document.querySelectorAll('.mchat-message').forEach(elem => {
		//console.log('Added to observer: '+elem.id);
		resizeObserver.observe(elem);
	});
	
	window.onkeydown = function(e) {
		if (e.ctrlKey) {
			switch (e.code) {
				case 'KeyB':
					bbstyle(0);
					return false;
				case 'KeyI':
					bbstyle(2);
					return false;
				case 'KeyU':
					bbstyle(4);
					return false;
				case 'KeyL':
					bbstyle(16);
					return false;
				case 'KeyS':
					bbfontstyle('[spoiler]', '[/spoiler]');
					return false;
				case 'KeyG':
					bbstyle(14);
					return false;
				case 'KeyM':
					bbfontstyle('[media]', '[/media]');
					return false;
				case 'KeyH':
					insert_text(' :hohoho:');
					return false;
				default:
					return true;
			}
		}
		else if ($('.chat-page').length>0) {
			if (e.metaKey || e.altKey) {
				return true;
			}
			var k = e.keyCode;
			// Verify that the key entered is not a special key
			if (k == 20 /* Caps lock */
				|| k == 16 /* Shift */
				|| k == 9 /* Tab */
				|| k == 27 /* Escape Key */
				|| k == 17 /* Control Key */
				|| k == 91 /* Windows Command Key */
				|| k == 19 /* Pause Break */
				|| k == 18 /* Alt Key */
				|| k == 93 /* Right Click Point Key */
				|| (k >= 35 && k <= 40) /* Home, End, Arrow Keys */
				|| k == 45 /* Insert Key */
				|| (k >= 33 && k <= 34 ) /*Page Down, Page Up */
				|| (k >= 112 && k <= 123) /* F1 - F12 */
				|| (k >= 144 && k <= 145 )) { /* Num Lock, Scroll Lock */
				return true;
			}
			if ($('#dialog-overlay').length==0) {
				$('#mchat-input').focus();
			}
		}
	};
});

function showForumDropdown() {
	$('#user-dropdown').removeClass('dropdown-show');
	$('#forum-dropdown').toggleClass('dropdown-show');
}

function showChatUsersDropdown() {
	$('#mchat-whois').toggleClass('show-user-list');
}

function exitChat() {
	mChat.refresh('/surt').always(function() {
		window.location.href="/";
	});
}

function addTargetToExternalLinks() {
	$('.postlink').each(function() {
		if ($('#mchat-body').length>0 || new URL($(this).attr('href')).hostname!=window.location.hostname) {
			$(this).attr("target", '_blank');
		}
	});
}

function previewChatColor(color) {
	$('.chat-choose-color-wrapper span').css('color', color);
}

function showChatSettings() {
	var currentSound = mChat.storage.get('sound');
	if (!currentSound) {
		currentSound = 'default';
	}
	var currentColor = mChat.storage.get('color');
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
	mChat.storage.set('sound', newSound);
	if (newColor=='000000' || newColor=='FFFFFF') {
		mChat.storage.remove('color');
	} else {
		mChat.storage.set('color', newColor);
	}
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

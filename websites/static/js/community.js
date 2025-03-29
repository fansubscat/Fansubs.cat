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

function showChatSettings() {
	var currentSound = mChat.storage.get('sound');
	if (!currentSound) {
		currentSound = 'default';
	}
	var currentColor = mChat.storage.get('color');
	if (!currentColor) {
		currentColor = '000000';
	}
	var code = '<div class="settings-section-data"><div class="settings-section-data-switch"><div class="settings-section-data-header"><div class="settings-section-data-header-title">So de les notificacions</div><div class="settings-section-data-header-subtitle">Tria el so de notificacions que vols fer servir per als missatges rebuts o silencia’l.</div></div><select id="chat-sound" class="settings-combo" onchange="mChat.sound(\'add\', this.value);"><option value="default"'+ (currentSound=='default' ? ' selected' : '') +'>Per defecte</option><option value="adara"'+ (currentSound=='adara' ? ' selected' : '') +'>Adara</option><option value="msn"'+ (currentSound=='msn' ? ' selected' : '') +'>Messenger</option><option value="tutturu"'+ (currentSound=='tutturu' ? ' selected' : '') +'>Tutturu</option><option value="silence"'+ (currentSound=='silence' ? ' selected' : '') +'>Silenci</option></select></div></div>';
	code += '<div class="settings-section-data"><div class="settings-section-data-switch"><div class="settings-section-data-header"><div class="settings-section-data-header-title">Color dels missatges</div><div class="settings-section-data-header-subtitle">Tria el color per defecte per als missatges que enviïs al xat.</div></div><input id="chat-color" type="color" value="#'+currentColor+'" /></div></div>';
	
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
});

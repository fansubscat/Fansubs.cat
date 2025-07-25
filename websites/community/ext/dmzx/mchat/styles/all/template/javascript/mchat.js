/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2009 Shapoval Andrey Vladimirovich (AllCity) ~ http://allcity.net.ru/
 * @copyright (c) 2013 Rich McGirr (RMcGirr83) http://rmcgirr83.org
 * @copyright (c) 2015 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

// Support Opera
if (typeof document.hasFocus === 'undefined') {
	document.hasFocus = function() {
		return document.visibilityState === 'visible';
	};
}

if (!Array.prototype.max) {
	Array.prototype.max = function() {
		return Math.max.apply(null, this);
	};
}

if (!Array.prototype.min) {
	Array.prototype.min = function() {
		return Math.min.apply(null, this);
	};
}

Array.prototype.removeValue = function(value) {
	var index;
	var elementsRemoved = 0;
	while ((index = this.indexOf(value)) !== -1) {
		this.splice(index, 1);
		elementsRemoved++;
	}
	return elementsRemoved;
};

String.prototype.format = function() {
	var str = this.toString();
	if (!arguments.length) {
		return str;
	}
	var type = typeof arguments[0];
	var args = 'string' === type || 'number' === type ? arguments : arguments[0];
	for (var arg in args) {
		if (args.hasOwnProperty(arg)) {
			str = str.replace(new RegExp("\\{" + arg + "\\}", "gi"), args[arg]);
		}
	}
	return str;
};

String.prototype.replaceMany = function() {
	var result = this;
	var args = arguments[0];
	for (var arg in args) {
		if (args.hasOwnProperty(arg)) {
			result = result.replace(new RegExp(RegExp.escape(arg), "g"), args[arg]);
		}
	}
	return result;
};

RegExp.escape = function(s) {
	return s.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
};

jQuery.fn.reverse = function(reverse) {
	return typeof reverse === 'undefined' || reverse ? jQuery(this.toArray().reverse()) : this;
};

function StorageWrapper(storage, prefix) {
	this.prefix = prefix;
	try {
		this.storage = window[storage];
		this.storage.setItem(prefix, prefix);
		this.storage.removeItem(prefix);
	} catch (e) {
		this.storage = false;
	}
}

StorageWrapper.prototype.get = function(key) {
	return this.storage && this.storage.getItem(this.prefix + key);
};

StorageWrapper.prototype.set = function(key, value) {
	this.storage && this.storage.setItem(this.prefix + key, value);
};

StorageWrapper.prototype.remove = function(key) {
	return this.storage && this.storage.removeItem(this.prefix + key);
};

jQuery(function($) {

	"use strict";

	$.extend(mChat, {
		storage: new StorageWrapper('localStorage', mChat.cookie + 'mchat_'),
		ajaxRequest: function(mode, sendHiddenFields, data) {
			var deferred = $.Deferred();
			$.extend(data,
				{_referer: mChat.currentUrl},
				sendHiddenFields ? mChat.hiddenFields : {}
			);
			$(mChat).trigger('mchat_send_request_before', [mode, data]);
			$.ajax({
				url: mChat.actionUrls[mode],
				timeout: Math.min(mChat.refreshTime, 10000) - 100,
				method: 'POST',
				dataType: 'json',
				data: data,
				context: {
					mode: mode,
					deferred: deferred
				}
			}).done(mChat.ajaxDone).fail(deferred.reject);
			return deferred.promise().fail(mChat.ajaxFail);
		},
		ajaxDone: function(json, status, xhr) {
			var data = {
				mode: this.mode,
				json: json,
				status: status,
				xhr: xhr,
				handle: true
			};
			$(mChat).trigger('mchat_ajax_done_before', [data]);
			if (data.handle) {
				if (json[data.mode]!==undefined) {
					this.deferred.resolve(data.json, data.status, data.xhr);
				} else {
					this.deferred.reject(data.xhr, data.status, mChat.lang.parserErr);
				}
			}
		},
		ajaxFail: function(xhr, textStatus, errorThrown) {
			mChat.skipNextRefresh = true;
			if (typeof console !== 'undefined' && console.log) {
				console.log('AJAX error. status: ' + textStatus + ', message: ' + errorThrown + ' (' + xhr.responseText + ')');
			}
			var data = {
				mode: this.mode,
				xhr: xhr,
				textStatus: textStatus,
				errorThrown: errorThrown,
				updateSession: function() {
					if (this.xhr.status === 403) {
						mChat.endSession(true);
					} else {
						mChat.resetSession();
					}
				}
			};
			$(mChat).trigger('mchat_ajax_fail_before', [data]);
			//mChat.sound('error');
			mChat.status('error');
			var title = mChat.lang.err;
			var responseText;
			try {
				var json = data.xhr.responseJSON;
				if (json.S_USER_WARNING || json.S_USER_NOTICE) {
					title = json.MESSAGE_TITLE;
					responseText = json.MESSAGE_TEXT;
					data.xhr.status = 403;
				} else {
					responseText = json.message || data.errorThrown;
				}
			} catch (e) {
				responseText = data.errorThrown;
			}
			if (responseText && responseText !== 'timeout') {
				if (data.xhr.status == 403) {
					showCustomDialog(lang('js.community.chat_session_ended.title'), lang('js.community.chat_session_ended.description'), null, false, true, [
						{
							text: lang('js.community.chat_session_ended.button_return'),
							class: 'normal-button',
							onclick: function(){
								window.location.href="/";
							}
						},
						{
							text: lang('js.community.chat_session_ended.button_reconnect'),
							class: 'cancel-button',
							onclick: function(){
								window.location.reload();
							}
						}
					]);
				} else if (data.xhr.status == 400) {
					showAlert(title, responseText);
				}
			}
			data.updateSession();
		},
		sound: function(file, forceSoundType=null) {
			var soundType = mChat.storage.get('sound');
			if (!soundType) {
				soundType = 'default';
			}
			if (forceSoundType) {
				soundType = forceSoundType;
			}
			if (soundType=='silence') {
				return;
			}
			var data = {
				audio: mChat.cached('sound-' + file + (file=='add' ? ('-' + soundType) : ''))[0],
				file: file,
				play: true
			};
			$(mChat).trigger('mchat_sound_before', [data]);
			if (data.play && data.audio && data.audio.duration) {
				data.audio.pause();
				data.audio.currentTime = 0;
				data.audio.play();
			}
		},
		titleAlert: function() {
			var data = {
				doAlert: !document.hasFocus(),
				interval: 1000
			};
			$(mChat).trigger('mchat_titlealert_before', [data]);
			if (data.doAlert) {
				$.titleAlert(mChat.lang.newMessageAlert, data);
			}
		},
		toggle: function() {
			var name = $(this).data('mchat-element');
			var $elem = mChat.cached(name);
			$elem.stop().toggle();
		},
		confirm: function(data) {
			var $confirmFields = data.container.find('.mchat-confirm-fields');
			$confirmFields.children().hide();
			var fields = data.fields($confirmFields);
			$.each(fields, function() {
				$(this).show();
			});
			setTimeout(function() {
				var $input = $confirmFields.find(':input:visible:enabled:first');
				if ($input.length) {
					var value = $input.val();
					$input.trigger('focus').val('').val(value);
				}
			}, 1);
			phpbb.confirm(data.container.show(), function(success) {
				if (success && typeof data.confirm === 'function') {
					data.confirm.apply(this, fields);
				}
			});
		},
		add: function() {
			var $add = mChat.cached('add');
			if ($add.prop('disabled')) {
				return;
			}
			var $input = mChat.cached('input');
			var originalInputValue = mChat.cleanMessage($input.val()).trim();
			var messageLength = originalInputValue.length;
			if (!messageLength) {
				return;
			}
			if (mChat.mssgLngth && messageLength > mChat.mssgLngth) {
				showAlert(lang('js.community.chat_error.title'), lang('js.community.chat_error.error_too_long'));
				return;
			}
			$add.prop('disabled', true);
			mChat.pauseSession();
			var inputValue = originalInputValue;
			var color = mChat.storage.get('color');
			if (!color) {
				color = '808080';
			}
			inputValue = '[color=#' + color + '] ' + inputValue + ' [/color]';
			mChat.setText('');
			mChat.refresh(inputValue).done(function() {
				mChat.resetSession();
			}).fail(function() {
				mChat.setText(originalInputValue);
			}).always(function() {
				$add.prop('disabled', false);
				$input.delay(1).trigger('focus');
			});
		},
		edit: function() {
			var $message = $(this).closest('.mchat-message');
			var code = '<textarea style="width: 50vw; height: 8rem;" id="edit-chat-message">'+$("<div>").text($message.data('mchat-message')).html()+'</textarea>';
			showCustomDialog(lang('js.community.chat_edit_message.title'), code, null, true, true, [
				{
					text: lang('js.community.chat_edit_message.save'),
					class: 'normal-button',
					onclick: function(){
						mChat.ajaxRequest('edit', true, {
							message_id: $message.data('mchat-id'),
							message: $('#edit-chat-message').val(),
							page: mChat.page
						}).done(mChat.editDone);
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
		},
		editDone: function(json) {
			mChat.updateMessages($(json.edit));
			mChat.resetSession();
		},
		del: function() {
			var delId = $(this).closest('.mchat-message').data('mchat-id');
			showCustomDialog(lang('js.dialog.confirm'), mChat.lang.delConfirm, null, true, true, [
				{
					text: lang('js.dialog.yes'),
					class: 'normal-button',
					onclick: function(){
						mChat.ajaxRequest('del', true, {
							message_id: delId
						}).done(mChat.delDone);
						closeCustomDialog();
					}
				},
				{
					text: lang('js.dialog.no'),
					class: 'cancel-button',
					onclick: function(){
						closeCustomDialog();
					}
				}
			]);
		},
		delDone: function(json) {
			mChat.removeMessages([json.del]);
			mChat.resetSession();
		},
		refresh: function(message) {
			var isAdd = typeof message !== 'undefined';
			if (!isAdd) {
				mChat.sessionLength += mChat.refreshTime;
				if (mChat.timeout && mChat.sessionLength >= mChat.timeout) {
					mChat.endSession();
					return;
				} else if (mChat.skipNextRefresh) {
					mChat.skipNextRefresh = false;
					return;
				}
			}
			var data = {
				last: mChat.messageIds.length ? mChat.messageIds.max() : 0,
				log: mChat.logId,
				message: isAdd ? message : undefined
			};
			//mChat.status('load');
			return mChat.ajaxRequest(isAdd ? 'add' : 'refresh', isAdd, data).done(mChat.refreshDone);
		},
		refreshDone: function(json) {
			$(mChat).trigger('mchat_response_handle_data_before', [json]);
			$('.mchat-topic').html(json.topic);
			if (json.topic) {
				$('.mchat-topic').removeClass('hidden');
			} else {
				$('.mchat-topic').addClass('hidden');
			}
			if (json.add) {
				mChat.addMessages($(json.add));
			}
			if (json.edit) {
				mChat.updateMessages($(json.edit));
			}
			if (json.del) {
				mChat.removeMessages(json.del);
			}
			if (json.clear) {
				mChat.clearMessages(json.clear);
			}
			mChat.whoisDone(json.users);
			if (json.log) {
				mChat.logId = json.log;
			}
			if (mChat.refreshInterval) {
				mChat.status('ok');
			}
			$(mChat).trigger('mchat_response_handle_data_after', [json]);
		},
		rules: function() {
			$('.mchat-nav-link-title').each(phpbb.toggleDropdown);
			popup(this.href, 450, 275);
		},
		whoisDone: function(json) {
			var $userlist = $('#mchat-userlist');
			$userlist.html(json);
		},
		addMessages: function($messages) {
			mChat.cached('messages').find('.mchat-no-messages').remove();
			$messages.reverse(mChat.messageTop).hide().each(this.addMessage);
			addTargetToExternalLinks();
		},
		addMessage: function(index) {
			var $message = $(this);
			var myUserId = $('#my-user-id').val();
			var dataAddMessageBefore = {
				message: $message,
				delay: mChat.refreshInterval ? 400 : 0,
				abort: $.inArray($message.data('mchat-id'), mChat.messageIds) !== -1,
				playSound: $message.data('mchat-user-id')!=myUserId,
				titleAlert: $message.data('mchat-user-id')!=myUserId
			};
			$(mChat).trigger('mchat_add_message_before', [dataAddMessageBefore]);
			if (dataAddMessageBefore.abort) {
				return;
			}
			
			mChat.clearOldMessages();
			if (dataAddMessageBefore.playSound) {
				mChat.sound('add');
			}
			if (dataAddMessageBefore.titleAlert) {
				mChat.titleAlert();
			}
			mChat.messageIds.push($message.data('mchat-id'));
			mChat.fixJumpToUrl.call($message);
			var dataAddMessageAnimateBefore = {
				container: mChat.cached('messages'),
				message: $message,
				add: function() {
					if (mChat.messageTop) {
						this.container.prepend(this.message);
					} else {
						this.container.append(this.message);
					}
				},
				show: function() {
					var scrollLeeway = 150;
					var scrollTop = this.container.scrollTop();
					var scrollHeight = this.container[0].scrollHeight;
					this.message.show();
					this.message.addClass('mchat-message-flash');
					if (mChat.messageTop) {
						if (scrollTop <= scrollLeeway)  {
							this.container.scrollTop(0);
						}
					} else {
						var height = this.container.height();
						if (scrollHeight - height - scrollTop <= scrollLeeway) {
							this.container.scrollTop(scrollHeight);
						}
					}
				}
			};
			$(mChat).trigger('mchat_add_message_animate_before', [dataAddMessageAnimateBefore]);
			dataAddMessageAnimateBefore.add();
			dataAddMessageAnimateBefore.show();
			if (mChat.editDeleteLimit && $message.data('mchat-edit-delete-limit') && $message.find('[data-mchat-action="edit"], [data-mchat-action="del"]').length > 0) {
				var id = $message.prop('id');
				setTimeout(function() {
					$('#' + id).find('[data-mchat-action="edit"], [data-mchat-action="del"]').fadeOut(function() {
						$(this).closest('li').remove();
					});
				}, mChat.editDeleteLimit);
			}
			mChat.startRelativeTimeUpdate.call($message);
		},
		clearOldMessages: function() {
			while (mChat.messageIds.length>50) {
				var messageIdToRemove = mChat.messageIds.shift();
				var data = {
					id: messageIdToRemove,
					message: $('#mchat-message-' + messageIdToRemove)
				};
				$(mChat).trigger('mchat_delete_message_before', [data]);
				mChat.stopRelativeTimeUpdate(data.message);
				data.message.remove();
			}
		},
		updateMessages: function($messages) {
			var playSound = true;
			$messages.each(function() {
				var $newMessage = $(this);
				var data = {
					newMessage: $newMessage,
					oldMessage: $('#mchat-message-' + $newMessage.data('mchat-id')),
					playSound: playSound
				};
				$(mChat).trigger('mchat_edit_message_before', [data]);
				mChat.stopRelativeTimeUpdate(data.oldMessage);
				mChat.startRelativeTimeUpdate.call(data.newMessage);
				mChat.fixJumpToUrl.call(data.newMessage);
				data.oldMessage.fadeOut(function() {
					data.oldMessage.replaceWith(data.newMessage.hide().fadeIn());
				});
				if (data.playSound) {
					mChat.sound('edit');
					playSound = false;
				}
			});
			addTargetToExternalLinks();
		},
		removeMessages: function(ids) {
			var playSound = true;
			$.each(ids, function(i, id) {
				if (mChat.messageIds.removeValue(id)) {
					var data = {
						id: id,
						message: $('#mchat-message-' + id),
						playSound: playSound
					};
					$(mChat).trigger('mchat_delete_message_before', [data]);
					mChat.stopRelativeTimeUpdate(data.message);
					(function($message) {
						$message.fadeOut(function() {
							$message.remove();
						});
					})(data.message);
					if (data.playSound) {
						mChat.sound('del');
						playSound = false;
					}
				}
			});
		},
		clearMessages: function(lastIdToBeCleared) {
			var playSound = true;
			var allMessageIds = mChat.messageIds.slice();
			$.each(allMessageIds, function(i, id) {
				if (id <= lastIdToBeCleared && mChat.messageIds.removeValue(id)) {
					var data = {
						id: id,
						message: $('#mchat-message-' + id),
						playSound: playSound
					};
					$(mChat).trigger('mchat_delete_message_before', [data]);
					mChat.stopRelativeTimeUpdate(data.message);
					(function($message) {
						$message.fadeOut(function() {
							$message.remove();
						});
					})(data.message);
					if (data.playSound) {
						mChat.sound('del');
						playSound = false;
					}
				}
			});
		},
		startRelativeTimeUpdate: function() {
			if (mChat.relativeTime) {
				$(this).find('.mchat-time[data-mchat-relative-update]').each(function() {
					var $time = $(this);
					setTimeout(function() {
						mChat.relativeTimeUpdate($time);
						$time.data('mchat-relative-interval', setInterval(function() {
							mChat.relativeTimeUpdate($time);
						}, 60 * 1000));
					}, $time.data('mchat-relative-update') * 1000);
				});
			}
		},
		relativeTimeUpdate: function($time) {
			var minutesAgo = $time.data('mchat-minutes-ago') + 1;
			var langMinutesAgo = mChat.lang.minutesAgo[minutesAgo];
			if (langMinutesAgo) {
				$time.text(langMinutesAgo).data('mchat-minutes-ago', minutesAgo);
			} else {
				mChat.stopRelativeTimeUpdate($time);
				$time.text($time.attr('title')).removeAttr('data-mchat-relative-update data-mchat-minutes-ago data-mchat-relative-interval');
			}
		},
		stopRelativeTimeUpdate: function($message) {
			var selector = '.mchat-time[data-mchat-relative-update]';
			clearInterval($message.find(selector).addBack(selector).data('mchat-relative-interval'));
		},
		status: function(status) {
			var data = {
				status: status,
				container: mChat.cached('status')
			};
			$(mChat).trigger('mchat_status_before', [data]);
			var $activeStatus = data.container.find('.mchat-status-' + data.status).removeClass('hidden');
			if ($activeStatus.length) {
				data.container.find('.mchat-status').not($activeStatus).addClass('hidden');
			}
		},
		pauseSession: function() {
			clearInterval(mChat.refreshInterval);
			mChat.refreshInterval = false;
		},
		resetSession: function() {
			if (mChat.page === 'archive') {
				return;
			}
			mChat.pauseSession();
			mChat.sessionLength = 0;
			mChat.refreshInterval = setInterval(mChat.refresh, mChat.refreshTime);
			mChat.status('ok');
		},
		endSession: function(skipUpdateWhois) {
			mChat.pauseSession();
			mChat.status('paused');
		},
		updateCharCount: function() {
			var count = mChat.cleanMessage(mChat.cached('input').val()).length;
			if (mChat.showCharCount) {
				var charCount = mChat.lang.charCount.format({current: count, max: mChat.mssgLngth});
				var $elem = mChat.cached('character-count').html(charCount).toggleClass('invisible', count === 0);
				if (mChat.mssgLngth) {
					$elem.toggleClass('error', count > mChat.mssgLngth);
				}
			}
			if (mChat.mssgLngth) {
				var exceedCount = mChat.mssgLngth - count;
				mChat.cached('exceed-character-count').text(exceedCount).toggleClass('hidden', exceedCount >= 0);
				mChat.cached('input').parent().toggleClass('mchat-input-error', exceedCount < 0);
				mChat.cached('add').toggleClass('hidden', exceedCount < 0);
			}
		},
		cleanMessage: function(message) {
			if (!mChat.maxInputHeight) {
				message = message.replace(/\s+/g, ' ');
			}
			return message;
		},
		smiley: function() {
			mChat.appendText($(this).data('smiley-code'), true);
		},
		smileyPopup: function() {
			popup(this.href, 300, 350, '_phpbbsmilies');
		},
		mention: function() {
			var $container = $(this).closest('.mchat-message');
			var username = $container.data('mchat-username');
			if (mChat.allowBBCodes) {
				var profileUrl = $container.find(".mchat-message-header a[class^='username']").prop('href');
				if (profileUrl) {
					var usercolor = $container.data('mchat-usercolor');
					if (usercolor) {
						username = '[url=' + profileUrl + '][b][color=' + usercolor + ']' + username + '[/color][/b][/url]';
					} else {
						username = '[url=' + profileUrl + '][b]' + username + '[/b][/url]';
					}
				}
			}
			mChat.appendText(mChat.lang.mention.format({username: username}));
		},
		fixJumpToUrl: function() {
			var $message = $(this);
			var $elem = $message.find('blockquote [data-post-id]');
			var messageId = $elem.data('post-id');
			var data = {
				message: $message,
				elem: $elem,
				url: mChat.getArchiveQuoteUrl(messageId)
			};
			$(mChat).trigger('mchat_fix_jump_to_url_before', [data]);
			if (data.url) {
				data.elem.attr('href', data.url);
			}
		},
		getArchiveQuoteUrl: function(messageId) {
			var archiveUrl = $('.mchat-nav-archive').find('a').prop('href');
			return archiveUrl ? mChat.addUrlParam(archiveUrl, 'jumpto=' + messageId) : false;
		},
		jumpToMessage: function() {
			var messageId = $(this).data('post-id');
			var data = {
				container: mChat.cached('messages'),
				messageId: messageId,
				message: $('#mchat-message-' + messageId),
				jump: function() {
					if (data.message.length) {
						var scrollTop = data.message.offset().top - data.container.offset().top + data.container.scrollTop();
						data.container.scrollTop(scrollTop);
						data.message.removeClass('mchat-message-flash');
						data.message.offset();
						data.message.addClass('mchat-message-flash');
					} else {
						var url = mChat.getArchiveQuoteUrl(data.messageId);
						if (url) {
							window.open(url, '_blank');
						}
					}
				}
			};
			$(mChat).trigger('mchat_jump_to_message_before', [data]);
			data.jump();
		},
		getQuoteText: function($container) {
			var quote = $container.data('mchat-message');
			var quoteAttributes = [
				'"' + $container.data('mchat-username') + '"',
				'post_id=' + $container.data('mchat-id'),
				'time=' + $container.data('mchat-message-time'),
				'user_id=' + $container.data('mchat-user-id')
			];
			return '[quote=' + quoteAttributes.join(' ') + '] ' + quote + ' [/quote]';
		},
		quote: function() {
			mChat.appendText(mChat.getQuoteText($(this).closest('.mchat-message')));
		},
		like: function() {
			mChat.appendText('[i]' + mChat.lang.likes + '[/i]' + mChat.getQuoteText($(this).closest('.mchat-message')));
		},
		ip: function() {
			popup(this.href, 750, 500);
		},
		setText: function(text) {
			mChat.cached('input').val('');
			mChat.appendText(text);
		},
		appendText: function(text, spaces, popup) {
			var $input = mChat.cached('input');
			if (text) {
				insert_text(text, spaces, popup);
			}
			if (mChat.maxInputHeight) {
				autosize.update($input);
			} else {
				$input.scrollLeft($input[0].scrollWidth - $input[0].clientWidth);
			}
		},
		addUrlParam: function(url, keyEqualsValue) {
			return url + (url.indexOf('?') === -1 ? '?' : '&') + keyEqualsValue;
		},
		cached: function(name) {
			if (!mChat.cache) {
				mChat.cache = {};
			}
			if (!mChat.cache.hasOwnProperty(name)) {
				mChat.cache[name] = $('#mchat-' + name);
			}
			return mChat.cache[name];
		},
		onKeyPress: function(e, callbacks) {
			var isEnter = e.which === 10 || e.which === 13;
			if (isEnter && $(e.target).is('textarea')) {
				var callback;
				var isCtrl = e.ctrlKey || e.metaKey;
				if (mChat.maxInputHeight && isCtrl) {
					callback = 'newline';
				} else {
					callback = 'submit';
				}
				if (typeof callbacks[callback] === 'function') {
					callbacks[callback].call(this, e);
				}
			}
		},
	});

	mChat.messageIds = mChat.cached('messages').children()
		.each(mChat.startRelativeTimeUpdate)
		.each(mChat.fixJumpToUrl)
		.map(function() { return $(this).data('mchat-id'); }).get();

	if (!mChat.messageIds.length) {
		mChat.messageIds.push(mChat.latestMessageId);
	}

	mChat.hiddenFields = {};
	mChat.cached('form').find('input[type=hidden]').each(function() {
		mChat.hiddenFields[this.name] = this.value;
	});

	if (mChat.page === 'archive') {
		if (mChat.jumpTo) {
			var fragment = '#mchat-message-' + mChat.jumpTo;
			if ($(fragment).addClass('mchat-message-flash').length) {
				window.location.hash = fragment;
			}
		}
	} else {
		mChat.resetSession();

		if (!mChat.messageTop) {
			mChat.cached('messages').delay(1).scrollTop(mChat.cached('messages')[0].scrollHeight);
		}

		$.each(mChat.removeBBCodes.split('|'), function(i, bbcode) {
			var bbCodeClass = '.bbcode-' + bbcode.replaceMany({
				'=': '-',
				'*': 'asterisk'
			});
			mChat.cached('body').find(bbCodeClass).remove();
		});

		if (mChat.maxInputHeight) {
			mChat.cached('input').one('focus', function() {
				autosize(this);
			});
		}

		mChat.cached('form').on('submit', function(e) {
			e.preventDefault();
		}).on('keypress', function(e) {
			mChat.onKeyPress(e, {
				'submit': function(e) {
					e.preventDefault();
					mChat.add();
				},
				'newline': function() {
					mChat.appendText('\n');
				}
			});
		});

		if (mChat.showCharCount || mChat.mssgLngth) {
			mChat.cached('form').on('input', mChat.updateCharCount);
			mChat.cached('input').on('focus', function() {
				setTimeout(mChat.updateCharCount, 1);
			});
		}
	}

	$('#phpbb').on('click', '[data-mchat-action]', function(e) {
		e.preventDefault();
		var action = $(this).data('mchat-action');
		mChat[action].call(this, e);
	}).on('click', '#mchat-messages blockquote [data-post-id]', function(e) {
		e.preventDefault();
		mChat.jumpToMessage.call(this);
	});
});

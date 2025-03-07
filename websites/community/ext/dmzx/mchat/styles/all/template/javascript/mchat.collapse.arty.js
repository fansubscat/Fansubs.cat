/**
 *
 * mchat.collapse.js - Collapse mChat container for styles by Arty
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2017 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 *
 */

jQuery(function($) {

	"use strict";

	// Most code below copied from elegance/template/forum_fn.js for phpBB 3.2.2

	var $this = $('.mchat-wrapper li.header').first(),
		forum = $this.closest('.topiclist').next(),
		id = 'mchat',
		toggled = false,
		toggle;

	// Add toggle code
	$this.append('<a class="forum-toggle" href="#"></a>');
	toggle = $this.find('.forum-toggle');
	toggle.click(function(event) {
		event.preventDefault();
		$(mChat).trigger('mchat_collapse_toggle_before', [{collapsed: !toggle}]);
		if (toggled) {
			forum.stop(true, true).slideDown(200, function() {
				$(mChat).trigger('mchat_collapse_toggle_after', [{collapsed: toggle}]);
			});
			toggled = false;
			toggle.removeClass('toggled');
			phpbb.deleteCookie('toggled-' + id, styleConfig.cookieConfig);
			return;
		}
		forum.stop(true, true).slideUp(200, function() {
			$(mChat).trigger('mchat_collapse_toggle_after', [{collapsed: toggle}]);
		});
		toggled = true;
		toggle.addClass('toggled');
		phpbb.setCookie('toggled-' + id, '1', styleConfig.cookieConfig);
	});

	// Check default state
	if (phpbb.getCookie('toggled-' + id, styleConfig.cookieConfig) === '1') {
		forum.stop(true, true).slideUp(0);
		toggled = true;
		toggle.addClass('toggled');
	}

});

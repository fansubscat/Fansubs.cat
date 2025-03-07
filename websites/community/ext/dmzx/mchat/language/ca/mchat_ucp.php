<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters for use
// ’ » “ ” …

$lang = array_merge($lang, [
	'MCHAT_PREFERENCES'				=> 'mChat preferences',
	'MCHAT_NO_SETTINGS'				=> 'You are not authorised to customise any settings.',

	'MCHAT_INDEX'					=> 'Display on the index page',
	'MCHAT_SOUND'					=> 'Enable sound',
	'MCHAT_WHOIS_INDEX'				=> 'Display <em>Who is chatting</em> below the chat',
	'MCHAT_STATS_INDEX'				=> 'Display <em>Who is chatting</em> in the stats section',
	'MCHAT_STATS_INDEX_EXPLAIN'		=> 'Displays who is chatting below the <em>Who is online</em> section on the index page.',
	'MCHAT_AVATARS'					=> 'Display avatars',
	'MCHAT_CAPITAL_LETTER'			=> 'Capital first letter in your messages',
	'MCHAT_POSTS'					=> 'Display new posts (currently all disabled, can be enabled in the mChat Global Settings section in the ACP)',
	'MCHAT_DISPLAY_CHARACTER_COUNT'	=> 'Display number of characters when typing a message',
	'MCHAT_RELATIVE_TIME'			=> 'Display relative time for new messages',
	'MCHAT_RELATIVE_TIME_EXPLAIN'	=> 'Displays “just now”, “1 minute ago” and so on for each message. Set to <em>No</em> to always display the full date.',
	'MCHAT_MESSAGE_TOP'				=> 'Location of new chat messages',
	'MCHAT_MESSAGE_TOP_EXPLAIN'		=> 'New messages will appear at the top or at the bottom in the chat.',
	'MCHAT_LOCATION'				=> 'Location on the index page',
	'MCHAT_BOTTOM'					=> 'Bottom',
	'MCHAT_TOP'						=> 'Top',

	'MCHAT_POSTS_TOPIC'				=> 'Display new topics',
	'MCHAT_POSTS_REPLY'				=> 'Display new replies',
	'MCHAT_POSTS_EDIT'				=> 'Display edited posts',
	'MCHAT_POSTS_QUOTE'				=> 'Display quoted posts',
	'MCHAT_POSTS_LOGIN'				=> 'Display user logins',

	'MCHAT_DATE_FORMAT'				=> 'Date format',
	'MCHAT_DATE_FORMAT_EXPLAIN'		=> 'The syntax used is identical to the PHP <a href="http://www.php.net/date">date()</a> function.',
	'MCHAT_CUSTOM_DATEFORMAT'		=> 'Custom…',
]);

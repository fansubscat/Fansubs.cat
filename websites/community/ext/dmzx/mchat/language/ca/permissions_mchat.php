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
	'ACL_U_MCHAT_USE'						=> 'Can use mChat',
	'ACL_U_MCHAT_VIEW'						=> 'Can view mChat',
	'ACL_U_MCHAT_EDIT'						=> 'Can edit own messages',
	'ACL_U_MCHAT_DELETE'					=> 'Can delete own messages',
	'ACL_U_MCHAT_MODERATOR_EDIT'			=> 'Can edit anyone’s messages',
	'ACL_U_MCHAT_MODERATOR_DELETE'			=> 'Can delete anyone’s messages',
	'ACL_U_MCHAT_IP'						=> 'Can view IP addresses',
	'ACL_U_MCHAT_PM'						=> 'Can use private message',
	'ACL_U_MCHAT_LIKE'						=> 'Can see like icon (requires BBCode permission)',
	'ACL_U_MCHAT_QUOTE'						=> 'Can see quote icon (requires BBCode permission)',
	'ACL_U_MCHAT_FLOOD_IGNORE'				=> 'Can ignore flood limits',
	'ACL_U_MCHAT_ARCHIVE'					=> 'Can view the archive',
	'ACL_U_MCHAT_BBCODE'					=> 'Can use BBCodes',
	'ACL_U_MCHAT_SMILIES'					=> 'Can use smilies',
	'ACL_U_MCHAT_URLS'						=> 'Can post automatically parsed URLs',

	'ACL_U_MCHAT_AVATARS'					=> 'Can customise <em>Display avatars</em>',
	'ACL_U_MCHAT_CAPITAL_LETTER'			=> 'Can customise <em>Capital first letter</em>',
	'ACL_U_MCHAT_CHARACTER_COUNT'			=> 'Can customise <em>Display number of characters</em>',
	'ACL_U_MCHAT_DATE'						=> 'Can customise <em>Date format</em>',
	'ACL_U_MCHAT_INDEX'						=> 'Can customise <em>Display on index</em>',
	'ACL_U_MCHAT_LOCATION'					=> 'Can customise <em>Location of mChat on the index page</em>',
	'ACL_U_MCHAT_MESSAGE_TOP'				=> 'Can customise <em>Location of new chat messages</em>',
	'ACL_U_MCHAT_POSTS'						=> 'Can customise <em>Display new posts</em>',
	'ACL_U_MCHAT_RELATIVE_TIME'				=> 'Can customise <em>Display relative time</em>',
	'ACL_U_MCHAT_SOUND'						=> 'Can customise <em>Play sounds</em>',
	'ACL_U_MCHAT_WHOIS_INDEX'				=> 'Can customise <em>Display who is chatting below the chat</em>',
	'ACL_U_MCHAT_STATS_INDEX'				=> 'Can customise <em>Display who is chatting in the stats section</em>',

	'ACL_A_MCHAT'							=> 'Can manage mChat settings',
]);

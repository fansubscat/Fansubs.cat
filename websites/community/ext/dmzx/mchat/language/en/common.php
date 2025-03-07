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
	'MCHAT_TITLE'					=> 'mChat',
	'MCHAT_TITLE_COUNT'				=> [
		0 => 'mChat',
		1 => 'mChat [<strong>%1$d</strong>]',
	],
	'MCHAT_NAVBAR_CUSTOM_PAGE'		=> 'mChat page',
	'MCHAT_NAVBAR_ARCHIVE'			=> 'Archive',
	'MCHAT_NAVBAR_RULES'			=> 'Rules',

	// Who is chatting
	'MCHAT_WHO_IS_CHATTING'			=> 'Who is chatting',
	'MCHAT_ONLINE_USERS_TOTAL'		=> [
		0 => 'No one is chatting',
		1 => '<strong>%1$d</strong> user is chatting',
		2 => '<strong>%1$d</strong> users are chatting',
	],
	'MCHAT_ONLINE_EXPLAIN'			=> 'based on users active over the past %1$s',
	'MCHAT_HOURS'					=> [
		1 => '%1$d hour',
		2 => '%1$d hours',
	],
	'MCHAT_MINUTES'					=> [
		1 => '%1$d minute',
		2 => '%1$d minutes',
	],
	'MCHAT_SECONDS'					=> [
		1 => '%1$d second',
		2 => '%1$d seconds',
	],

	// Custom translations for administrators
	'MCHAT_RULES_MESSAGE'			=> '',
	'MCHAT_STATIC_MESSAGE'			=> '',

	// Post notification messages (%1$s is replaced with a link to the new/edited post, %2$s is replaced with a link to the forum)
	'MCHAT_NEW_POST'				=> 'posted a new topic: %1$s in %2$s',
	'MCHAT_NEW_POST_DELETED'		=> 'posted a new topic that was deleted',
	'MCHAT_NEW_REPLY'				=> 'posted a reply: %1$s in %2$s',
	'MCHAT_NEW_REPLY_DELETED'		=> 'posted a reply that was deleted',
	'MCHAT_NEW_QUOTE'				=> 'replied with a quote: %1$s in %2$s',
	'MCHAT_NEW_QUOTE_DELETED'		=> 'posted a reply that was deleted',
	'MCHAT_NEW_EDIT'				=> 'edited a post: %1$s in %2$s',
	'MCHAT_NEW_EDIT_DELETED'		=> 'edited a post that was deleted',
	'MCHAT_NEW_LOGIN'				=> 'just logged in',
]);

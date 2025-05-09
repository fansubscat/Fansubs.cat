<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
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

$lang = array_merge($lang, array(
	'ACTIVE_TOPICS'			=> 'Temes actius',
	'ANNOUNCEMENTS'			=> 'Avisos',

	'FORUM_PERMISSIONS'		=> 'Permisos del fòrum',

	'ICON_ANNOUNCEMENT'		=> 'Avís',
	'ICON_STICKY'			=> 'Tema recurrent',

	'LOGIN_NOTIFY_FORUM'	=> 'T’han informat d’aquest fòrum. Inicia la sessió per a veure’l.',

	'MARK_TOPICS_READ'		=> 'Marca els temes com a llegits',

	'NEW_POSTS_HOT'			=> 'Hi ha missatges nous [Popular]',	// Not used anymore
	'NEW_POSTS_LOCKED'		=> 'Hi ha missatges nous [Blocat]',	// Not used anymore
	'NO_NEW_POSTS_HOT'		=> 'No hi ha missatges nous [Popular]',	// Not used anymore
	'NO_NEW_POSTS_LOCKED'	=> 'No hi ha missatges nous [Blocat]',	// Not used anymore
	'NO_READ_ACCESS'		=> 'No tens els permisos necessaris per a veure o llegir els temes d’aquest fòrum.',
	'NO_FORUMS_IN_CATEGORY'	=> 'Aquesta categoria no té cap fòrum.',
	'NO_UNREAD_POSTS_HOT'		=> 'No hi ha missatges no llegits [Popular]',
	'NO_UNREAD_POSTS_LOCKED'	=> 'No hi ha missatges no llegits [Blocat]',

	'POST_FORUM_LOCKED'		=> 'El fòrum està blocat',

	'TOPICS_MARKED'			=> 'Els temes d’aquest fòrum s’han marcat com a llegits.',

	'UNREAD_POSTS_HOT'		=> 'Missatges no llegits [Popular]',
	'UNREAD_POSTS_LOCKED'	=> 'Missatges no llegits [Bloquejat]',

	'VIEW_FORUM'			=> 'Mostra el fòrum',
	'VIEW_FORUM_TOPICS'		=> array(
		1	=> '%d tema',
		2	=> '%d temes',
	),
));

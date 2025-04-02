<?php
/**
 *
 * Thanks For Posts.
 * Adds the ability to thank the author and to use per posts/topics/forum rating system based on the count of thanks.
 * An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, rxu, https://www.phpbbguru.net
 * @license GNU General Public License, version 2 (GPL-2.0)
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
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'CLEAR_LIST_THANKS'			=> 'Clear Thanks List',
	'CLEAR_LIST_THANKS_CONFIRM'	=> 'Do you really want to clear the user`s Thanks List?',
	'CLEAR_LIST_THANKS_GIVE'	=> 'List of thanks issued by the user was cleared.',
	'CLEAR_LIST_THANKS_POST'	=> 'List of thanks in the message was cleared.',
	'CLEAR_LIST_THANKS_RECEIVE'	=> 'List of thanks obtained by the user was cleared.',

	'DISABLE_REMOVE_THANKS'		=> 'Deleting thanks has been disabled by the administrator',

	'GIVEN'						=> 'Ha agraït',
	'GLOBAL_INCORRECT_THANKS'	=> 'You cannot give thanks for a Global Announcement that has no reference to a particular forum.',
	'GRATITUDES'				=> 'Thanks list',

	'INCORRECT_THANKS'			=> 'Invalid thank',

	'JUMP_TO_FORUM'				=> 'Jump to forum',
	'JUMP_TO_TOPIC'				=> 'Jump to topic',

	'FOR_MESSAGE'				=> ' for post',
	'FURTHER_THANKS'     	    => [
		1 => ' and one more user',
		2 => ' and %d more users',
	],

	'NO_VIEW_USERS_THANKS'		=> 'You are not authorised to view the Thanks List.',

	'NOTIFICATION_THANKS_GIVE'	=> [
		1 => '%1$s <strong>t’ha agraït</strong> aquest missatge:',
		2 => '%1$s <strong>t’ha agraït</strong> aquest missatge:',
	],
	'NOTIFICATION_THANKS_REMOVE'=> [
		1 => '%1$s <strong>ha eliminat l’agraïment</strong> del missatge:',
		2 => '%1$s <strong>ha eliminat l’agraïment</strong> del missatge:',
	],
	'NOTIFICATION_TYPE_THANKS_GIVE'		=> 'Algú t’agraeix un missatge',
	'NOTIFICATION_TYPE_THANKS_REMOVE'	=> 'Algú elimina l’agraïment d’un missatge',

	'RECEIVED'					=> 'Agraït',
	'REMOVE_THANKS'				=> 'Elimina l’agraïment: ',
	'REMOVE_THANKS_CONFIRM'		=> 'Segur que vols eliminar l’agraïment?',
	'REMOVE_THANKS_SHORT'		=> 'Elimina l’agraïment',
	'REPUT'						=> 'Rating',
	'REPUT_TOPLIST'				=> 'Thanks Toplist — %d',
	'RATING_LOGIN_EXPLAIN'		=> 'You are not authorised to view the toplist.',
	'RATING_NO_VIEW_TOPLIST'	=> 'You are not authorised to view the toplist.',
	'RATING_VIEW_TOPLIST_NO'	=> 'Toplist is empty or disabled by administrator',
	'RATING_FORUM'				=> 'Forum',
	'RATING_POST'				=> 'Post',
	'RATING_TOP_FORUM'			=> 'Rating forums',
	'RATING_TOP_POST'			=> 'Rating posts',
	'RATING_TOP_TOPIC'			=> 'Rating topics',
	'RATING_TOPIC'				=> 'Topic',

	'THANK'						=> 'vegada',
	'THANK_FROM'				=> 'de',
	'THANK_TEXT_1'				=> 'Els usuaris següents han agraït el missatge de ',
	'THANK_TEXT_2'				=> ': ',
	'THANK_TEXT_2PL'			=> ' (%d en total):',
	'THANK_POST'				=> 'Agraeix el missatge a ',
	'THANK_POST_SHORT'			=> 'Agraïment',
	'THANKS'					=> [
		1	=> '%d time',
		2	=> '%d times',
	],
	'THANKS_BACK'				=> 'Torna',
	'THANKS_INFO_GIVE'			=> 'Has agraït el missatge.',
	'THANKS_INFO_REMOVE'		=> 'Has eliminat l’agraïemnt.',
	'THANKS_LIST'				=> 'Mostra o amaga la llista',
	'THANKS_PM_MES_GIVE'		=> 't’ha agraït el missatge',
	'THANKS_PM_MES_REMOVE'		=> 'ha eliminat l’agraïment del missatge',
	'THANKS_PM_SUBJECT_GIVE'	=> 'Agraïment pel missatge',
	'THANKS_PM_SUBJECT_REMOVE'	=> 'Agraïment eliminat pel missatge:',
	'THANKS_USER'				=> 'List of thanks',
	'TOPLIST'					=> 'Posts toplist',
]);

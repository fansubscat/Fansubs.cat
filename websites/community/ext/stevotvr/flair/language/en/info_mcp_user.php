<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'MCP_FLAIR'			=> 'Profile Flair',
	'MCP_FLAIR_EXPLAIN'	=> 'Here you can manage %s’s profile flair.<ul><li>Click the <b>Set</b> button to set the count to the specified value.</li><li>Click the <b>&times;</b> button to remove an item.</li><li>Click the <b>+</b> button to add an item with the specified count.</li></ul>',

	'MCP_FLAIR_USER'	=> 'Manage user’s flair',
	'MCP_FLAIR_FRONT'	=> 'Front page',

	'MCP_FLAIR_USER_FLAIR'		=> '%s’s flair',
	'MCP_FLAIR_AVAILABLE'		=> 'Available flair',
	'MCP_FLAIR_NO_FLAIR'		=> 'No flair is assigned to this user’s profile.',
	'MCP_FLAIR_NO_AVAILABLE'	=> 'There are no flair items available.',
	'MCP_FLAIR_ADD_TITLE'		=> 'Add “%1$s” to %2$s’s profile',
	'MCP_FLAIR_SET_COUNT_TITLE'	=> 'Set the count of “%1$s” on %2$s’s profile',
	'MCP_FLAIR_REMOVE_TITLE'	=> 'Remove “%1$s” from %2$s’s profile',
	'MCP_FLAIR_REMOVE_CONFIRM'	=> 'Are you sure you wish to remove this item?',

	'MCP_FLAIR_SET'	=> 'Set',
));

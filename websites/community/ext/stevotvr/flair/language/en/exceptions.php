<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
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
	'EXCEPTION_OUT_OF_BOUNDS'		=> 'The field `%s` received a value out of its range.',
	'EXCEPTION_INVALID_COLOR'		=> 'The field `%s` received an invalid color value. A valid color is a 6 character hexadecimal string (0-9, A-F).',
	'EXCEPTION_TOO_LONG'			=> 'The field `%s` received a value longer than the maximum length.',
	'EXCEPTION_NAME_REQUIRED'		=> 'You must enter a name for the flair item.',
	'EXCEPTION_CAT_NAME_REQUIRED'	=> 'You must enter a name for the category.',
	'EXCEPTION_IMG_CONFLICT'		=> 'An image with that name already exists.',
	'EXCEPTION_IMG_PROCESSING'		=> 'An error occurred while processing the image.',

	'EXCEPTION_FIELD_FLAIR_COLOR'		=> 'Flair color',
	'EXCEPTION_FIELD_FLAIR_ICON_COLOR'	=> 'Flair icon color',
	'EXCEPTION_FIELD_FLAIR_FONT_COLOR'	=> 'Flair font color',
));

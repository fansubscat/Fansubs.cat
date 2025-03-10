<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019 phpBB Limited <https://www.phpbb.com>
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
	'PHPBB_VERSION_ERROR'	=> 'Tu foro parece estar usando una versión anterior de phpBB. Se requiere phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' o posterior para usar esta extensión.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Detectamos la extensión s9e/mediaembed. El complemento Media Embed de phpBB no se puede instalar hasta que deshabilites, purgues y elimines todos los archivos relacionados con la extensión s9e/mediaembed.',
]);

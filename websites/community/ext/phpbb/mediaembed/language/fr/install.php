<?php
/**
 *
 * phpBB Media Embed PlugIn. An extension for the phpBB Forum Software package.
 * French translation by Galixte (http://www.galixte.com)
 *
 * @copyright (c) 2018 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0-only)
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
// ’ « » “ ” …
//

$lang = array_merge($lang, [
	'PHPBB_VERSION_ERROR'	=> 'Il semble que le forum n’est pas à jour, une ancienne version de phpBB est installée. phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' ou une version plus récente est requise pour utiliser cette extension.',
	'S9E_MEDIAEMBED_ERROR'	=> 'L’extension « s9e/mediaembed » a été détectée, celle-ci est installée sur le forum. L’extension « phpBB Media Embed PlugIn » ne peut être installée tant que l’extension « <a href="https://www.phpbb.com/community/viewtopic.php?f=456&t=2272431">s9e/mediaembed</a> » est activée. Ainsi, il est nécessaire de la désactiver puis de supprimer tous ses fichiers sur son FTP.',
]);

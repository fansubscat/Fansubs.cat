<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
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
	'HELP_EMBEDDING_MEDIA'			=> 'Vkládání médií',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Jak vkládat média z jiných stránek do příspěvků.',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Uživatelů mohou vkládat obsah jako jsou videa nebo zvukové stopy z povolených stránek používáním <strong>[media][/media]</strong> nebo jednoduše vložením povoleného URL do textu. Například:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Jak bylo výše zmíněno, odkaz může být také vložen pomocí
										<strong>[media]</strong> tagů.
										<br /><br />Uvedený příklad bude vypadat následovně:<br /><br />%2$s
										<br /><br />Tyto stránky jsou podporovány:<br /><samp>%3$s.</samp>
										<br /><br />Pro kompletní dokumentaci podporovaných tránek a příklady URL navštivte <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin dokumentace</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

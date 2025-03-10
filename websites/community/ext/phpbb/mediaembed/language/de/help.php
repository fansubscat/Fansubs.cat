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
	'HELP_EMBEDDING_MEDIA'			=> 'Embedding Media',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Wie werden Medien von anderen Seiten in Posts eingebunden',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'User können Medien von anderen erlaubten Seiten wie Video und Audio einbinden indem
										die <strong>[media][/media]</strong> Tags oder indem einfach die reine URL der erlaubten Seite
										in den Text kopiert wird. Als Beispiel:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Wie oben beschrieben kann die URL auch ohne die
										<strong>[media]</strong> Tags verwendet werden.
										<br /><br />Das hier gezeigt Beispiel würde folgendes generieren:<br /><br />%2$s
										<br /><br />Die folgenden Seiten sind erlaubt:<br /><samp>%3$s.</samp>
										<br /><br />Für eine komplette Dokumentation der unterstützen Seiten und Beispiel URLs,
										besuche die <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentation</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

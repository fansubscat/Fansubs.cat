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
	'HELP_EMBEDDING_MEDIA'			=> 'Indlejring af medie',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Sådan indlejres medie fra andre steder i indlæg',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Brugere kan indlejre indhold, såsom videoer og lyd fra tilladte stedet med
										<strong>[media][/media]</strong>-tags, eller blot ved at skrive en understøttet
										URL i ren tekst. F.eks.:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Som beskrevet ovenfor, kan linket også bruges uden
										<strong>[media]</strong>-taggene.
										<br /><br />Eksemplet som vises her genererer:<br /><br />%2$s
										<br /><br />Følgende steder understøttes:<br /><samp>%3$s.</samp>
										<br /><br />Besøg <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										dokumentationen til MediaEmbed Plugin</a> for fuld dokumentation på understøttede steder og
										URL\'er med eksempler.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

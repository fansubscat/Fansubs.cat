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
	'HELP_EMBEDDING_MEDIA'			=> 'Media Invoegen',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Hoe voeg je media van andere website in in je berichten',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Gebruikers kunnen inhoud zoals video\'s en audo invoegen van toegestane websites met gebruik van
										de <strong>[media][/media]</strong> tags, of door heel simpel het plaatsen van een ondersteunde
										URL in platte tekst. Als voorbeeld:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Zoals we zien hierboven kan de link ook worden gebruikt zonder de
										<strong>[media]</strong> tags.
										<br /><br />Bovenstaand voorbeeld geeft als resultaat:<br /><br />%2$s
										<br /><br />De volgende websites worden ondersteund:<br /><samp>%3$s.</samp>
										<br /><br />Voor de volledige documentatie op ondersteunde websites en voorbeeld URL\'s,
										bezoekt u de <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentatie</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

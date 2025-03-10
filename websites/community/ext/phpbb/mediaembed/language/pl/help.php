<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Polska wersja językowa phpBB Media Embed 1.1.2 - 10.09.2020, Mateusz Dutko (vader) www.rnavspotters.pl
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
	'HELP_EMBEDDING_MEDIA'			=> 'Osadzanie multimediów',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Jak wyświetlić multimedia z innych stron na forum',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Użytkownicy mogą wyświetlić zawartość z innych stron jak wideo lub muzyka
										poprzez znacznik <strong>[media][/media]</strong>. Należy wkleić adres URL pomiędzy znacznikami.
										<br /><br />Na przykład:
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Powyższy adres URL zostanie wyświetlony jak pokazano poniżej:<br /><br />%2$s
										<br /><br />Na forum wspierane są strony:<br /><samp>%3$s.</samp>
										<br /><br />Dokumentacja wraz z wszystkimi wspieranymi stronami i przykładami adresów URL
										znajduje się pod linkiem <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentation</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

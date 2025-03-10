<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Slovak translation by Senky (https://github.com/senky)
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
	'HELP_EMBEDDING_MEDIA'			=> 'Vkladanie médií',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Ako vložiť obsah z inej stránky do príspevku',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Používatelia môžu vložiť obsah (napríklad video alebo zvuk z povolených stránok použítím
										<strong>[media][/media]</strong> kódu alebo jednoducho odoslať podporovanú
										URL v texte. Napríklad:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Ako je uvedené vyššie, odkaz môže byť použitý bez
										<strong>[media]</strong> kódu.
										<br /><br />Príklad zobrazený tuto vygeneruje:<br /><br />%2$s
										<br /><br />Podporované sú tieto stránky:<br /><samp>%3$s.</samp>
										<br /><br />Pre kompletnú dokumentáciu ohľadom podporovaných stránok a príkladov adries
										navštívte <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										dokumentáciu pluginu vkladania médií</a> (v Angličtine).',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

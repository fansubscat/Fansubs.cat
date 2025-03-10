<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Italian language By alex75 https://www.phpbb-store.it
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
	'HELP_EMBEDDING_MEDIA'			=> 'Incorporamento dei media',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Come incorporare i contenuti multimediali da altri siti nei messaggi',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Gli utenti possono incorporare contenuti come video e audio dai siti consentiti utilizzando
										i tags <strong>[media][/media]</strong>, o semplicemente postando in testo normale un URL
                                        che lo supporta. Per esempio:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Come notato sopra, il collegamento potrebbe anche essere utilizzato senza
										<strong>[media]</strong> tags.
										<br /><br />L’esempio mostrato qui genererebbe:<br /><br />%2$s
										<br /><br />Sono supportati i seguenti siti:<br /><samp>%3$s.</samp>
										<br /><br />Per la documentazione completa sui siti supportati e sugli URL di esempio,
										consulta la <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										Documentazione sul plug-in multimediale incorporato</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

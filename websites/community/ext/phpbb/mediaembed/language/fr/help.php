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
	'HELP_EMBEDDING_MEDIA'			=> 'Intégration de médias',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Intégrer un média dans les messages provenant d’un service d’un autre site Web',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Les utilisateurs peuvent intégrer du contenu multimédia (audio, vidéo) provenant de services fournis
										par d’autres sites Web en utilisant les balises du BBCode <strong>[media][/media]</strong>,
										ou en publiant simplement l’adresse URL d’un lien pris en charge. Tel que, par exemple :<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Comme indiqué précédemment, le lien peut aussi être utilisé sans
										les balises du BBCode <strong>[media]</strong>.
										<br /><br />Ainsi, l’exemple montré ci-dessus affichera :<br /><br />%2$s
										<br /><br />Le services des sites Web suivants sont supportés :<br /><samp>%3$s.</samp>
										<br /><br />Une documentation complète à propos des services et des exemples d’adresses URL pris en charge,
										est disponible depuis la page : <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										Sites pris en charges par le plugin MediaEmbed</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 1.0.1] (https://github.com/phpBBTraducoes)
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
	'HELP_EMBEDDING_MEDIA'			=> 'Incorporando mídia',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Como inserir mídia de outros sites em postagens',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Os usuários podem incorporar conteúdo como vídeos e áudio de sites permitidos usando
										as tags <strong>[media][/media]</strong>, ou simplesmente postando uma
										URL suportada em texto simples. Por exemplo:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Conforme mencionado acima, o link também pode ser usado sem as tags
										<strong>[media]</strong>.
										<br /><br />O exemplo mostrado aqui geraria:<br /><br />%2$s
										<br /><br />Os seguintes sites são suportados:<br /><samp>%3$s.</samp>
										<br /><br />Para obter documentação completa sobre sites suportados e URLs de exemplo,
										visite a <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentation</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

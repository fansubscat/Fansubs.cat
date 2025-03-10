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
	'HELP_EMBEDDING_MEDIA'			=> 'Incrustación de Medios',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Cómo insertar medios de otros sitios en los mensajes',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Los usuarios pueden incorporar contenido como videos y audio desde sitios permitidos usando
										las etiquetas <strong>[media][/media]</strong>, o simplemente publicando una URL soportada
										en texto plano. Por ejemplo:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Como se indicó anteriormente, el enlace también podría utilizarse sin las
										etiquetas <strong>[media]</strong>.
										<br /><br />El ejemplo mostrado aquí generaría:<br /><br />%2$s
										<br /><br />Los siguientes sitios son compatibles:<br /><samp>%3$s.</samp>
										<br /><br />Para obtener una documentación completa sobre los sitios admitidos, y las URL de ejemplo,
										visite la <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										Documentación del PlugIn MediaEmbed</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Russian translation by HD321kbps
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
	'HELP_EMBEDDING_MEDIA'			=> 'Встраивание мультимедиа',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Как вставлять медиа с других сайтов в сообщения?',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Пользователи могут вставлять контент, например, видео и аудио с разрешенных сайтов, используя бб-код
										<strong>[media][/media]</strong>, или просто вставив поддерживаемую ссылку в текстовом формате. Например:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Как говорилось выше, ссылка также может использоваться без бб-кода <strong>[media]</strong>.
										<br /><br />Пример, показанный здесь, будет генерировать:<br /><br />%2$s
										<br /><br />Поддерживаются следующие сайты::<br /><samp>%3$s.</samp>
										<br /><br />Полную документацию о поддерживаемых сайтах и ​​URL-адресах смотрите на странице <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										Документация MediaEmbed</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

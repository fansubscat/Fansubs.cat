<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @简体中文语言　David Yin <https://www.phpbbchinese.com/>
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
	'HELP_EMBEDDING_MEDIA'			=> '嵌入的媒体',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> '如何在帖子中嵌入其它网站的媒体内容',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> '用户可以嵌入来自所允许的网站的视频和音频内容，使用
										 <strong>[media][/media]</strong> 标签， 或者直接以文本贴入支持的网址 URL
										 例如：<br><br>
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br><br>注意上面，链接也可以直接使用，而不带
										<strong>[media]</strong> tags.
										<br><br>这个例子会生成：<br><br>%2$s
										<br><br>下面这些网站可以使用：<br><samp>%3$s.</samp>
										<br><br>访问 <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentation</a> 可以查看完整的文档，包括支持的网站和网址列子。',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @正體中文化 竹貓星球 <http://phpbb-tw.net/phpbb/>
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
	'HELP_EMBEDDING_MEDIA'			=> '嵌入媒體',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> '如何將來自其他網站的媒體嵌入到文章中',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> '會員可以使用 <strong>[media][/media]</strong> 標籤嵌入來自允許網站的視頻和音頻等內容，或者簡單地以純文本形式發表受支持的網址。例如：
										<br /><br /><strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />如上所述，連結也可以在沒有 <strong>[media]</strong> 標籤的情況下使用。
										<br /><br />此處顯示的範例將生成：<br /><br />%2$s
										<br /><br />支持以下站點：<br /><samp>%3$s</samp>。
										<br /><br />有關受支持站點和範例網址的完整文件，參訪 <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">MediaEmbed Plugin Documentation</a>。',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

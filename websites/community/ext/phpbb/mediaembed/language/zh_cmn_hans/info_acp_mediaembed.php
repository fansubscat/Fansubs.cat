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

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'ACP_PHPBB_MEDIA_EMBED'				=> '嵌入的媒体',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> '管理网站',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> '设置',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Media Embed 缓存清理</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>可嵌入媒体网站更新完毕</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>媒体嵌入设置更新完毕</strong>',
]);

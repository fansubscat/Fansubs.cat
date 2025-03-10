<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @正體中文化 竹貓星球 <http://phpbb-tw.net/phpbb/>
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
	'ACP_PHPBB_MEDIA_EMBED'				=> '媒體嵌入',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> '管理站點',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> '設定',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>媒體嵌入快取已清除</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>媒體嵌入站點已更新</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>媒體嵌入設定已更新</strong>',
]);

<?php
/**
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
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
	'PHPBB_VERSION_ERROR'	=> '您的论坛似乎正在使用旧版 phpBB。使用此扩展需要 phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' 或更高版本。',
	'S9E_MEDIAEMBED_ERROR'	=> '检测到 s9e/mediaembed 扩展。您必须先禁用并清除 s9e/mediaembed 扩展的数据，再删除与其相关的所有文件，才能安装 phpBB 的 Media Embed 插件。',
]);

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
	'PHPBB_VERSION_ERROR'	=> 'يبدو أن منتداك يستخدم إصداراً أقدم من phpBB. يلزم phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' أو أحدث لاستخدام هذه الإضافة.',
	'S9E_MEDIAEMBED_ERROR'	=> 'اكتشفنا إضافة s9e/mediaembed. لا يمكن تثبيت إضافة Media Embed الخاصة بـ phpBB حتى تعطل إضافة s9e/mediaembed وتمسح بياناتها وتحذف جميع الملفات المتعلقة بها.',
]);

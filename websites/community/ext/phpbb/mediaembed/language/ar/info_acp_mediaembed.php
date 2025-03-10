<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Bassel Taha Alhitary <http://alhitary.net>
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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'إدراج مُحتوى الوسائط',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'إدارة المواقع',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'الإعدادات',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Media Embed cache purged</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>تم تحديث قائمة المواقع في “إدراج مُحتوى الوسائط”</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>تم تحديث إعدادات “إدراج مُحتوى الوسائط”</strong>',
]);

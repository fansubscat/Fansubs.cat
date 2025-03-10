<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Turkish translation by ESQARE (https://www.phpbbturkey.com)
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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'Medya (Ortam) Yerleştirme',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'Siteleri yönet',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'Ayarlar',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Medya (Ortam) Yerleştirme önbelleği temizlendi</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>Medya (Ortam) Yerleştirme siteleri güncellendi</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>Medya (Ortam) Yerleştirme ayarları güncellendi</strong>',
]);

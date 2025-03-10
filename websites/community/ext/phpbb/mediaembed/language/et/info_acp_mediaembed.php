<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'Manustatud meedia',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'Halda veebilehti',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'Seaded',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Media Embed cache purged</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>Uuendati laienduse manustatud meedia veebilehti</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>Manustatud meedia seaded on uuendatud</strong>',
]);

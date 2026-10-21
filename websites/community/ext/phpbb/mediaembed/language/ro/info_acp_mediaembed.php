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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'Media Embed',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'Gestionați site-urile',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'Setări',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Memoria cache pentru Media Embed a fost eliminată</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>Site-urile Media Embed au fost actualizate</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>Setările pentru Media Embed au fost actualizate</strong>',
]);

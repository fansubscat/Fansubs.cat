<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Italian language By alex75 https://www.phpbb-store.it
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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'Incorpora i media',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'Gestisci siti',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'Impostazioni',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Media Embed cache purged</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>I siti con i  Media Incorporati sono stati Aggiornati</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>Impostazioni PlugIn Media Embed aggiornate</strong>',
]);

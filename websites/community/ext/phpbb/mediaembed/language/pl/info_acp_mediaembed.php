<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Polska wersja językowa phpBB Media Embed 1.1.2 - 10.09.2020, Mateusz Dutko (vader) www.rnavspotters.pl
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
	'ACP_PHPBB_MEDIA_EMBED'				=> 'Osadzanie multimediów',
	'ACP_PHPBB_MEDIA_EMBED_MANAGE'		=> 'Zarządzaj stronami',
	'ACP_PHPBB_MEDIA_EMBED_SETTINGS'	=> 'Ustawienia',

	// Log keys
	'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED'=> '<strong>Wyczyszczono pamięć podręczną Media Embed</strong>',
	'LOG_PHPBB_MEDIA_EMBED_MANAGE'		=> '<strong>Zaktualizowano strony Media Embed</strong>',
	'LOG_PHPBB_MEDIA_EMBED_SETTINGS'	=> '<strong>Zmieniono ustawienia Media Embed</strong>',
]);

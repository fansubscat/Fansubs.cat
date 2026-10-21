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
	'PHPBB_VERSION_ERROR'	=> 'La tua board sembra utilizzare una versione precedente di phpBB. Per usare questa estensione è richiesto phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' o successivo.',
	'S9E_MEDIAEMBED_ERROR'	=> 'È stata rilevata l’estensione s9e/mediaembed. Il plugin Media Embed di phpBB non può essere installato finché non disabiliti e rimuovi i dati e tutti i file relativi all’estensione s9e/mediaembed.',
]);

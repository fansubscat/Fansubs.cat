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
	'PHPBB_VERSION_ERROR'	=> 'Dein Board verwendet offenbar eine ältere phpBB-Version. Für diese Erweiterung ist phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' oder neuer erforderlich.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Die Erweiterung s9e/mediaembed wurde erkannt. Das Media-Embed-Plugin von phpBB kann erst installiert werden, nachdem du s9e/mediaembed deaktiviert, dessen Daten bereinigt und alle zugehörigen Dateien gelöscht hast.',
]);

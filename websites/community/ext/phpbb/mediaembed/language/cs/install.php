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
	'PHPBB_VERSION_ERROR'	=> 'Vaše fórum zřejmě používá starší verzi phpBB. Pro použití tohoto rozšíření je vyžadováno phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' nebo novější.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Bylo zjištěno rozšíření s9e/mediaembed. Doplněk Media Embed pro phpBB nelze nainstalovat, dokud rozšíření s9e/mediaembed nezakážete, neodstraníte jeho data a nesmažete všechny související soubory.',
]);

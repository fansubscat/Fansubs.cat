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
	'PHPBB_VERSION_ERROR'	=> 'Dit forum ser ud til at bruge en ældre version af phpBB. phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' eller nyere kræves for at bruge denne udvidelse.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Udvidelsen s9e/mediaembed blev fundet. phpBB’s Media Embed-plugin kan ikke installeres, før du har deaktiveret og slettet data og alle filer, der tilhører udvidelsen s9e/mediaembed.',
]);

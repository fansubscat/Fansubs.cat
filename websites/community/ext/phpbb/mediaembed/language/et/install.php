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
	'PHPBB_VERSION_ERROR'	=> 'Sinu foorum kasutab ilmselt phpBB vanemat versiooni. Selle laienduse kasutamiseks on vajalik phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' või uuem.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Tuvastasime laienduse s9e/mediaembed. phpBB Media Embedi lisandmoodulit ei saa paigaldada enne, kui oled s9e/mediaembedi keelanud, selle andmed kustutanud ja eemaldanud kõik sellega seotud failid.',
]);

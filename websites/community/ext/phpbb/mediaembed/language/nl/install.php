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
	'PHPBB_VERSION_ERROR'	=> 'Je forum lijkt een oudere versie van phpBB te gebruiken. phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' of nieuwer is vereist om deze extensie te gebruiken.',
	'S9E_MEDIAEMBED_ERROR'	=> 'De extensie s9e/mediaembed is gevonden. De Media Embed-plug-in van phpBB kan pas worden geïnstalleerd nadat je s9e/mediaembed hebt uitgeschakeld, de gegevens ervan hebt verwijderd en alle bijbehorende bestanden hebt gewist.',
]);

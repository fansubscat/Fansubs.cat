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
	'PHPBB_VERSION_ERROR'	=> 'Vaše fórum zrejme používa staršiu verziu phpBB. Na použitie tohto rozšírenia je potrebné phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' alebo novšie.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Bolo zistené rozšírenie s9e/mediaembed. Doplnok Media Embed pre phpBB nemožno nainštalovať, kým rozšírenie s9e/mediaembed nezakážete, neodstránite jeho údaje a všetky súvisiace súbory.',
]);

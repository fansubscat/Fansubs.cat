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
	'PHPBB_VERSION_ERROR'	=> 'Ваш форум использует устаревшую версию phpBB. Для работы этого расширения требуется phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' или новее.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Обнаружено расширение s9e/mediaembed. Плагин Media Embed для phpBB нельзя установить, пока вы не отключите расширение s9e/mediaembed, не удалите его данные и все связанные с ним файлы.',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Turkish translation by ESQARE (https://www.phpbbturkey.com)
 *
 * @copyright (c) 2019 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'PHPBB_VERSION_ERROR'	=> 'Mesaj panonuz phpBB’nin eski bir sürümünü kullanıyor gibi görünüyor. Bu eklentiyi kullanmak için phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' ya da daha yeni bir sürüm gereklidir.',
	'S9E_MEDIAEMBED_ERROR'	=> 's9e/mediaembed eklentisi tespit edildi. phpBB’nin Media (Ortam) yerleştirme eklentisi, s9e/mediaembed eklentisi ile alakalı tüm dosyalar silinip temizlenmedikçe kurulmayacaktır.',
]);

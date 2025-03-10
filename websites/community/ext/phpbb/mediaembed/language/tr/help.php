<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Turkish translation by ESQARE (https://www.phpbbturkey.com)
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
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
	'HELP_EMBEDDING_MEDIA'			=> 'Medya (Ortam) Yerleştirme',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'Diğer sitelerden alınan medya(ortam) içerikleri mesajların içerisine nasıl yerleştirilir',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Kullanıcılar izin verilen sitelerden aldığı video ve ses gibi içerikleri
										<strong>[media][/media]</strong> etiketlerini kullanarak, ya da düz metin içerisine desteklenen URL adreslerini basit bir şekilde ekleyerek
										mesajlara yerleştirebilir. Örneğin:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />Yukarıda belirtildiği gibi, <strong>[media]</strong> etiketleri olmadan
										sadece bağlantılar da kullanılabilir.
										<br /><br />Burada gösterilen örnek şu şekilde oluşturulacaktır:<br /><br />%2$s
										<br /><br />Şu siteler desteklenmektedir:<br /><samp>%3$s.</samp>
										<br /><br />Desteklenen siteler ve örnek URL adresleri hakkında tüm belgeler için,
										<a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Belgeleri</a> sayfasını ziyaret edin.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

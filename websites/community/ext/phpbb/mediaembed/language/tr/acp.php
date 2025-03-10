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

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Medya (Ortam) Yerleştirme Ayarları',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Buradan Medya (Ortam) Yerleştirme Eklentisi için ayarları yapabilirsiniz.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '<samp>[MEDIA]</samp> BBCode butonunu mesaj gönderme sayfasında göster',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'İzin verilmediği takdirde, BBCode butonu gösterilmeyecektir, ancak kullanıcılar hala <samp>[media]</samp> etiketini kendi mesajlarında kullanabilir',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Options',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Kullanıcı imzalarında izin ver',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Kullanıcı imzalarında yerleşik medya (ortam) içeriğine izin ver.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Content caching',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Medya (Ortam) Yerleştirme önbelleğini etkinleştir',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Bazı durumlarda diğer sitelerden medya (ortam) yüklerken, özellikle aynı içerik çok kez yüklenirken (örneğin, bir gönderiyi düzenlerken) normal performanstan daha yavaş olduğunu fark edebilirsiniz. Bu ayar etkinleştirildiğinde Medya (Ortam) Yerleştirme eklentisi, sitelerden topladığı bilgileri yerel olarak önbelleğe alacak ve performansı artıracaktır.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Düz URL adreslerini dönüştür',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Düz URL (<samp>[media]</samp> ya da <samp>[url]</samp> etiketleri arasına alınmamış) adreslerini yerleştirilmiş medya (ortam) içeriğine dönüştürmek için bu ayarı etkinleştirin. Not: Bu ayarın değiştirilmesi (mevcut gönderiler zaten ayrıştırılmış olduğundan) sadece yeni mesajları etkileyecektir.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Content sizing',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Enable full width content',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Enable this to expand most Media Embed content to fill the full width of the post content area while maintaining its native aspect ratio.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Custom max-width content',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Use this field to define custom max-width values for individual sites. This will override the default size and the full width option above. Enter each site on a new line, using the format <samp class="error">siteId:width</samp> with either <samp class="error">px</samp> or <samp class="error">%</samp>. For example:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Hover your mouse over a site on the Manage sites page to reveal the site id name to use here.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Medya (Ortam) Yerleştirme önbelleğini temizle',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Medya (Ortam) Yerleştirme önbelleği günde bir kez otomatik olarak zaten temizlenir. Yine de önbelleği hemen temizlemek isterseniz bu düğmeyi kullanarak elle (manuel) temizleme yapabilirsiniz.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Site id numarası: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Bu site mevcut olan bir BBCode ile çakışıyor: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'The following errors were encountered:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” is not a valid site id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” is not a valid width in “px” or “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Medya (Ortam) Yerleştirme Sitelerini Yönet',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Buradan, içeriğini göstermek için Medya (Ortam) Yerleştirme Eklentisine izin vermek istediğiniz siteleri yönetebilirsiniz.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Gösterilecek hiç bir medya (ortam) sitesi yok.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Aşağıdaki siteler artık desteklenmiyor veya çalışmıyor. Lütfen bu siteleri kaldırmak için bu sayfayı yeniden gönderin.',
]);

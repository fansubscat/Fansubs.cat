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
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '<samp>[media]</samp> BBCode butonunu mesaj gönderme sayfasında göster',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'İzin verilmediği takdirde, BBCode butonu gösterilmeyecektir, ancak kullanıcılar hala <samp>[media]</samp> etiketini kendi mesajlarında kullanabilir',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Seçenekler',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Kullanıcı imzalarında izin ver',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Kullanıcı imzalarında yerleşik medya (ortam) içeriğine izin ver.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'İçerik önbelleğe alma',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Medya (Ortam) Yerleştirme önbelleğini etkinleştir',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Bazı durumlarda diğer sitelerden medya (ortam) yüklerken, özellikle aynı içerik çok kez yüklenirken (örneğin, bir gönderiyi düzenlerken) normal performanstan daha yavaş olduğunu fark edebilirsiniz. Bu ayar etkinleştirildiğinde Medya (Ortam) Yerleştirme eklentisi, sitelerden topladığı bilgileri yerel olarak önbelleğe alacak ve performansı artıracaktır.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Düz URL adreslerini dönüştür',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Düz URL (<samp>[media]</samp> ya da <samp>[url]</samp> etiketleri arasına alınmamış) adreslerini yerleştirilmiş medya (ortam) içeriğine dönüştürmek için bu ayarı etkinleştirin. Not: Bu ayarın değiştirilmesi (mevcut gönderiler zaten ayrıştırılmış olduğundan) sadece yeni mesajları etkileyecektir.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'İçerik boyutlandırma',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Tam genişlikte içeriği etkinleştir',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Çoğu Media Embed içeriğini, özgün en-boy oranını koruyarak mesaj içerik alanının tamamını dolduracak şekilde genişletmek için etkinleştirin.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Özel azami içerik genişliği',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Siteler için ayrı ayrı özel azami genişlik değerleri tanımlamak üzere bu alanı kullanın. Bu değer, varsayılan boyutu ve yukarıdaki tam genişlik seçeneğini geçersiz kılar. Her siteyi yeni bir satıra <samp class="error">siteId:width</samp> biçiminde, <samp class="error">px</samp> veya <samp class="error">%</samp> kullanarak girin. Örnek:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">İpucu:</strong> Burada kullanılacak site kimliğini görmek için Siteleri yönet sayfasında fareyi sitenin üzerine getirin.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Medya (Ortam) Yerleştirme önbelleğini temizle',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Medya (Ortam) Yerleştirme önbelleği günde bir kez otomatik olarak zaten temizlenir. Yine de önbelleği hemen temizlemek isterseniz bu düğmeyi kullanarak elle (manuel) temizleme yapabilirsiniz.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Site id numarası: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Bu site mevcut olan bir BBCode ile çakışıyor: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Aşağıdaki hatalarla karşılaşıldı:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” geçerli bir site kimliği değil',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s”, “px” veya “%%” cinsinden geçerli bir genişlik değil',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Medya (Ortam) Yerleştirme Sitelerini Yönet',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Buradan, içeriğini göstermek için Medya (Ortam) Yerleştirme Eklentisine izin vermek istediğiniz siteleri yönetebilirsiniz.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Gösterilecek hiç bir medya (ortam) sitesi yok.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Aşağıdaki siteler artık desteklenmiyor veya çalışmıyor. Lütfen bu siteleri kaldırmak için bu sayfayı yeniden gönderin.',
]);

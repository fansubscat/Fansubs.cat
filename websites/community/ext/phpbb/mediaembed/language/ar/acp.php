<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Translated By : Bassel Taha Alhitary <http://alhitary.net>
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
	'ACP_MEDIA_SETTINGS'				=> 'إعدادات إدراج مُحتوى الوسائط',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'من هنا تستطيع ضبط الإعدادات الخاصة بالإضافة “إدراج مُحتوى الوسائط”.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'ظهور زر BBCode للوسائط <samp>[media]</samp>',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'اختيارك “لا”, يعني منع ظهور زر BBCode الخاص بالوسائط <samp>[media]</samp> في محرر الكتابة, وبالرغم من ذلك يستطيع الأعضاء استخدام الوسم <samp>[media]</samp> في مُشاركاتهم.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'الخيارات',
	'ACP_MEDIA_ALLOW_SIG'				=> 'التوقيعات',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'السماح للعضو بإستخدام وسم الوسائط في التوقيع.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'التخزين المؤقت للمحتوى',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'تفعيل التخزين المؤقت للوسائط المضمنة',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'قد تلاحظ في بعض الحالات بطئاً عند تحميل الوسائط من مواقع أخرى، خصوصاً عند تحميل المحتوى نفسه عدة مرات (مثلاً عند تعديل مشاركة). يؤدي تفعيل هذا الخيار إلى تخزين المعلومات التي تجمعها إضافة الوسائط المضمنة محلياً، مما يُحسن الأداء.',
	'ACP_MEDIA_PARSE_URLS'				=> 'تحويل الروابط العادية',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'اختار “نعم” لتحويل الروابط العادية (التي لم تندرج بين الوسوم <samp>[media]</samp> أو <samp>[url]</samp>) إلى محتوى الوسائط. مع الملاحظة بأن تطبيق هذا الخيار سيكون على المشاركات الجديدة فقط, ولن تؤثر على المشاركات القديمة.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'حجم المحتوى',
	'ACP_MEDIA_FULL_WIDTH'				=> 'تفعيل المحتوى بعرض كامل',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'يؤدي تفعيل هذا الخيار إلى توسيع معظم محتوى الوسائط المضمنة ليملأ عرض منطقة محتوى المشاركة مع الحفاظ على نسبة العرض إلى الارتفاع الأصلية.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'حد أقصى مخصص لعرض المحتوى',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'استخدم هذا الحقل لتحديد قيم مخصصة للحد الأقصى للعرض لكل موقع. سيؤدي هذا إلى تجاوز الحجم الافتراضي وخيار العرض الكامل أعلاه. أدخل كل موقع في سطر جديد بالتنسيق <samp class="error">siteId:width</samp> مستخدماً <samp class="error">px</samp> أو <samp class="error">%</samp>. مثال:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">تلميح:</strong> مرّر مؤشر الفأرة فوق موقع في صفحة إدارة المواقع لإظهار معرّف الموقع المطلوب استخدامه هنا.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'مسح التخزين المؤقت للوسائط المضمنة',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'يُمسح التخزين المؤقت للوسائط المضمنة تلقائياً مرة يومياً، ويمكن استخدام هذا الزر لمسحه يدوياً الآن.',
	'ACP_MEDIA_SITE_TITLE'				=> 'الموقع: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'هذا الموقع يتعارض مع BBCode آخر موجود: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'حدثت الأخطاء التالية:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: ‏“%1$s” ليس معرّف موقع صالحاً',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: ‏“%2$s” ليس عرضاً صالحاً بوحدة “px” أو “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'إدارة مواقع الوسائط',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'من هنا تستطيع إدارة المواقع التي تريد السماح بإدارج محتوى الوسائط منها.',
	'ACP_MEDIA_SITES_ERROR'				=> 'لا يوجد مواقع وسائط يُمكن عرضها.',
	'ACP_MEDIA_SITES_MISSING'			=> 'المواقع التالية لم تعد مدعومة أو تعمل. نرجوا إعادة إرسال هذه الصفحة لإزالتها.',
]);

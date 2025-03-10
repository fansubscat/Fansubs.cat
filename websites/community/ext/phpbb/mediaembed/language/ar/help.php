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
	'HELP_EMBEDDING_MEDIA'			=> 'إدراج مُحتوى الوسائط',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'كيف أستطيع إدراج مُحتوى الوسائط من المواقع الأخرى في المُشاركات؟',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'يستطيع الأعضاء إدراج محتوى الوسائط مثل الفيديو والمقاطع الصوتية من المواقع المسموح بها بإستخدام
										الوسم <strong>[media][/media]</strong>, أو ببساطة كتابة الرابط لأحد المواقع المدعومة
										كما هو بدون الوسوم. مثال:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />كما تلاحظ في المثال أعلاه, تستطيع أيضاً استخدام الرابط بدون الوسم
										<strong>[media]</strong>.
										<br /><br />ستكون النتيجة للمثال المذكور أعلاه كالآتي:<br /><br />%2$s
										<br /><br />القائمة التالية تحتوي على المواقع المدعومة:<br /><samp>%3$s.</samp>
										<br /><br />للمزيد من المعلومات والأمثلة للمواقع المدعومة,
										انقر على هذا الرابط <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										معلومات إدراج مُحتوى الوسائط</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

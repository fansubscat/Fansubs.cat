<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Translated By : Bassel Taha Alhitary - www.alhitary.net
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
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
	$lang = array();
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

$lang = array_merge($lang, array(
	'ACP_AUTOGROUPS_MANAGE'			=> 'إدارة المجموعات التلقائية',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'تستطيع بواسطة هذه الصفحة إضافة / تعديل / مُشاهدة / حذف إعدادات المجموعات التلقائية.',
	'ACP_AUTOGROUPS_ADD'			=> 'إضافة مجموعة تلقائية',
	'ACP_AUTOGROUPS_EDIT'			=> 'نعديل مجموعة تلقائية',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'المجموعة ',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'اختار مجموعة لكي يتم إضافة / حذف الأعضاء منها تلقائياً.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'النوع ',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'اختار النوع بحيث سيتم إضافة / حذف الأعضاء من هذه المجموعة.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'الحد الأدنى ',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'سيتم إضافة الأعضاء إلى هذه المجموعة عند الوصول إلى هذه القيمة.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'الحد الأعلى ',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'سيتم حذف الأعضاء من هذه المجموعة عند الوصول إلى هذه القيمة. لن يتم حذف الأعضاء إذا تركت هذا الحقل فارغاً.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'المحموعة الإفتراضية ',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'هذه المجموعة ستكون هي المجموعة الإفتراضية الجديدة للعضو.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'لن يؤثر هذا على الأعضاء الذين مجموعتهم الافتراضية هي أحد المجموعات التالية: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'إشعار الأعضاء ',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'ارسال تنبيه إلى الأعضاء بأنه تم اضافتهم أو حذفهم تلقائياً من هذه المجموعة.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'تجاهل المجموعة الإفتراضية ',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'لن يتم تغيير المجموعة الإفتراضية للعضو بواسطة المجموعة التلقائية إذا تم تحديدها من القائمة هذه. تستطيع تحديد أكثر من مجموعة بواسطة النقر باستمرار على زر الكنترول <samp>CTRL</samp> (أو <samp>&#8984;CMD</samp> في نظام الماك Mac) ومن ثم التقر بالماوس على المجموعات التي تريدها.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'انشاء مجموعة تلقائية جديدة',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'تم ضبط الإعدادات بنجاح.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'هل أنت متأكد من حذف  هذه المجموعة التلقائية ؟',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'تم حذف المجموعة التلقائية بنجاح.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'لا توجد مجموعات تلقائية.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'لا توجد مجموعات',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'هناك خطأ. لم يتم تحديد مجموعة عضو.<br />يمكن استخدام المجموعات التلقائية فقط مع مجموعات العضو المعروفة , والتي يمكن انشائها بواسطة صفحة إدارة المجموعات.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'هناك خطأ. لا تستطيع ضبط نفس القيمة في الحد الأعلى و الحد الأدني.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'عمر العضو',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'أيام منذ الزيارة الأخيرة',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'أيام العضوية',
	'AUTOGROUPS_TYPE_POSTS'			=> 'المشاركات',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'التحذيرات',
));

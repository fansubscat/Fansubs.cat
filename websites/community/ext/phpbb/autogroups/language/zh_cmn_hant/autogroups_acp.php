<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
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
	'ACP_AUTOGROUPS_MANAGE'			=> '管理自動分組',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> '使用此表單，你可以添加，編輯，查看和刪除自動分組配置。',
	'ACP_AUTOGROUPS_ADD'			=> '增加自動分組',
	'ACP_AUTOGROUPS_EDIT'			=> '編輯自動分組',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> '用戶組',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> '選擇一個用戶組以進行自動添加/刪除用戶。',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> '自動分組類型',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> '選擇對哪些用戶將被添加或從該組刪除的條件。',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> '最小值',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> '如果它們超過最小值，用戶將被添加到該組。',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> '最大值',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> '如果它們超過最大值，用戶將從此組中刪除。留空本欄如果你不希望用戶被刪除。',
	'ACP_AUTOGROUPS_DEFAULT'				=> '設置預設群組',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> '設置為用戶新的預設用戶組。',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> '這不會影響默認用戶組是以下用戶之一的用戶： <samp>%s</samp>.',
	'ACP_AUTOGROUPS_NOTIFY'					=> '通知用戶',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> '自動添加或刪除該組後，將通知發送給用戶。',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> '設置豁免預設分組的用戶組',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> '此表中已選擇的用戶組將豁免於設置預設分組。按下 <samp>CTRL</samp> (或 Mac 的 <samp>&#8984;CMD</samp>) 來選擇多過一個用戶組',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> '建立新的用戶組',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> '自動分組配置成功。',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> '你確定你要刪除此自動分組設置？',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> '分組設置已成功刪除。',
	'ACP_AUTOGROUPS_EMPTY'			=> '沒有任何分組設置',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> '沒有用戶組可以選擇',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> '錯誤！沒有一個有效的用戶組被選中。<br/>自動分組只能設定用戶組，用戶組可以在管理頁上創建。',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> '错误！最大值不能等于最小值。',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> '用戶年齡',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> '自上次访问以来的几天',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> '成為會員天數',
	'AUTOGROUPS_TYPE_POSTS'			=> '帖子數目',
	'AUTOGROUPS_TYPE_WARNINGS'		=> '警告數目',
));

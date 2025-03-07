<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* @简体中文语言　David Yin <http://www.g2soft.net/>
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
	'ACP_AUTOGROUPS_MANAGE'			=> '管理自动分组',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> '使用此表单，您可以添加，编辑，查看或删除自动分组配置。',
	'ACP_AUTOGROUPS_ADD'			=> '添加自动分组',
	'ACP_AUTOGROUPS_EDIT'			=> '编辑自动分组',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> '用户组',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> '选择一个用户组，用于自动加入或者移出用户。',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> '自动分组类型',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> '选择触发用户被加入或者移出的条件类型。',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> '最小值',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> '如果超过这个最小值，用户将被加入该用户组。',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> '最大值',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> '如果超过最大值，用户将被移出该用户组。如果不希望用户被移出，请留空此栏。',
	'ACP_AUTOGROUPS_DEFAULT'				=> '设置默认用户组',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> '设置为用户的新默认用户组。',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> '这不会影响那些默认用户组是： <samp>%s</samp> 的用户',
	'ACP_AUTOGROUPS_NOTIFY'					=> '通知用户',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> '自动加入或者移出该组后，该用户会收到通知。',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> '排除的用户组',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> '排除这些用户组的成员',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> '列表中所选择的<em>任何组</em>的成员都会别忽略掉。此处留空，如果你希望自动分组能对 <em>所有论坛成员</em> 都有效。 使用 <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) 来选择多个用户组。',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> '发生错误。此条件下的组无法同时被排除在外。',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> '设置用户组默认的豁免',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> '此表中选定的用户组将不受自动分组设置的影响。按下 <samp>CTRL</samp> （或者 Mac 的 <samp>&#8984;CMD</samp> ）来选择多个用户组。',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> '建立新的自动分组',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> '自动分组配置成功。',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> '您确定要删除此自动分组配置？',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> '自动分组配置已删除。',
	'ACP_AUTOGROUPS_EMPTY'			=> '没有任何的自动分组设置。',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> '无用户组可以选择',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> '错误！无效用户组被选中。<br /> 自动分组只能应用于用户定义的组，用户定义的组可以在用户组管理界面添加。',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> '错误！最大值不能等于最小值。',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> '用户年龄',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> '自上次訪問以來的幾天',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> '成为会员天数',
	'AUTOGROUPS_TYPE_POSTS'			=> '帖子数目',
	'AUTOGROUPS_TYPE_WARNINGS'		=> '警告数目',
));

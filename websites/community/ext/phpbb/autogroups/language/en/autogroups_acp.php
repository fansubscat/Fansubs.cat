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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Manage Auto Groups',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Using this form you can add, edit, view and delete Auto Group configurations.',
	'ACP_AUTOGROUPS_ADD'			=> 'Add Auto Groups',
	'ACP_AUTOGROUPS_EDIT'			=> 'Edit Auto Groups',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Group',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Choose a group to automatically add/remove users from.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Auto Group type',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Choose the type of condition on which users will be added or removed from this group.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimum value',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Users will be added to this group if they meet or exceed the minimum value.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maximum value',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Users will be removed from this group if they meet or exceed the maximum value. Set this to 0 if you do not want users to be removed.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Set group default',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Make this the user’s new default group.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'This will not affect users whose default user group is one of the following: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notify users',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Send a notification to users after being automatically added or removed from this group.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Set group default exemptions',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto Groups will not change a user’s <em>default group</em> if it is selected in this list. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Create new Auto Group',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Auto Group successfully configured.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Are you sure you want to delete this Auto Group configuration?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Auto Group successfully deleted.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'There are no auto groups.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'No groups available',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'An error occurred. A valid user group was not selected.<br />Auto Groups can only be used with user defined groups, which can be created on the Manage groups page.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'An error occurred. Minimum and maximum values can not be set to the same value.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'User age',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Days since last visit',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Membership days',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Posts',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Warnings',
));

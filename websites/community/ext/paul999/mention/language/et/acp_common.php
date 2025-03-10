<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'MENTION_LENGTH'                => 'Simple mention minimum length',
	'MENTION_LENGTH_EXPLAIN'        => 'The minimum text length before the simple mention dropdown is shown. 
	On larger boards you might want to increase this value.',
	'MENTION_COLOR'                 => 'Simple mention color',
	'MENTION_COLOR_EXPLAIN'         => 'This color is used within the post to define what user is mentioned. Only hex values can be used.',
	'MENTION_COLOR_INVALID'         => 'The selected mention color (%s) is invalid. Please select a valid HEX color, without #',
	'MENTION_MAX_RESULTS'			=> 'Simple mention max results',
	'MENTION_MAX_RESULTS_EXPLAIN'	=> 'The maximum number of users show in the dropdown. On larger boards you might want to decrease this value',
	'MENTION_LARGE_GROUPS'			=> 'Mention large group size',
	'MENTION_LARGE_GROUPS_EXPLAIN'	=> 'If the group has more members as the specified number, the “Can mention large groups” permission is required.'
));

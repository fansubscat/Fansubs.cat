<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Generating Spoilers',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Adding a spoiler into a post',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'A basic spoiler consist in a text wrapped in <strong>[spoiler][/spoiler]</strong>. For example:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>This would generate:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Adding a spoiler with title into a post',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'A spoiler can optionally show a custom title, to do so the text need to be wrapped in <strong>[spoiler title=][/spoiler]</strong>. For example:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>This would generate:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Plot summary',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Details about the movie narrative'
]);

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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Создание спойлеров',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Добавление спойлера в сообщение',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'Простой спойлер состоит из текста, заключенного в теги <strong>[spoiler][/spoiler]</strong>. Например:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>Это выведет:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Добавление спойлера с названием в сообщение',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'Спойлер может иметь произвольное название, для этого текст заключается в теги <strong>[spoiler title=][/spoiler]</strong>. Например:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>Это выведет:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Краткий сюжет',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Подробности о сюжете фильма'
]);

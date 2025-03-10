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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'מייצר ספויילרים',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'הוסף ספויילר להודעה',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'ספויילר בסיסי מוקף בתגים <strong>[spoiler][/spoiler]</strong>. לדוגמה:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br> מייצר:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'הוספת ספויילר עם כותרת לתוך ההודעה',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'ספויילר יכול להכין כותרת מותאמת אישית, כדי לעשות זאת צריך שהטקסט יהיה מוקף ב<strong>[spoiler title=][/spoiler]</strong>. לדוגמה:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br> יציג על המסך:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'תקציר העלילה',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'פרטים על עלילת הסרט'
]);

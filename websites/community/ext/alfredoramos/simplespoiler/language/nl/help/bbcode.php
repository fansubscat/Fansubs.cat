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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Spoilers aanmaken',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Een spoiler toevoegen aan een bericht',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'Een basisspoiler bestaat uit een tekst ingepakt in <strong>[spoiler][/spoiler]</strong>. Bijvoorbeeld:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>Dit zou genereren:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Een spoiler met titel toevoegen aan een bericht',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'Een spoiler kan optioneel een aangepaste titel tonen, daarvoor moet de tekst worden ingepakt in <strong>[spoiler title=][/spoiler]</strong>. Bijvoorbeeld:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>Dit zou genereren:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Plotsamenvatting',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Details over het verhaal van de film'
]);

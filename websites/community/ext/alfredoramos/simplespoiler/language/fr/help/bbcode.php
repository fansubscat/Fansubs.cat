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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Génération de spoilers',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Ajouter un spoiler dans un message',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'Un spoiler de base consiste en un texte contenu dans une balise <strong>[spoiler][/spoiler]</strong>. Par exemple:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>Cela générera<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Ajouter un spoiler avec un titre dans un message',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'Un spoiler peut éventuellement afficher un titre personnalisé, pour ce faire, le texte doit être contenu dans <strong>[spoiler title=][/spoiler]</strong>. Par exemple:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>Cela générera<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Résumé de l\'intrigue',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Détails sur le récit du film'
]);

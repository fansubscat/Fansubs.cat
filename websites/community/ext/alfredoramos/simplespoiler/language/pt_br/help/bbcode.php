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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Gerando Spoilers',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Adicionando um spoiler em um post',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'Um spoiler básico consiste em um texto contido em <strong>[spoiler][/spoiler]</strong>. Por exemplo:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>Isso geraria:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Adicionando um spoiler com título em um post',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'Um spoiler pode, opcionalmente, mostrar um título personalizado. Para isso, o texto precisa ser agrupado em <strong>[spoiler title=][/spoiler]</strong>. Por exemplo:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>Isso geraria:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Resumo do enredo',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Detalhes sobre a narrativa do filme'
]);

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
	'HELP_BBCODE_BLOCK_SPOILERS' => 'Creando Spoilers',

	'HELP_BBCODE_SPOILERS_BASIC_QUESTION'	=> 'Agregando un spoiler en un mensaje',
	'HELP_BBCODE_SPOILERS_BASIC_ANSWER'		=> 'Un spoiler básico consiste en texto encapsulado entre las etiquetas <strong>[spoiler][/spoiler]</strong>. Por ejemplo:<br><br><strong>[spoiler]</strong>%2$s<strong>[/spoiler]</strong><br><br>Esto generará:<br>%1$s',

	'HELP_BBCODE_SPOILERS_TITLE_QUESTION'	=> 'Agregando un spoiler con título en un mensaje',
	'HELP_BBCODE_SPOILERS_TITLE_ANSWER'		=> 'Un spoiler opcionalmente puede mostrar un título personalizado, para hacerlo el texto necesita ser encapsulado entre las etiquetas <strong>[spoiler title=][/spoiler]</strong>. Por ejemplo:<br><br><strong>[spoiler title=</strong>%3$s<strong>]</strong>%2$s<strong>[/spoiler]</strong><br><br>Esto generará:<br>%1$s',

	'HELP_BBCODE_SPOILERS_DEMO_TITLE'	=> 'Resumen de la trama',
	'HELP_BBCODE_SPOILERS_DEMO_BODY'	=> 'Detalles de la narrativa de la película'
]);

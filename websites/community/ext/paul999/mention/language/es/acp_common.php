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
	'MENTION_LENGTH'                => 'Longitud mínima de la mención simple',
	'MENTION_LENGTH_EXPLAIN'        => 'Longitud de texto mínima mostrada antes de la lista desplegable de mención simple. 
	En foros más grandes es posible que desee aumentar este valor.',
	'MENTION_COLOR'                 => 'Color de mención simple',
	'MENTION_COLOR_EXPLAIN'         => 'Este color se utiliza dentro del mensaje para definir qué usuario se menciona. Solo se pueden usar valores hexadecimales.',
	'MENTION_COLOR_INVALID'         => 'El color de mención seleccionado (%s) no es válido. Por favor, seleccione un color HEX válido, sin #',
	'MENTION_MAX_RESULTS'			=> 'Simple mention max results',
	'MENTION_MAX_RESULTS_EXPLAIN'	=> 'The maximum number of users show in the dropdown. On larger boards you might want to decrease this value',
	'MENTION_LARGE_GROUPS'			=> 'Mention large group size',
	'MENTION_LARGE_GROUPS_EXPLAIN'	=> 'If the group has more members as the specified number, the “Can mention large groups” permission is required.'
));

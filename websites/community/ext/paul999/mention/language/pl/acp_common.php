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
	'MENTION_LENGTH'                => 'Minimalna długość nazwy użytkownika',
	'MENTION_LENGTH_EXPLAIN'        => 'Tutaj można ustawić minimalną długość nazwy użytkownika, którą należy wprowadzić, aby wspomnieć użytkownika. Na większych forach możesz zwiększyć tę wartość.',
	'MENTION_COLOR'                 => 'Kolor wzmianki',
	'MENTION_COLOR_EXPLAIN'         => 'Ten kolor jest używany w poście, aby określić, który użytkownik został wspomniany. Możesz użyć tylko kolor zapisany w systemie szesnastkowym.',
	'MENTION_COLOR_INVALID'         => 'Wybrany kolor wzmianki (%s) jest nieprawidłowy. Proszę wybrać prawidłowy kolor HEX bez #',
	'MENTION_MAX_RESULTS'			=> 'Simple mention max results',
	'MENTION_MAX_RESULTS_EXPLAIN'	=> 'The maximum number of users show in the dropdown. On larger boards you might want to decrease this value',
	'MENTION_LARGE_GROUPS'			=> 'Mention large group size',
	'MENTION_LARGE_GROUPS_EXPLAIN'	=> 'If the group has more members as the specified number, the “Can mention large groups” permission is required.'
));

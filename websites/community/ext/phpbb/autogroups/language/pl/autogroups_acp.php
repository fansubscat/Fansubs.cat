<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* @Polska wersja językowa Auto Groups 2.0.0 - 04.08.2018, Mateusz Dutko (vader) www.rnavspotters.pl
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_AUTOGROUPS_MANAGE'			=> 'Zarządzanie automatycznymi grupami',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Tutaj można dodać, edytować, przeglądać lub usunąć automatyczną grupę.',
	'ACP_AUTOGROUPS_ADD'			=> 'Tworzenie automatycznej grupy',
	'ACP_AUTOGROUPS_EDIT'			=> 'Edycja automatycznej grupy',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grupa',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Wybierz grupę, aby automatycznie dodać lub usunąć użytkowników.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Warunek',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Wybierz warunek, na podstawie którego użytkownik zostanie dodany lub usunięty z grupy.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimalna wartość',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Użytkownik zostanie dodany do grupy, jeśli osiągnie minimalną wartość.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maksymalna wartość',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Użytkownik zostanie usunięty z grupy, jeśli przekroczy maksymalną wartość. Ustaw 0, aby użytkownik nie został usunięty z grupy.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Ustaw domyślną grupę',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Ustaw Tak, aby zmienić domyślną grupę użytkownika.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Nie wpłynie to na użytkowników, których domyślna grupa to: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Powiadom użytkownika',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Ustaw Tak, aby powiadomić użytkownika, gdy zostanie automatycznie dodany lub usunięty z grupy.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Domyślna grupa',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Domyślna grupa użytkownika nie zostanie zmieniona, jeśli zostanie wybrana na liście predefiniowanych grup. Zaznacz kilka grup przy pomocy wciśniętego przycisku <samp>CTRL</samp> (lub <samp>&#8984;CMD</samp> w systemie iOS) na klawiaturze.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Utwórz automatyczną grupę',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Utworzono automatyczną grupę.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Czy na pewno chcesz usunąć automatyczną grupę?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Usunięto automatyczną grupę.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Brak automatycznych grup.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Brak grup',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Nie wybrano żadnej grupy.<br /><br />Automatyczne grupy muszą być połączone z istniejącymi grupami, które można utworzyć na stronie zarządzania grupami.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Minimalna i maksymalna wartość nie może być taka sama.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Wiek',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Days since last visit',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Długość członkostwa (dni)',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Liczba postów',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Ostrzeżenia',
));

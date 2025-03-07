<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Dutch translation by Nadleeh (www.heralder.net)
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Beheer Automatische Groepen',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Hier kun je automatische groepen aanmaken, bewerken, weergeven en verwijderen.',
	'ACP_AUTOGROUPS_ADD'			=> 'Voeg automatische groep toe',
	'ACP_AUTOGROUPS_EDIT'			=> 'Bewerk automatische groep',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Groep',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Kies een groep waaraan gebruikers automatisch toegevoegd of verwijderd moeten worden.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Type automatische groep',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Kies het type conditie waarop gebruikers toegevoegd of verwijderd worden aan deze groep.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimale waarde',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Gebruikers worden toegevoegd aan deze groep als de minimale waarde wordt overschreden.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maximale waarde',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Gebruikers worden verwijderd van deze groep als de maximale waarde wordt overschreden. Laat dit veld leeg om automatisch verwijderen te voorkomen.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Instellen als standaard groep',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Maak voor deze gebruikers de groep standaard.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Dit heeft geen invloed op gebruikers wiens standaardgroep één van de volgende is: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notificeer gebruikers',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Stuur een notificatie naar gebruikers als ze automatisch worden toegevoegd of verwijderd van deze groep.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Instellen standaard groep uitzonderingen',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Automatische groepen zal niet een gebruiker zijn standaard groep veranderen als deze groep geselecteerd is in deze lijst. Selecteer meerdere groepen door <samp>CTRL</samp> (of <samp>&#8984;CMD</samp> op Mac) ingedrukt te houden en de groepen te selecteren.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Maak nieuwe automatische groep',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Automatische groep succesvol geconfigureerd',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Weet je zeker dat je deze automatische groep wilt verwijderen?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Automatische groep succesvol verwijderd.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Er zijn geen automatische groepen.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Geen groepen beschikbaar',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Er is een fout opgetreden. Er is geen geldige groep geselecteerd.<br />Automatische groepen kan alleen worden gebruikt bij door de beheerder aangemaakte groepen, welke kan worden aangemaakt op de “Beheer groepen pagina”.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Er is een fout opgetreden. De minimale en maximale waarde kunnen niet hetzelfde zijn.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Leeftijd',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Dagen sinds laatste bezoek',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Aantal dagen lid',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Berichten',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Waarschuwingen',
));

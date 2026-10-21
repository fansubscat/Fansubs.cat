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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Beheer Auto Groepen',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Met dit formulier kun je Auto Groepconfiguraties toevoegen, bewerken, bekijken en verwijderen.',
	'ACP_AUTOGROUPS_ADD'			=> 'Voeg Auto Groepen toe',
	'ACP_AUTOGROUPS_EDIT'			=> 'Bewerk Auto Groepen',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Groep',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Kies een groep waaraan gebruikers automatisch worden toegevoegd of verwijderd.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Auto Groep type',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Kies het type voorwaarde waarop gebruikers aan deze groep worden toegevoegd of verwijderd.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimale waarde',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Gebruikers worden aan deze groep toegevoegd als ze de minimale waarde bereiken of overschrijden.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maximale waarde',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Gebruikers worden uit deze groep verwijderd als ze de maximale waarde bereiken of overschrijden. Zet dit op 0 als je niet wilt dat gebruikers worden verwijderd.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Stel standaardgroep in',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Maak dit de nieuwe standaardgroep van de gebruiker.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Dit heeft geen effect op gebruikers waarvan de standaardgroep een van de volgende is: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Meld gebruikers',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Stuur een melding naar gebruikers nadat ze automatisch aan deze groep zijn toegevoegd of verwijderd.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Uitsluitgroepen',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Negeer leden van deze groepen',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Leden die tot <em>om het even welke groep</em> in deze lijst behoren, worden genegeerd. Laat dit veld leeg als je deze Auto Groep op <em>alle leden</em> van je forum wilt toepassen. Selecteer meerdere groepen door <samp>CTRL</samp> (of <samp>&#8984;CMD</samp> op Mac) in te houden en de groepen te selecteren.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'Er is een fout opgetreden. De groep voor deze voorwaarde kan niet ook in het veld "uitgesloten groepen" worden geselecteerd.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Stel standaardgroep uitzonderingen in',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto Groepen zullen de <em>standaardgroep</em> van een gebruiker niet veranderen als deze in deze lijst is geselecteerd. Selecteer meerdere groepen door <samp>CTRL</samp> (of <samp>&#8984;CMD</samp> op Mac) in te houden en de groepen te selecteren.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Maak nieuwe Auto Groep aan',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Auto Groep succesvol geconfigureerd.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Weet je zeker dat je deze Auto Groepconfiguratie wilt verwijderen?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Auto Groep succesvol verwijderd.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Er zijn geen auto groepen.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Geen groepen beschikbaar',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Er is een fout opgetreden. Er is geen geldige gebruikersgroep geselecteerd.<br />Auto Groepen kunnen alleen worden gebruikt met door gebruikers gedefinieerde groepen, die aangemaakt kunnen worden op de pagina "Groepen beheren".',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Er is een fout opgetreden. Minimale en maximale waarden kunnen niet op dezelfde waarde worden ingesteld.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Gebruikersleeftijd',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Dagen sinds laatste bezoek',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Lidmaatschapsdagen',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Berichten',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Waarschuwingen',
));

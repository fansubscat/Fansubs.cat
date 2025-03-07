<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Håndter autogrupper',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Med denne formular kan du tilføje, redigere, vise og slette autogruppekonfigurationer.',
	'ACP_AUTOGROUPS_ADD'			=> 'Tilføj autogrupper',
	'ACP_AUTOGROUPS_EDIT'			=> 'Rediger autogrupper',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Gruppe',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Vælg en gruppe som brugere automatisk skal tilføjes/fjernes fra.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Autogruppetype',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Vælg typen af betingelse hvor brugere tilføjes eller fjernes fra gruppen.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimum værdi',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Brugere tilføjes til gruppen hvis de overstiger minimumsværdien.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maksimum værdi',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Brugere fjernes fra gruppen hvis de overstiger maksimumsværdien. Lad feltet været tomt, hvis du ikke vil have at brugere fjernes.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Sæt gruppestandard',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Gør det til brugerens nye standardgruppe.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Dette påvirker ikke brugere, hvis standardbrugergruppe er en af: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Informer brugere',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Send en notifikation til brugere efter automatisk tilføjelse eller fjernelse fra gruppen.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Sæt undtagelser for gruppestandard',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Autogrupper ændre ikke en brugers standardgruppe hvis den er valgt i listen. Vælg flere grupper ved at holde <samp>CTRL</samp> (eller <samp>&#8984;CMD</samp> på Mac) nede og vælg grupperne.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Opret ny autogruppe',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Autogruppe konfigureret.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Er du sikker på, at du vil slette autogruppekonfigurationen?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Autogruppe slettet.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Der er ingen autogrupper.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Ingen grupper tilgængelig',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Der opstod en fejl. Der var ikke valgt nogen gyldig brugergruppe.<br />Autogrupper kan kun bruges med brugerdefinerede grupper, som kan oprettes på Håndter grupper-siden.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Der opstod en fejl. Minimums- og maksimumsværdier kan ikke sættes til den samme værdi.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Brugerens alder',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Days since last visit',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Dage med medlemsskab',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Indlæg',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Advarsler',
));

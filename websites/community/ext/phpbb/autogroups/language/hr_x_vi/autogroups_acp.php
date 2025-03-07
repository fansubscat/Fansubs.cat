<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* Croatian translation by Ančica Sečan Matijaščić (http://ancica.sunceko.net)
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Automatsko upravljanje grupom/ama',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Odavde možete dodavati/izbrisati/pregled(av)ati/uređivati postavke automatskog upravljanja grupom/ama.',
	'ACP_AUTOGROUPS_ADD'			=> 'Dodavanje grupa',
	'ACP_AUTOGROUPS_EDIT'			=> 'Uređivanje grupa',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grupa',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Izaberite grupu u/iz koju/e želite automatski dodati/izbrisati korisnike/ce.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Tip automatskog upravljanja grupom/ama',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Izaberite tip uvjeta na osnovu kojeg će korisnici/e biti dodani(e)/izbrisani(e) u/iz grupu/e.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimalna vrijednost',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Vrijednost prekoračenje koje će rezultirati dodavanjem korisnika/ca grupi.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maksimalna vrijednost',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Vrijednost prekoračenje koje će rezultirati izbrisivanjem korisnika/ca iz grupe. Ostavite praznim ukoliko ne želite izbrisati korisnike/ce iz grupe.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Postavite grupu kao zadanu',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Ukoliko je omogućeno, grupa će zamijeniti postojeću i postati nova zadana grupa korisnika/ca.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Ovo neće imati utjecaja na korisnike/ce čija je zadana grupa jedna od sljedećih: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Obavijestite korisnike/ce',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Ukoliko je omogućeno, obavijest, o dodavanju/izbrisivanju u/iz grupu/e, će automatski biti poslana korisnicima/ama.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Iznimke za (ne)postavljanje grupe kao zadane',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Automatsko upravljanje grupom/ama neće promijeniti zadanu grupu korisnika/ca ukoliko je zadana grupa odabrana na listi. Odaberite višestruke grupe držeći pritisnutu tipku [tipkovnice] <samp>CTRL</samp> [ili <samp>&#8984;CMD</samp> na Macu] + klikćući [označavajući] [grupe za odabir].',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Dodajte grupu',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Automatsko upravljanje izabranom grupom je postavljeno.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Jeste li siguran/na da želite izbrisati automatsko upravljanje izabranom grupom?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Automatsko upravljanje izabranom grupom je izbrisano.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'There are no auto groups.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Nema dostupnih grupa',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Došlo je do greške: nije izabrana niti jedna grupa.<br />Automatsko upravljanje grupom/ama može se primijeniti samo na korisnički dodane grupe.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Došlo je do greške: minimalna i maksimalna vrijednost ne mogu biti postavljene na istu vrijednost.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Starost',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Dani od zadnjeg posjeta.',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Trajanje članstva u danima',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Postovi',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Upozorenja',
));

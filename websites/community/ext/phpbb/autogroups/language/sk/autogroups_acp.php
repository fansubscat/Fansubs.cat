<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Slovak translation by Senky (https://github.com/senky)
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Spravovať automatické skupiny',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'V tomto formulári môžete pridávať, upravovať, prezerať a mazať konfigurácie automatických skupín.',
	'ACP_AUTOGROUPS_ADD'			=> 'Pridať automatickú skupinu',
	'ACP_AUTOGROUPS_EDIT'			=> 'Upraviť automatickú skupinu',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Skupina',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Vyberte skupinu, do ktorej budú automaticky pridávaní/odstraňovaní používatelia.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Typ automatickej skupiny',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Vyberte typ podmienky, podľa ktorej budú používatelia pridaní alebo odstránení z tejto skupiny.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimálna hodnota',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Používatelia budú pridaní do tejto skupiny ak prekročia minimálnu hodnotu.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maximálna hodnota',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Používatelia budú odstránení z tejto skupiny ak prekročia maximálnu hodnotu. Nechajte toto pole prázdne ak nechcete používateľov odstraňovať.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Nastaviť skupinu ako predvolenú',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Spraviť túto skupinu novú predvolenú skupinu používateľa.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Toto neovplyvní používateľov, ktorých predvolená skupina je jedna z týchto: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Oboznámiť používateľa',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Pošle notifikáciu používateľov po tom, čo budú automaticky pridaní alebo odstránení z tejto skupiny.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Nastaviť výnimky v predvolenej skupine',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Automatické skupiny nebudú meniť používateľovu predvolenú skupinu ak bude vybraná v tomto zozname. Vyberte viacero skupín podrčaním <samp>CTRL</samp> (alebo <samp>&#8984;CMD</samp> na Macu) a vyberte skupiny.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Vytvoriť novú automatickú skupinu',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Automatická skupina bola úspešne nakonfigurovaná.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Ste si istí, že chcete vymazať túto konfiguráciu automatickej skupiny?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Automatická skupina bola úspešne vymazaná.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Nie sú zatiaľ žiadne automatické skupiny.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Nie sú dostupné žiadne skupiny',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Nastala chyba. Nebola vybraná správna používteľská skupina.<br />Automatické skupiny môžu byť použité iba s používateľskými skupinami, ktoré môžu byť vytvorené na stránke Spravovať skupiny.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Nastala chyba. Minimálna a maximálna hodnota nemôžu byť nastavené na rovnakú hodnotu.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Vek používateľa',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Dni od poslednej návštevy',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Dĺžka členstva',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Počet príspevkov',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Počet varovaní',
));

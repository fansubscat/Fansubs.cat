<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Estonian translation by phpBBeesti.ee [Exabot]
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Halda auto-gruppe',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Kasutades seda, saad lisada, muuta, vaadata ja kustutada automaatsete grupide konfiguratsioone.',
	'ACP_AUTOGROUPS_ADD'			=> 'Lisa auto-grupp',
	'ACP_AUTOGROUPS_EDIT'			=> 'Muuda auto-grupi',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grupp',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Vali grupp, et automaatselt lisada / eemaldada liikmelt.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Auto-grupi tüüp',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Vali tüübi seisund, milline liige lisatakse või eemaldatakse sellest grupist.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimaalne väärtus',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Liikmed lisatakse sellesse gruppi, kui nad ületavad minimaalse väärtuse.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maksimaalne väärtus',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Liikmed eemaldatakse sellest grupist, kui nad ületavad selle maksimaalse väärtuse. Jäta see väli tühjaks, kui sa ei soovi seda kasutada.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Seadista vaikimisi grupp',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Tee see liikme(te) uueks vaikimisi grupiks.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'See ei mõjuta kasutajaid, kelle vaikimisi kasutajarühm on üks järgmistest: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Teavita liikmeid',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Saada teavitus liikmetele, kui nad on automaatselt lisatud või eemaldatud sellest grupist.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Seadista vaikimisi grupile erandid',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto-grupp ei muuda liikmete vaikimisi grupi, kui see on valitud sellest nimekirjast. Vali mitu gruppi korraga hoides klaviatuuri klahvi <samp>CTRL</samp> all (või <samp>&#8984;CMD</samp> Mac\'is) ja vali grupid.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Loo uus automaatne grupp',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Automaatne grupp edukalt seadistatud.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Kas oled kindel, et soovid selle automaatse gruppi seadistuse kustutada?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Automaatne grupp on edukalt kustutatud.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Ühtegi automaatset gruppi ei ole.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Ühtegi gruppi ei ole saadaval.',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Tekkis viga. Kehtivat kasutajagruppi ei ole valitud.<br />Automaatseid gruppe saab kasutada ainult siis, kui liikmele on määratud grupid, mida saab luua grupide halduse leheküljel.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Tekkis viga. Minimaalset ja maksimaalset väärtust ei saa seadistada samade väärtustega.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Liikme vanus',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Viimase külastuse päevad',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Liikmelisus päevades',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Postitused',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Hoiatused',
));

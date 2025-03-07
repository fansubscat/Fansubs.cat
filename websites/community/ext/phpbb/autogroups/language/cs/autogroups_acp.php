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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Správa Auto skupiny',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Pomocí tohoto formuláře můžete přidávat, upravovat, zobrazovat a odstraňovat konfigurace Auto skupiny.',
	'ACP_AUTOGROUPS_ADD'			=> 'Přidejte Auto skupiny',
	'ACP_AUTOGROUPS_EDIT'			=> 'Upravit Auto skupiny',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Skupina',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Vyberte si skupinu, která automaticky přidat / odebere uživatelé.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Typ Auto skupiny',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Vyberte podmínky, za kterých budou uživatelé přidáni nebo odebráni z této skupiny.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimální hodnota',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Uživatelé budou přidány do této skupiny, pokud překročí minimální hodnotu.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maximální hodnota',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Uživatelé budou odstraněny z této skupiny v případě, že překročí maximální hodnotu. Nechte toto pole prázdné, pokud nechcete, aby uživatelé byli odstraněny.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Nastavit výchozí skupinu',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Nastavit jako novou výchozí skupinu pro uživatelé.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'To neovlivní uživatele, jejichž výchozí skupinou uživatelů je: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Informovat uživatele',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Zaslat oznámení uživateli poté, co byl automaticky přidán nebo odebán z této skupiny.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Nastavte výchozí skupině výjimky',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto skupiny nezmění výchozí skupinu uživatele, pokud je zvolena v tomto seznamu. Vyberte několik skupin přidržením<samp>CTRL</samp> (nebo <samp>&#8984;CMD</samp> v Mac) a výberte skupiny.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Vytvořit novou Auto skupinu',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Auto skupina byla úspěšně nakonfigurovaná.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Jste si jisti, že chcete smazat tuto konfiguraci Auto skupiny?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Auto skupina byla úspěšně odstraněna.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Neexistují žádné auto skupiny.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'K dispozici nejsou žádné skupiny',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Vyskytla se chyba. Platná skupina uživatelů nebyla vybrána.<br />Auto Skupiny mohou být použity pouze s uživatelsky definovanými skupinami, které mohou být vytvořeny na straně správa skupin.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Vyskytla se chyba. Minimální a maximální hodnoty nelze nastavit na stejnou hodnotu.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Věk uživatelé',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Days since last visit',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Členem dnů',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Příspěvky',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Varování',
));

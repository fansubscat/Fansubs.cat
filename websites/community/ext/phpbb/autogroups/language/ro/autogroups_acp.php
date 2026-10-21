<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Romanian translation by iorG19 (https://ioforos.com)
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Gestionați Grupurile Automate',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Folosind acest formular puteți adăuga, edita, vizualiza și șterge configurațiile Auto Group.',
	'ACP_AUTOGROUPS_ADD'			=> 'Adăugați grupuri automate',
	'ACP_AUTOGROUPS_EDIT'			=> 'Editați grupurile automate',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grup',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Alegeți un grup din care să adăugați/eliminați automat utilizatori.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Tip de grup automat',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Alegeți tipul de condiție în care utilizatorii vor fi adăugați sau eliminați din acest grup.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Valoarea minima',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Utilizatorii vor fi adăugați în acest grup dacă îndeplinesc sau depășesc valoarea minimă.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Valoarea maximă',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Utilizatorii vor fi excluși din acest grup dacă îndeplinesc sau depășesc valoarea maximă. Setați acest lucru la 0 dacă nu doriți ca utilizatorii să fie eliminați.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Setați grupul implicit',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Faceți din acesta noul grup implicit al utilizatorului.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Acest lucru nu va afecta utilizatorii al căror grup de utilizatori implicit este unul dintre următoarele: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notificați utilizatorii',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Trimiteți o notificare utilizatorilor după ce au fost adăugat sau eliminat automat din acest grup.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Grupuri excluse',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Excludeți membrii acestor grupuri',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Membrii care aparțin <em>orice grup</em> selectat în această listă vor fi ignorați. Lăsați acest câmp necompletat dacă doriți ca acest grup automat să fie aplicat <em>toți membrii</em> ai forumului dvs. Selectați mai multe grupuri ținând apăsat pe <samp>CTRL</samp> (sau <samp>&#8984;CMD</samp> pe Mac) și selectând grupurile.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'A apărut o eroare. Grupul pentru această condiție nu poate fi selectat și în câmpul grupuri excluse.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Setați scutiri implicite de grup',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Grupurile automate nu vor schimba <em>grupul implicit</em> al unui utilizator dacă este selectat în această listă. Selectați mai multe grupuri ținând apăsat pe <samp>CTRL</samp> (sau <samp>&#8984;CMD</samp> pe Mac) și selectând grupurile.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Creați un nou Grup Automat',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Grupul automat a fost configurat cu succes.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Sigur doriți să ștergeți această configurație de grup automat?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Grupul automat a fost șters cu succes.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Nu există Grupuri Automate.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Nu există grupuri disponibile',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'A apărut o eroare. Nu a fost selectat un grup de utilizatori valid.<br />Grupurile automate pot fi utilizate numai cu grupuri definite de utilizator, care pot fi create pe pagina Gestionați grupuri.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'A apărut o eroare. Valorile minime și maxime nu pot fi setate la aceeași valoare.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Vârsta utilizatorului',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Zile de la ultima vizită',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Zilele de membru',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Postări',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Avertismente',
));

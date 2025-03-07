<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
* @Italian translation By alex75 https://www.phpbb-store.it
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Gestisci Autogruppi',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Da questo modulo è possibile aggiungere, modificare, vedere e cancellare le configurazioni di autogruppi.',
	'ACP_AUTOGROUPS_ADD'			=> 'Aggiungi autogruppi',
	'ACP_AUTOGROUPS_EDIT'			=> 'Modifica autogruppi',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Gruppo',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Scegli un gruppo a cui aggiungere o rimuovere automaticamente utenti.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Tipo autogruppo',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Scegli il tipo di condizione per cui gli utenti vengono aggiunti o rimossi dal gruppo.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Valore minimo',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Gli utenti verranno aggiunti a questo gruppo se supereranno il valore minimo.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Valore massimo',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Gli utenti verranno rimossi da questo gruppo se supereranno il valore massimo. Impostare su 0 se non si desidera che gli utenti vengano rimossi automaticamente.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Imposta come gruppo predefinito',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Rendi questo gruppo predefinito per l’utente.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Ciò non influirà sugli utenti il cui gruppo di utenti predefinito è uno dei seguenti: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notifica utenti',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Invia una notifica agli utenti automaticamente aggiunti o rimossi dal gruppo.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Imposta eccezioni per gruppo predefinito',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Autogruppi non cambierà il gruppo predefinito se fa parte di un gruppo selezionato in questa lista. Per selezionare più gruppi, tenere premuto <samp>CTRL</samp> (o <samp>&#8984;CMD</samp> su sistemi Mac) e selezionare i gruppi.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Crea nuovo autogruppo',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Autogruppo creato correttamente.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Sicuro di voler rimuovere quest’autogruppo?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Autogruppo rimosso correttamente.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Non ci sono autogruppi.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Non ci sono gruppi a disposizione',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Si è verificato un errore: non è stato selezionato un gruppo utente valido.<br />Autogruppi può essere usato solo con gruppi utenti, creabili nella pagina Gestisci gruppi.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Si è verificato un errore: il valore minimo e il valore massimo non possono coincidere.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Età utente',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Giorni dall’ultima visita',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Giorni di appartenenza',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Messaggi',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Richiami',
));

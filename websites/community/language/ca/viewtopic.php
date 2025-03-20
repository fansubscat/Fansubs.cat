<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
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

$lang = array_merge($lang, array(
	'APPROVE'							=> 'Aprova',
	'ATTACHMENT'						=> 'Fitxer adjunt',
	'ATTACHMENT_FUNCTIONALITY_DISABLED'	=> 'La funció de fitxers adjunts està inhabilitada.',

	'BOOKMARK_ADDED'		=> 'S’ha afegit el tema a les adreces d’interès correctament.',
	'BOOKMARK_ERR'			=> 'No s’ha pogut afegir el tema a les adreces d’interès. Torna-ho a provar.',
	'BOOKMARK_REMOVED'		=> 'S’ha eliminat el tema de les adreces d’interès correctament.',
	'BOOKMARK_TOPIC'		=> 'Afegeix aquest tema a les adreces d’interès',
	'BOOKMARK_TOPIC_REMOVE'	=> 'Elimina aquest tema de les adreces d’interès',
	'BUMPED_BY'				=> '%1$s l’ha reactivat per darrera vegada el dia: %2$s.',
	'BUMP_TOPIC'			=> 'Reactiva el tema',

	'DELETE_TOPIC'			=> 'Elimina el tema',
	'DELETED_INFORMATION'	=> 'Eliminat per %1$s a %2$s',
	'DISAPPROVE'					=> 'Rebutja',
	'DOWNLOAD_NOTICE'		=> 'No tens els permisos necessaris per a veure els fitxers adjunts d’aquest missatge.',

	'EDITED_TIMES_TOTAL'	=> array(
		1	=> '%2$s l’ha editat per darrera vegada el dia %3$s; en total s’ha editat %1$d vegada.',
		2	=> '%2$s l’ha editat per darrera vegada el dia %3$s; en total s’ha editat %1$d vegades.',
	),
	'EMAIL_TOPIC'			=> 'Envia el tema per correu electrònic',
	'ERROR_NO_ATTACHMENT'	=> 'El fitxer adjunt seleccionat ja no existeix.',

	'FILE_NOT_FOUND_404'	=> 'El fitxer <strong>%s</strong> no existeix.',
	'FORK_TOPIC'			=> 'Copia el tema',
	'FULL_EDITOR'			=> 'Editor complet i previsualització',

	'LINKAGE_FORBIDDEN'		=> 'No tens permís per a veure, baixar o enllaçar de o a aquest lloc web.',
	'LOGIN_NOTIFY_TOPIC'	=> 'Has rebut una notificació d’aquest tema: inicia sessió per a veure’l.',
	'LOGIN_VIEWTOPIC'		=> 'Cal que hagis iniciat la sessió per a veure aquest tema.',

	'MAKE_ANNOUNCE'				=> 'Canvia’l a «Avís»',
	'MAKE_GLOBAL'				=> 'Canvia’l a «Global»',
	'MAKE_NORMAL'				=> 'Canvia’l a «Tema estàndard»',
	'MAKE_STICKY'				=> 'Canvia’l a «Tema recurrent»',
	'MAX_OPTIONS_SELECT'		=> array(
		1	=> 'Pots seleccionar <strong>%d</strong> opció',
		2	=> 'Pots seleccionar fins a <strong>%d</strong> opcions',
	),
	'MISSING_INLINE_ATTACHMENT'	=> 'El fitxer adjunt <strong>%s</strong> ja no està disponible',
	'MOVE_TOPIC'				=> 'Desplaça el tema',

	'NO_ATTACHMENT_SELECTED'=> 'No has seleccionat cap fitxer adjunt per a baixar o veure.',
	'NO_NEWER_TOPICS'		=> 'No hi ha temes més recents en aquest fòrum.',
	'NO_OLDER_TOPICS'		=> 'No hi ha temes més antics en aquest fòrum.',
	'NO_UNREAD_POSTS'		=> 'No hi ha missatges no llegits en aquest tema.',
	'NO_VOTE_OPTION'		=> 'Quan votes, cal que triïs una opció.',
	'NO_VOTES'				=> 'No hi ha cap vot',
	'NO_AUTH_PRINT_TOPIC'	=> 'No tens permís per a imprimir temes.',

	'POLL_ENDED_AT'			=> 'L’enquesta ha finalitzat el %s',
	'POST_DELETED_RESTORE'	=> 'Aquest missatge està eliminat, però es pot restaurar.',
	'POLL_RUN_TILL'			=> 'L’enquesta durarà fins a %s',
	'POLL_VOTED_OPTION'		=> 'Has votat aquesta opció',
	'PRINT_TOPIC'			=> 'Visualització d’impressió',

	'QUICK_MOD'				=> 'Eines de moderació ràpida',
	'QUICKREPLY'			=> 'Resposta ràpida',

	'REPLY_TO_TOPIC'		=> 'Respon al tema',
	'RESTORE'				=> 'Restaura',
	'RESTORE_TOPIC'			=> 'Restaura el tema',
	'RETURN_POST'			=> '%sTorna al missatge%s',

	'SUBMIT_VOTE'			=> 'Envia el vot',

	'TOPIC_TOOLS'			=> 'Eines del tema',
	'TOTAL_VOTES'			=> 'Vots totals',

	'UNLOCK_TOPIC'			=> 'Desbloca el tema',

	'VIEW_INFO'				=> 'Detalls del missatge',
	'VIEW_NEXT_TOPIC'		=> 'Tema següent',
	'VIEW_PREVIOUS_TOPIC'	=> 'Tema anterior',
	'VIEW_QUOTED_POST'		=> 'Mostra el missatge citat',
	'VIEW_RESULTS'			=> 'Mostra els resultats',
	'VIEW_TOPIC_POSTS'		=> array(
		1	=> '%d missatge',
		2	=> '%d missatges',
	),
	'VIEW_UNREAD_POST'		=> 'Primer missatge no llegit',
	'VOTE_SUBMITTED'		=> 'S’ha registrat el teu vot.',
	'VOTE_CONVERTED'		=> 'No es permet el canvi de vot en enquestes convertides d’un altre tipus de fòrum.',

));

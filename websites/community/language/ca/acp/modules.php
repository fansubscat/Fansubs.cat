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
	'ACP_MODULE_MANAGEMENT_EXPLAIN'	=> 'Aquí podeu gestionar tot tipus de mòduls. Tingueu en compte que el TCA té una estructura de menús de tres nivells (Categoria -> Categoria -> Mòdul) mentre que els altres tenen una estructura de menús de dos nivells (Categoria -> Mòdul) que s’ha de mantenir. També heu de ser conscients que és possible que bloquegeu el vostre propi accés si inhabiliteu o elimineu els moduls responsables de la pròpia gestió de mòduls.',
	'ADD_MODULE'					=> 'Afegeix el mòdul',
	'ADD_MODULE_CONFIRM'			=> 'Esteu segur que voleu afegir el mòdul seleccionat amb el mode seleccionat?',
	'ADD_MODULE_TITLE'				=> 'Afegeix el mòdul',

	'CANNOT_REMOVE_MODULE'	=> 'No es pot eliminar el mòdul ja que té fills assignats. Elimineu o moveu tots els fills abans de realitzar aquesta acció.',
	'CATEGORY'				=> 'Categoria',
	'CHOOSE_MODE'			=> 'Trieu el mode del mòdul',
	'CHOOSE_MODE_EXPLAIN'	=> 'Trieu el mode d’aquest mòdul que voleu usar.',
	'CHOOSE_MODULE'			=> 'Trieu el mòdul',
	'CHOOSE_MODULE_EXPLAIN'	=> 'Trieu el fitxer que crida aquest mòdul.',
	'CREATE_MODULE'			=> 'Crea un mòdul nou',

	'DEACTIVATED_MODULE'	=> 'Mòdul desactivat',
	'DELETE_MODULE'			=> 'Eliminació del mòdul',
	'DELETE_MODULE_CONFIRM'	=> 'Esteu segur que voleu eliminar aquest mòdul?',

	'EDIT_MODULE'			=> 'Edició del mòdul',
	'EDIT_MODULE_EXPLAIN'	=> 'Aquí podeu seleccionar configuracions específiques del mòdul.',

	'HIDDEN_MODULE'			=> 'Mòdul ocult',

	'MODULE'					=> 'Mòdul',
	'MODULE_ADDED'				=> 'S’ha afegit el mòdul correctament.',
	'MODULE_DELETED'			=> 'S’ha eliminat el mòdul correctament.',
	'MODULE_DISPLAYED'			=> 'Mòdul mostrat',
	'MODULE_DISPLAYED_EXPLAIN'	=> 'Si no voleu mostrar aquest mòdul, però voleu usar-lo, indiqueu l’opció No.',
	'MODULE_EDITED'				=> 'S’ha editat el mòdul correctament.',
	'MODULE_ENABLED'			=> 'Mòdul habilitat',
	'MODULE_LANGNAME'			=> 'Nom del mòdul',
	'MODULE_LANGNAME_EXPLAIN'	=> 'Introduïu el nom que es mostrarà per al mòdul. Utilitzeu una constant d’idioma si el nom es serveix des d’un fitxer d’idioma.',
	'MODULE_TYPE'				=> 'Tipus de mòdul',

	'NO_CATEGORY_TO_MODULE'	=> 'No s’ha pogut convertir la categoria en un mòdul. Moveu o elimineu-ne tots els fills abans de realitzar aquesta acció.',
	'NO_MODULE'				=> 'No s’ha trobat cap mòdul.',
	'NO_MODULE_ID'			=> 'No heu especificat un id de mòdul.',
	'NO_MODULE_LANGNAME'	=> 'No heu especificat un nom per al mòdul.',
	'NO_PARENT'				=> 'Sense pare',

	'PARENT'				=> 'Pare',
	'PARENT_NO_EXIST'		=> 'El pare no existeix.',

	'SELECT_MODULE'			=> 'Seleccioneu un mòdul',
));

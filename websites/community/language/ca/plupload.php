<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @copyright (c) 2010-2013 Moxiecode Systems AB
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
	'PLUPLOAD_ADD_FILES'		=> 'Afegeix fitxers',
	'PLUPLOAD_ADD_FILES_TO_QUEUE'	=> 'Afegiu fitxers a la cua de penjades i feu clic al botó d’inici.',
	'PLUPLOAD_ALREADY_QUEUED'	=> '%s ja és a la cua.',
	'PLUPLOAD_CLOSE'			=> 'Tanca',
	'PLUPLOAD_DRAG'				=> 'Arrossegueu els fitxers aquí.',
	'PLUPLOAD_DUPLICATE_ERROR'	=> 'Error de fitxer duplicat.',
	'PLUPLOAD_DRAG_TEXTAREA'	=> 'També podeu adjuntar fitxers arrossegant-los i deixant-los anar al quadre d’edició.',
	'PLUPLOAD_ERR_INPUT'		=> 'No s’ha pogut obrir el flux d’entrada.',
	'PLUPLOAD_ERR_MOVE_UPLOADED'	=> 'No s’ha pogut moure el fitxer penjat.',
	'PLUPLOAD_ERR_OUTPUT'		=> 'No s’ha pogut obrir el flux de sortida.',
	'PLUPLOAD_ERR_FILE_TOO_LARGE'	=> 'El fitxer és massa gran:',
	'PLUPLOAD_ERR_FILE_COUNT'	=> 'Error de nombre de fitxers.',
	'PLUPLOAD_ERR_FILE_INVALID_EXT'	=> 'Extensió de fitxer no vàlida:',
	'PLUPLOAD_ERR_RUNTIME_MEMORY'	=> 'El procés d’execució s’ha quedat sense memòria.',
	'PLUPLOAD_ERR_UPLOAD_URL'	=> 'L’URL de penjades és incorrecte o no existeix.',
	'PLUPLOAD_EXTENSION_ERROR'	=> 'Error d’extensió de fitxer.',
	'PLUPLOAD_FILE'				=> 'Fitxer: %s',
	'PLUPLOAD_FILE_DETAILS'		=> 'Fitxer: %s, mida: %d, mida màxima: %d',
	'PLUPLOAD_FILENAME'			=> 'Nom de fitxer',
	'PLUPLOAD_FILES_QUEUED'		=> '%d fitxers a la cua',
	'PLUPLOAD_GENERIC_ERROR'	=> 'Error genèric.',
	'PLUPLOAD_HTTP_ERROR'		=> 'Error d’HTTP.',
	'PLUPLOAD_IMAGE_FORMAT'		=> 'El format de la imatge és incorrecte o no es permet.',
	'PLUPLOAD_INIT_ERROR'		=> 'Error d’inicialització.',
	'PLUPLOAD_IO_ERROR'			=> 'Error E/S.',
	'PLUPLOAD_NOT_APPLICABLE'	=> 'N/D',
	'PLUPLOAD_SECURITY_ERROR'	=> 'Error de seguretat.',
	'PLUPLOAD_SELECT_FILES'		=> 'Seleccioneu els fitxers',
	'PLUPLOAD_SIZE'				=> 'Mida',
	'PLUPLOAD_SIZE_ERROR'		=> 'Error de mida de fitxer.',
	'PLUPLOAD_STATUS'			=> 'Estat',
	'PLUPLOAD_START_UPLOAD'		=> 'Inicia la penjada',
	'PLUPLOAD_START_CURRENT_UPLOAD'	=> 'Inicia la cua de penjades',
	'PLUPLOAD_STOP_UPLOAD'		=> 'Atura la penjada',
	'PLUPLOAD_STOP_CURRENT_UPLOAD'	=> 'Atura la penjada en curs',
	// Note: This string is formatted independently by plupload and so does not
	// use the same formatting rules as normal phpBB translation strings
	'PLUPLOAD_UPLOADED'			=> 'S’han penjat %d/%d fitxers',
));

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
	'ACP_FILES'						=> 'Fitxers d’idioma de l’administrador',
	'ACP_LANGUAGE_PACKS_EXPLAIN'	=> 'Aquí podeu instal·lar/eliminar paquets d’idioma. El paquet d’idioma per defecte està marcat amb un asterisc (*).',

	'DELETE_LANGUAGE_CONFIRM'		=> 'Esteu segur que voleu eliminar “%s”?',

	'INSTALLED_LANGUAGE_PACKS'	=> 'Paquets d’idioma instal·lats',

	'LANGUAGE_DETAILS_UPDATED'			=> 'S’ha actualitzat la informació de l’idioma correctament.',
	'LANGUAGE_PACK_ALREADY_INSTALLED'	=> 'Aquest paquet d’idioma ja està instal·lat.',
	'LANGUAGE_PACK_DELETED'				=> 'S’ha eliminat el paquet d’idioma “%s” correctament. A tots els usuaris que utilitzaven aquest idioma se’ls ha assignat l’idioma per defecte del fòrum.',
	'LANGUAGE_PACK_DETAILS'				=> 'Informació del paquet d’idioma',
	'LANGUAGE_PACK_INSTALLED'			=> 'S’ha instal·lat el paquet d’idioma “%s” correctament.',
	'LANGUAGE_PACK_CPF_UPDATE'			=> 'Les cadenes d’idioma dels camps de perfil personalitzats s’han copiat des de l’idioma per defecte. Canvieu-los si fos necessari.',
	'LANGUAGE_PACK_ISO'					=> 'ISO',
	'LANGUAGE_PACK_LOCALNAME'			=> 'Nom local',
	'LANGUAGE_PACK_NAME'				=> 'Nom',
	'LANGUAGE_PACK_NOT_EXIST'			=> 'El paquet d’idioma seleccionat no existeix.',
	'LANGUAGE_PACK_USED_BY'				=> 'Utilitzat per (incloent-hi els robots)',
	'LANGUAGE_VARIABLE'					=> 'Variable d’idioma',
	'LANG_AUTHOR'						=> 'Autor del paquet d’idioma',
	'LANG_ENGLISH_NAME'					=> 'Nom en anglès',
	'LANG_ISO_CODE'						=> 'Codi ISO',
	'LANG_LOCAL_NAME'					=> 'Nom local',

	'MISSING_LANG_FILES'		=> 'Fitxers d’idioma absents',
	'MISSING_LANG_VARIABLES'	=> 'Variables d’idioma absents',

	'NO_FILE_SELECTED'				=> 'No heu especificat un fitxer d’idioma.',
	'NO_LANG_ID'					=> 'No heu especificat un paquet d’idioma.',
	'NO_REMOVE_DEFAULT_LANG'		=> 'No podeu eliminar el paquet d’idioma per defecte.<br />Per eliminar aquest paquet d’idioma, canvieu abans l’idioma per defecte del fòrum.',
	'NO_UNINSTALLED_LANGUAGE_PACKS'	=> 'No hi ha paquets d’idioma desinstal·lats',

	'THOSE_MISSING_LANG_FILES'			=> 'Falten els fitxers d’idioma següents al directori d’idioma “%s”',
	'THOSE_MISSING_LANG_VARIABLES'		=> 'Falten les variables d’idioma següents al paquet d’idioma “%s”',

	'UNINSTALLED_LANGUAGE_PACKS'	=> 'Paquets d’idioma desinstal·lats',

	'BROWSE_LANGUAGE_PACKS_DATABASE'	=> 'Explora la base de dades de paquets d’idioma',
));

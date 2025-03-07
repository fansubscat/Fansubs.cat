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
	'CONFIG_NOT_EXIST'					=> 'El paràmetre de configuració "%s" no existeix (error no esperat).',

	'GROUP_NOT_EXIST'					=> 'El grup "%s" no existeix (error no esperat).',

	'MIGRATION_APPLY_DEPENDENCIES'		=> 'Aplica les dependències de %s.',
	'MIGRATION_DATA_DONE'				=> 'Dades instal·lades: %1$s; Temps: %2$.2f segons',
	'MIGRATION_DATA_IN_PROGRESS'		=> 'S’estan instal·lant les dades: %1$s; Temps: %2$.2f segons',
	'MIGRATION_DATA_RUNNING'			=> 'S’estan instal·lant les dades: %s.',
	'MIGRATION_EFFECTIVELY_INSTALLED'	=> 'Migració ja instal·lada de forma efectiva (s’ha omès): %s',
	'MIGRATION_EXCEPTION_ERROR'			=> 'S’ha produït un error durant la sol·licitud i s’ha llançat una excepció. Els canvis fets abans de l’error s’han invertit dintre del que és possible, però és recomanable que verifique el fòrum per si hi ha errors.',
	'MIGRATION_NOT_FULFILLABLE'			=> 'La migració "%1$s" no es pot completar, falta la migració "%2$s".',
	'MIGRATION_NOT_INSTALLED'			=> 'La migració "%s" no està instal·lada.',
	'MIGRATION_NOT_VALID'				=> '%s no és una migració vàlida.',
	'MIGRATION_SCHEMA_DONE'				=> 'Esquema instal·lat: %1$s; Temps: %2$.2f segons',
	'MIGRATION_SCHEMA_IN_PROGRESS'		=> 'S’està instal·lant l’esquema: %1$s; Temps: %2$.2f segons',
	'MIGRATION_SCHEMA_RUNNING'			=> 'S’està instal·lant l’esquema: %s.',

	'MIGRATION_REVERT_DATA_DONE'		=> 'Dades revertides: %1$s; Temps: %2$.2f segons',
	'MIGRATION_REVERT_DATA_IN_PROGRESS'	=> 'S’estan revertint les dades: %1$s; Temps: %2$.2f segons',
	'MIGRATION_REVERT_DATA_RUNNING'		=> 'S’estan revertint les dades: %s.',
	'MIGRATION_REVERT_SCHEMA_DONE'		=> 'Esquema revertit: %1$s; Temps: %2$.2f segons',
	'MIGRATION_REVERT_SCHEMA_IN_PROGRESS'	=> 'S’està revertint l’esquema: %1$s; Temps: %2$.2f segons',
	'MIGRATION_REVERT_SCHEMA_RUNNING'	=> 'S’està revertint l’esquema: %s.',

	'MIGRATION_INVALID_DATA_MISSING_CONDITION'		=> 'Una migració no és vàlida. A una sentencia “if” d’ajuda li falta una condició.',
	'MIGRATION_INVALID_DATA_MISSING_STEP'			=> 'Una migració no és vàlida. A una sentencia “if” d’ajuda li falta una crida vàlida a un pas de migració.',
	'MIGRATION_INVALID_DATA_CUSTOM_NOT_CALLABLE'	=> 'Una migració no és vàlida. No s’ha pogut cridar una funció cridable personalitzada.',
	'MIGRATION_INVALID_DATA_UNKNOWN_TYPE'			=> 'Una migració no és vàlida. S’ha trobat un tipus d’eina de migració desconegut.',
	'MIGRATION_INVALID_DATA_UNDEFINED_TOOL'			=> 'Una migració no és vàlida. S’ha trobat una eina de migració no definida.',
	'MIGRATION_INVALID_DATA_UNDEFINED_METHOD'		=> 'Una migració no és vàlida. S’ha trobat una mètode d’eina de migració no definit.',

	'MODULE_ERROR'						=> 'S’ha produït un error mentre es creava un mòdul: %s',
	'MODULE_EXISTS'						=> 'Ja existeix un mòdul: %s',
	'MODULE_EXIST_MULTIPLE'				=> 'Ja existeixen diversos mòduls amb el nom de mòdul proporcionat per al mòdul pare: %s. Proveu a usar claus com ara pre/post per fer més clara la ubicació dels mòduls.',
	'MODULE_INFO_FILE_NOT_EXIST'		=> 'Falta un fitxer necessari d’informació del mòdul: %2$s',
	'MODULE_NOT_EXIST'					=> 'No existeix un mòdul necessari: %s',

	'PARENT_MODULE_FIND_ERROR'			=> 'No s’ha pogut determinar l’identificador del mòdul pare: %s',
	'PERMISSION_NOT_EXIST'				=> 'The permission setting "%s" no existeix (error no esperat).',

	'ROLE_ASSIGNED_NOT_EXIST'			=> 'El rol de permisos assignat al grup "%1$s" no existeix (error no esperat). Id del rol: "%2$s"',
	'ROLE_NOT_EXIST'					=> 'El rol de permisos "%s" no existeix (error no esperat).',
));

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

// Database Backup/Restore
$lang = array_merge($lang, array(
	'ACP_BACKUP_EXPLAIN'	=> 'Aquí podeu fer una còpia de seguretat de totes les dades del phpBB. El fitxer resultant s’emmagatzemarà al directori <samp>store/</samp>. Depenent de la configuració del servidor, és possible que pugueu comprimir el fitxer en diversos formats.',
	'ACP_RESTORE_EXPLAIN'	=> 'Es realitzarà una restauració completa de totes les taules del phpBB des d’un fitxer desat. Si el servidor ho permet, podeu usar un fitxer de text comprimit en format gzip o bzip2 i es descomprimirà automàticament. <strong>ADVERTIMENT</strong> Aquest procediment sobreescriurà totes les dades existents. La restauració pot trigar molt en ser processada, no us mogueu d’aquesta pàgina fins que hagi acabat. Les còpies de seguretat s’emmagatzemen al directori <samp>store/</samp> i s’assumeix que han estat generades amb la funció de còpia de seguretat del phpBB. La restauració amb còpies de seguretat que no han estat creades pel sistema integrat poden no funcionar correctament.',

	'BACKUP_DELETE'			=> 'S’ha eliminat el fitxer de còpia de seguretat correctament.',
	'BACKUP_INVALID'		=> 'El fitxer de còpia de seguretat seleccionat no és vàlid.',
	'BACKUP_NOT_SUPPORTED'	=> 'La còpia de seguretat seleccionada no és compatible',
	'BACKUP_OPTIONS'		=> 'Opcions de còpia de seguretat',
	'BACKUP_SUCCESS'		=> 'El fitxer de còpia de seguretat s’ha creat correctament.',
	'BACKUP_TYPE'			=> 'Tipus de còpia de seguretat',

	'DATABASE'			=> 'Utilitats de bases de dades',
	'DATA_ONLY'			=> 'Només les dades',
	'DELETE_BACKUP'		=> 'Esborra la còpia de seguretat',
	'DELETE_SELECTED_BACKUP'	=> 'Esteu segur que voleu eliminar la còpia de seguretat seleccionada?',
	'DESELECT_ALL'		=> 'Desselecciona-les totes',
	'DOWNLOAD_BACKUP'	=> 'Baixa la còpia de seguretat',

	'FILE_TYPE'			=> 'Tipus de fitxer',
	'FILE_WRITE_FAIL'	=> 'No s’ha pogut escriure el fitxer al directori d’emmagatzemament.',
	'FULL_BACKUP'		=> 'Completa',

	'RESTORE_FAILURE'		=> 'És possible que el fitxer de còpia de seguretat estigui malmès.',
	'RESTORE_OPTIONS'		=> 'Opcions de restauració',
	'RESTORE_SELECTED_BACKUP'	=> 'Esteu segur que voleu restaurar la còpia de seguretat seleccionada?',
	'RESTORE_SUCCESS'		=> 'S’ha restaurat la base de dades correctament.<br /><br />El fòrum ha de trobar-se en el mateix estat que quan es va fer la còpia de seguretat.',

	'SELECT_ALL'			=> 'Selecciona-les totes',
	'SELECT_FILE'			=> 'Seleccioneu un fitxer',
	'START_BACKUP'			=> 'Inicia la còpia de seguretat',
	'START_RESTORE'			=> 'Inicia la restauració',
	'STORE_AND_DOWNLOAD'	=> 'Emmagatzema’l i baixa’l',
	'STORE_LOCAL'			=> 'Emmagatzema el fitxer localment',

	'TABLE_SELECT'		=> 'Selecció de taules',
	'TABLE_SELECT_ERROR'=> 'Cal que seleccioneu una taula com a mínim.',
));

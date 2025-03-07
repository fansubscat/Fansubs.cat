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

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* DO NOT CHANGE
*/
if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine

$lang = array_merge($lang, array(
	'CLI_APCU_CACHE_NOTICE'				=> 'La memòria cau APCu l’heu de purgar des del Tauler de control de l’administrador.',

	'CLI_CONFIG_CANNOT_CACHED'			=> 'Marqueu aquesta opció si l’opció de configuració canvia massa sovint com per emmagatzemar-la de forma eficient a la memòria cau.',
	'CLI_CONFIG_CURRENT'				=> 'Valor actual de la configuració, utilitzeu 0 i 1 per especificar valors booleans',
	'CLI_CONFIG_DELETE_SUCCESS'			=> 'S’ha eliminat la configuració %s correctament',
	'CLI_CONFIG_NEW'					=> 'Nou valor de la configuració, utilitzeu 0 i 1 per especificar valors booleans',
	'CLI_CONFIG_NOT_EXISTS'				=> 'La configuració %s no existeix',
	'CLI_CONFIG_OPTION_NAME'			=> 'El nom de l’opció de configuració',
	'CLI_CONFIG_PRINT_WITHOUT_NEWLINE'	=> 'Marqueu aquesta opció si el valor s’ha d’imprimir sense una linia nova al final.',
	'CLI_CONFIG_INCREMENT_BY'			=> 'Quantitat a incrementar',
	'CLI_CONFIG_INCREMENT_SUCCESS'		=> 'S’ha incrementat la configuració %s correctament',
	'CLI_CONFIG_SET_FAILURE'			=> 'No s’ha pogut establir la configuració %s',
	'CLI_CONFIG_SET_SUCCESS'			=> 'S’ha establert la configuració %s correctament',

	'CLI_DESCRIPTION_CRON_LIST'					=> 'Imprimeix una llista dels treballs cron llestos i no llestos.',
	'CLI_DESCRIPTION_CRON_RUN'					=> 'Executa totes les tasques cron que estan llestes.',
	'CLI_DESCRIPTION_CRON_RUN_ARGUMENT_1'		=> 'Indiqueu la tasca a executar',
	'CLI_DESCRIPTION_DB_LIST'					=> 'Llista totes les migracions instal·lades i disponibles.',
	'CLI_DESCRIPTION_DB_MIGRATE'				=> 'Actualitza la base de dades aplicant migracions.',
	'CLI_DESCRIPTION_DB_REVERT'					=> 'Revertir una migració.',
	'CLI_DESCRIPTION_DELETE_CONFIG'				=> 'Elimina una opció de configuració',
	'CLI_DESCRIPTION_DISABLE_EXTENSION'			=> 'Inhabilita l’extensió especificada.',
	'CLI_DESCRIPTION_ENABLE_EXTENSION'			=> 'Habilita l’extensió especificada.',
	'CLI_DESCRIPTION_FIND_MIGRATIONS'			=> 'Troba migracions en les quals no es depèn.',
	'CLI_DESCRIPTION_FIX_LEFT_RIGHT_IDS'		=> 'Repara l’estructura en arbre dels fòrums i els mòduls.',
	'CLI_DESCRIPTION_GET_CONFIG'				=> 'Obté el valor d’una opció de configuració',
	'CLI_DESCRIPTION_INCREMENT_CONFIG'			=> 'Incrementa el valor enter d’una opció de configuració',
	'CLI_DESCRIPTION_LIST_EXTENSIONS'			=> 'Lista totes les extensions a la base de dades i al sistema de fitxers.',

	'CLI_DESCRIPTION_OPTION_ENV'				=> 'El nom de l’Entorn.',
	'CLI_DESCRIPTION_OPTION_SAFE_MODE'			=> 'Executa’l en mode segur (sense extensions).',
	'CLI_DESCRIPTION_OPTION_SHELL'				=> 'Executa l’intèrpret d’ordres.',

	'CLI_DESCRIPTION_PURGE_EXTENSION'			=> 'Purga l’extensió especificada.',

	'CLI_DESCRIPTION_REPARSER_LIST'						=> 'Llista els tipus de text que es poden reanalitzar.',
	'CLI_DESCRIPTION_REPARSER_AVAILABLE'				=> 'Reanalitzadors disponibles:',
	'CLI_DESCRIPTION_REPARSER_REPARSE'					=> 'Reanalitza text emmagatzemat amb els serveis text_formatter actuals.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_ARG_1'			=> 'Tipus de text a reanalitzar. Deixeu-lo en blanc per reanalitzar-ho tot.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_DRY_RUN'		=> 'No desis els canvis; mostra només el que passaria',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_FORCE_BBCODE'	=> 'Reanalitza tots els BBCodes sense excepció. Tingueu en compte que els BBCodes inhabilitats prèviament es reprocessaran, s’habilitaran i es mostraran completament.',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_MIN'	=> 'Mínim ID de registre a processar',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_MAX'	=> 'Màxim ID de registre a processar',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RANGE_SIZE'	=> 'Nombre aproximat de registres a tractar a la vegada',
	'CLI_DESCRIPTION_REPARSER_REPARSE_OPT_RESUME'		=> 'Comença a reanalitzar on s’ha aturat la darrera execució',

	'CLI_DESCRIPTION_SET_ATOMIC_CONFIG'					=> 'Estableix el valor d’una opció de configuració només si el valor vell coincideix amb el valor nou',
	'CLI_DESCRIPTION_SET_CONFIG'						=> 'Estableix el valor d’una opció de configuració',

	'CLI_DESCRIPTION_THUMBNAIL_DELETE'					=> 'Elimina totes les miniatures existents.',
	'CLI_DESCRIPTION_THUMBNAIL_GENERATE'				=> 'Genera totes les miniatures que falten.',
	'CLI_DESCRIPTION_THUMBNAIL_RECREATE'				=> 'Torna a crear totes les miniatures.',

	'CLI_DESCRIPTION_UPDATE_CHECK'					=> 'Comprova si el fòrum està actualitzat.',
	'CLI_DESCRIPTION_UPDATE_CHECK_ARGUMENT_1'		=> 'Nom de ’extensió a comprovar (si utilitzeu l’opció “all”, comprova totes les extensions)',
	'CLI_DESCRIPTION_UPDATE_CHECK_OPTION_CACHE'		=> 'Executa l’ordre de comprovació amb memòria cau.',
	'CLI_DESCRIPTION_UPDATE_CHECK_OPTION_STABILITY'	=> 'Executa l’ordre amb l’opció de comprovar només versions estables o inestables.',

	'CLI_DESCRIPTION_UPDATE_HASH_BCRYPT'		=> 'Actualitza els resums de contrasenya obsolets per usar la funció de resum bcrypt.',

	'CLI_ERROR_INVALID_STABILITY' => '"%s" s’ha de definir com a "estable" o "inestable".',

	'CLI_DESCRIPTION_USER_ACTIVATE'				=> 'Activa (o desactiva) el compte d’un usuari.',
	'CLI_DESCRIPTION_USER_ACTIVATE_USERNAME'	=> 'Nom d’usuari del compte que voleu activar.',
	'CLI_DESCRIPTION_USER_ACTIVATE_DEACTIVATE'	=> 'Desactiva el compte de l’usuari',
	'CLI_DESCRIPTION_USER_ACTIVATE_ACTIVE'		=> 'L’usuari ja és actiu.',
	'CLI_DESCRIPTION_USER_ACTIVATE_INACTIVE'	=> 'L’usuari ja és inactiu.',
	'CLI_DESCRIPTION_USER_ADD'					=> 'Afegeix un usuari nou.',
	'CLI_DESCRIPTION_USER_ADD_OPTION_USERNAME'	=> 'Nom d’usuari del nou usuari',
	'CLI_DESCRIPTION_USER_ADD_OPTION_PASSWORD'	=> 'Contrasenya  del nou usuari',
	'CLI_DESCRIPTION_USER_ADD_OPTION_EMAIL'		=> 'Adreça electrònica del nou usuari',
	'CLI_DESCRIPTION_USER_ADD_OPTION_NOTIFY'	=> 'Envia un correu d’activació de compte al nou usuari (no s’envia per defecte)',
	'CLI_DESCRIPTION_USER_DELETE'				=> 'Elimina un compte d’usuari.',
	'CLI_DESCRIPTION_USER_DELETE_USERNAME'		=> 'Nom d’usuari de l’usuari que voleu eliminar',
	'CLI_DESCRIPTION_USER_DELETE_ID'			=> 'Elimina comptes d’usuari per ID.',
	'CLI_DESCRIPTION_USER_DELETE_ID_OPTION_ID'	=> 'IDs dels usuaris a eliminar',
 	'CLI_DESCRIPTION_USER_DELETE_OPTION_POSTS'	=> 'Elimina totes les entrades fetes per l’usuari. Si no marqueu aquesta opció, es conservaran les entrades de l’usuari.',
	'CLI_DESCRIPTION_USER_RECLEAN'				=> 'Neteja els noms d’usuaris.',

	'CLI_EXTENSION_DISABLE_FAILURE'		=> 'No s’ha pogut inhabilitar l’extensió %s',
	'CLI_EXTENSION_DISABLE_SUCCESS'		=> 'S’ha inhabilitat l’extensió %s correctament',
	'CLI_EXTENSION_DISABLED'			=> 'L’extensió %s no està habilitada',
	'CLI_EXTENSION_ENABLE_FAILURE'		=> 'No s’ha pogut habilitar l’extensió %s',
	'CLI_EXTENSION_ENABLE_SUCCESS'		=> 'S’ha habilitat l’extensió %s correctament',
	'CLI_EXTENSION_ENABLED'				=> 'L’extensió %s ja està habilitada',
	'CLI_EXTENSION_NOT_EXIST'			=> 'L’extensió %s no existeix',
	'CLI_EXTENSION_NAME'				=> 'Nom de l’extensió',
	'CLI_EXTENSION_PURGE_FAILURE'		=> 'No s’ha pogut purgar l’extensió %s',
	'CLI_EXTENSION_PURGE_SUCCESS'		=> 'S’ha purgat l’extensió %s correctament',
	'CLI_EXTENSION_UPDATE_FAILURE'		=> 'No s’ha pogut actualitzar l’extensió %s',
	'CLI_EXTENSION_UPDATE_SUCCESS'		=> 'S’ha actualitzat correctament l’extensió %s',
	'CLI_EXTENSION_NOT_FOUND'			=> 'No s’ha trobat cap extensió.',
	'CLI_EXTENSION_NOT_ENABLEABLE'		=> 'L’extensió %s no es pot habilitar',
	'CLI_EXTENSIONS_AVAILABLE'			=> 'Disponible',
	'CLI_EXTENSIONS_DISABLED'			=> 'Inhabilitada',
	'CLI_EXTENSIONS_ENABLED'			=> 'Habilitada',

	'CLI_FIXUP_FIX_LEFT_RIGHT_IDS_SUCCESS'		=> 'S’ha reparat correctament l’estructura en arbre dels fòrums i els mòduls.',
	'CLI_FIXUP_UPDATE_HASH_BCRYPT_SUCCESS'		=> 'S’han actualitzat correctament a bcrypt els resums de contrasenya obsolets.',

	'CLI_MIGRATION_NAME'					=> 'Nom de la migració, amb l’espai de noms inclòs (utilitzeu barres inclinades en lloc de barres inverses per evitar problemes).',
	'CLI_MIGRATIONS_AVAILABLE'				=> 'Migracions disponibles',
	'CLI_MIGRATIONS_INSTALLED'				=> 'Migracions instal·lades',
	'CLI_MIGRATIONS_ONLY_AVAILABLE'		    => 'Mostra només les migracions disponibles',
	'CLI_MIGRATIONS_EMPTY'                  => 'No hi ha migracions.',

	'CLI_REPARSER_REPARSE_REPARSING'		=> 'Reanalitzant %1$s (rang %2$d..%3$d)',
	'CLI_REPARSER_REPARSE_REPARSING_START'	=> 'Reanalitzant %s...',
	'CLI_REPARSER_REPARSE_SUCCESS'			=> 'El reanàlisi ha finalitzat correctament',

	// In all the case %1$s is the logical name of the file and %2$s the real name on the filesystem
	// eg: big_image.png (2_a51529ae7932008cf8454a95af84cacd) generated.
	'CLI_THUMBNAIL_DELETED'		=> 'S’ha eliminat %1$s (%2$s).',
	'CLI_THUMBNAIL_DELETING'	=> 'S’estan eliminant les miniatures',
	'CLI_THUMBNAIL_SKIPPED'		=> 'S’ha omès %1$s (%2$s).',
	'CLI_THUMBNAIL_GENERATED'	=> 'S’ha generat %1$s (%2$s).',
	'CLI_THUMBNAIL_GENERATING'	=> 'S’estan generant les miniatures',
	'CLI_THUMBNAIL_GENERATING_DONE'	=> 'S’han regenerat totes les miniatures.',
	'CLI_THUMBNAIL_DELETING_DONE'	=> 'S’han eliminat totes les miniatures.',

	'CLI_THUMBNAIL_NOTHING_TO_GENERATE'	=> 'No hi ha miniatures per generar.',
	'CLI_THUMBNAIL_NOTHING_TO_DELETE'	=> 'No hi ha miniatures per eliminar.',

	'CLI_USER_ADD_SUCCESS'			=> 'S’ha afegit correctament l’usuari %s.',
	'CLI_USER_DELETE_CONFIRM'		=> 'Esteu segur de voler eliminar ‘%s’? [y/N]',
	'CLI_USER_DELETE_ID_CONFIRM'	=> 'Esteu segur de voler eliminar els IDs d’usuari ‘%s’? [y/N]',
	'CLI_USER_DELETE_ID_SUCCESS'	=> 'S’han eliminat els IDs d’usuari correctament.',
	'CLI_USER_DELETE_ID_START'		=> 'Eliminant usuaris per ID',
	'CLI_USER_DELETE_NONE'			=> 'No s’ha eliminat cap usuari per ID d’usuari.',
	'CLI_USER_RECLEAN_START'		=> 'S’estan netejant els noms d’usuari',
	'CLI_USER_RECLEAN_DONE'			=> [
		0	=> 'Neteja completa. No ha calgut netejar cap nom d’usuari.',
		1	=> 'Neteja completa. S’ha netejat %d nom d’usuari.',
		2	=> 'Neteja completa. S’han netejat %d noms d’usuari.',
	],
));

// Additional help for commands.
$lang = array_merge($lang, array(
	'CLI_HELP_CRON_RUN'			=> $lang['CLI_DESCRIPTION_CRON_RUN'] . ' Opcionalment, podeu indicar el nom d’una tasca “cron” per que s’executi només la tasca “cron” especificada.',
	'CLI_HELP_USER_ACTIVATE'	=> 'Activeu o desactiveu un compte d’usuari utilitzant l’opció <info>--deactivate</info>.
Opcionalment, per enviar a l’usuari un correu electrònic d’activació, utilitzeu l’opció <info>--send-email</info>.',
	'CLI_HELP_USER_ADD'			=> 'L’ordre <info>%command.name%</info> afegeix un usuari nou:
Si executeu l’ordre sense opcions , se us demanarà que les introduïu.
Opcionalment, per enviar a l’usuari nou un correu electrònic, utilitzeu l’opció <info>--send-email</info>.',
	'CLI_HELP_USER_RECLEAN'		=> 'Netejar els noms d’usuari comprobarà tots els noms d’usuari emmagatzemats i s’assegurarà que també s’emmagatzemen versions netes. Els noms d’usuari nets són una forma que no distingeix entre majúscules i minúscules, normalitzada amb NFC i transformada a ASCII.',
));

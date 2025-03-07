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
	'EXTENSION'					=> 'Extension',
	'EXTENSIONS'				=> 'Extensions',
	'EXTENSIONS_ADMIN'			=> 'Gestor d’extensions',
	'EXTENSIONS_EXPLAIN'		=> 'El gestor d’extensions és una eina del fòrum phpBB que us permet gestionar l’estat de totes les extensions i veure’n informació.',
	'EXTENSION_INVALID_LIST'	=> 'L’extensió “%s” no és vàlida.<br />%s<br /><br />',
	'EXTENSION_NOT_AVAILABLE'	=> 'L’extensió seleccionada no està disponible per aquest fòrum, verifiqueu que les vostres versions del phpBB i del PHP estan permeses (veieu la pàgina de detalls).',
	'EXTENSION_DIR_INVALID'		=> 'L’extensió seleccionada té una estructura de directoris no vàlida i no es pot habilitar.',
	'EXTENSION_NOT_ENABLEABLE'	=> 'L’extensió seleccionada no es pot habilitar, verifiqueu els requeriments de l’extensió.',
	'EXTENSION_NOT_INSTALLED'	=> 'L’extensió %s no està disponible. Comproveu que l’heu instal·lada correctament.',

	'DETAILS'				=> 'Detalls',

	'EXTENSIONS_NOT_INSTALLED'	=> 'Extensions no instal·lades',
	'EXTENSIONS_DISABLED'		=> 'Extensions inhabilitades',
	'EXTENSIONS_ENABLED'		=> 'Extensions habilitades',

	'EXTENSION_DELETE_DATA'	=> 'Elimina les dades',
	'EXTENSION_DISABLE'		=> 'Inhabilita',
	'EXTENSION_ENABLE'		=> 'Habilita',

	'EXTENSION_DELETE_DATA_EXPLAIN'	=> 'Eliminar les dades d’una extensió suprimeix totes les seves dades i configuracions. Els fitxers de l’extensió es conserven per que es pugui habilitar un altre cop.',
	'EXTENSION_DISABLE_EXPLAIN'		=> 'Deshabilitar una extensió conserva els seus fitxers, dades i configuracions, però true la funcionalitat afegida per l’extensió.',
	'EXTENSION_ENABLE_EXPLAIN'		=> 'Habilitar una extensió us permet usar-la en el vostre fòrum.',

	'EXTENSION_DELETE_DATA_IN_PROGRESS'	=> 'S’estan eliminant les dades de l’extensió. No abandoneu ni refresqueu aquesta pàgina fins que es completi el procés.',
	'EXTENSION_DISABLE_IN_PROGRESS'	=> 'S’està inhabilitant l’extensió. No abandoneu ni refresqueu aquesta pàgina fins que es completi el procés.',
	'EXTENSION_ENABLE_IN_PROGRESS'	=> 'S’està habilitant l’extensió. No abandoneu ni refresqueu aquesta pàgina fins que es completi el procés.',

	'EXTENSION_DELETE_DATA_SUCCESS'	=> 'S’han eliminat les dades de l’extensió correctament',
	'EXTENSION_DISABLE_SUCCESS'		=> 'S’ha inhabilitat l’extensió correctament',
	'EXTENSION_ENABLE_SUCCESS'		=> 'S’ha habilitat l’extensió correctament',

	'EXTENSION_NAME'			=> 'Nom de l’extensió',
	'EXTENSION_ACTIONS'			=> 'Accions',
	'EXTENSION_OPTIONS'			=> 'Opcions',
	'EXTENSION_INSTALL_HEADLINE'=> 'Com s’instal·la una extensió',
	'EXTENSION_INSTALL_EXPLAIN'	=> '<ol>
			<li>Baixeu una extensió de la base de dades d’extensions del phpBB</li>
			<li>Descomprimiu l’extensió i pengeu-la a la carpeta <samp>ext/</samp> del fòrum phpBB</li>
			<li>Habiliteu l’extensió aquí, al gestor d’extensions</li>
		</ol>',
	'EXTENSION_UPDATE_HEADLINE'	=> 'Com s’actualitza una extensió',
	'EXTENSION_UPDATE_EXPLAIN'	=> '<ol>
			<li>Inhabiliteu l’extensió</li>
			<li>Elimineu els fitxers de l’extensió al sistema de fitxers</li>
			<li>Pengeu els fitxers nous</li>
			<li>Habiliteu l’extensió</li>
		</ol>',
	'EXTENSION_REMOVE_HEADLINE'	=> 'Com s’elimina completament una extensió del fòrum',
	'EXTENSION_REMOVE_EXPLAIN'	=> '<ol>
			<li>Inhabiliteu l’extensió</li>
			<li>Elimineu les dades de l’extensió</li>
			<li>Elimineu els fitxers de l’extensió al sistema de fitxers</li>
		</ol>',

	'EXTENSION_DELETE_DATA_CONFIRM'	=> 'Esteu segur que voleu eliminar les dades associades a “%s”?<br /><br />Se suprimiran totes les seves dades i configuracions i no es pot desfer!',
	'EXTENSION_DISABLE_CONFIRM'		=> 'Esteu segur que voleu inhabilitar l’extensió “%s”?',
	'EXTENSION_ENABLE_CONFIRM'		=> 'Esteu segur que voleu habilitar l’extensió “%s”?',
	'EXTENSION_FORCE_UNSTABLE_CONFIRM'	=> 'Esteu segur que voleu forçar l’ús d’una versió no estable?',

	'RETURN_TO_EXTENSION_LIST'	=> 'Torna a la llista d’extensions',

	'EXT_DETAILS'			=> 'Detalls de l’extensió',
	'DISPLAY_NAME'			=> 'Nom visualitzat',
	'CLEAN_NAME'			=> 'Nom net',
	'TYPE'					=> 'Tipus',
	'DESCRIPTION'			=> 'Descripció',
	'VERSION'				=> 'Versió',
	'HOMEPAGE'				=> 'Pàgina inicial',
	'PATH'					=> 'Camí de fitxers',
	'TIME'					=> 'Hara de llançament',
	'LICENSE'				=> 'Llicència',

	'REQUIREMENTS'			=> 'Requeriments',
	'PHPBB_VERSION'			=> 'Versió del phpBB',
	'PHP_VERSION'			=> 'Versió del PHP',
	'AUTHOR_INFORMATION'	=> 'Informació de l’autor',
	'AUTHOR_NAME'			=> 'Nom',
	'AUTHOR_EMAIL'			=> 'Adreça electrònica',
	'AUTHOR_HOMEPAGE'		=> 'Pàgina inicial',
	'AUTHOR_ROLE'			=> 'Rol',

	'NOT_UP_TO_DATE'		=> '%s no està actualitzada',
	'UP_TO_DATE'			=> '%s està actualitzada',
	'ANNOUNCEMENT_TOPIC'	=> 'Avís de llançament',
	'DOWNLOAD_LATEST'		=> 'Baixa la versió',
	'NO_VERSIONCHECK'		=> 'No s’ha proporcionat informació de comprovació de versions.',

	'VERSIONCHECK_FORCE_UPDATE_ALL'		=> 'Torna a comprovar totes les versions',
	'FORCE_UNSTABLE'					=> 'Comprova sempre si hi ha versions no estables',
	'EXTENSIONS_VERSION_CHECK_SETTINGS'	=> 'Configuració de comprovació de versions',

	'BROWSE_EXTENSIONS_DATABASE'		=> 'Explora la base de dades d’extensions',

	'META_FIELD_NOT_SET'	=> 'No s’ha establit el camp meta necessari %s.',
	'META_FIELD_INVALID'	=> 'El camp meta %s no és vàlid.',
));

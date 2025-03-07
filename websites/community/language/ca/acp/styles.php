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
	$lang = [];
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

$lang = array_merge($lang, [
	'ACP_STYLES_EXPLAIN'						=> 'Aquí podeu gestionar els estils disponibles al fòrum.<br>Tingueu en compte que no podeu desinstal·lar l’estil “<strong>prosilver</strong>” ja que és l’estil per defecte del phpBB i l’estil pare primari.',

	'CANNOT_BE_INSTALLED'						=> 'No es pot instal·lar',
	'CONFIRM_UNINSTALL_STYLES'					=> 'Esteu segur que voleu desinstal·lar els estils seleccionats?',
	'COPYRIGHT'									=> 'Copyright',

	'DEACTIVATE_DEFAULT'						=> 'No podeu desactivar l’estil per defecte.',
	'DELETE_FROM_FS'							=> 'Elimina’l del sistema de fitxers',
	'DELETE_STYLE_FILES_FAILED'					=> 'S’ha produït un error eliminant els fitxers de l’estil "%s".',
	'DELETE_STYLE_FILES_SUCCESS'				=> 'S’han eliminat els fitxers de l’estil "%s".',
	'DETAILS'									=> 'Detalls',

	'INHERITING_FROM'							=> 'Hereta de',
	'INSTALL_STYLE'								=> 'Instal·la l’estil',
	'INSTALL_STYLES'							=> 'Instal·la els estils',
	'INSTALL_STYLES_EXPLAIN'					=> 'Aquí podeu instal·lar estils nous.<br>Si no trobeu un estil concret a la llista de sota, assegureu-vos que l’estil ja estigui instal·lat. Si no està instal·lat, comproveu que l’hagueu penjat correctament.',
	'INVALID_STYLE_ID'							=> 'Id d’estil no vàlid.',

	'NO_MATCHING_STYLES_FOUND'					=> 'Cap estil coincideix amb la consulta.',
	'NO_UNINSTALLED_STYLE'						=> 'No s’han detectat estils desinstal·lats.',

	'PURGED_CACHE'								=> 'S’ha buidat la memòria cau.',

	'REQUIRES_STYLE'							=> 'Per usar aquest estil cal que tingueu l’estil "%s" instal·lat.',

	'STYLE_ACTIVATE'							=> 'Activa',
	'STYLE_ACTIVE'								=> 'Actiu',
	'STYLE_DEACTIVATE'							=> 'Desactiva',
	'STYLE_DEFAULT'								=> 'És l’estil per defecte',
	'STYLE_DEFAULT_CHANGE_INACTIVE'				=> 'Heu d’activar l’estil abans de marcar-lo com a estil per defecte.',
	'STYLE_ERR_INVALID_PARENT'					=> 'Estil pare no vàlid.',
	'STYLE_ERR_NAME_EXIST'						=> 'Ja existeix un estil amb aquest nom.',
	'STYLE_ERR_STYLE_NAME'						=> 'Heu de proporcionar un nom per aquest estil.',
	'STYLE_INSTALLED'							=> 'S’ha instal·lat l’estil "%s".',
	'STYLE_INSTALLED_RETURN_INSTALLED_STYLES'	=> 'Torna a la llista d’estils instal·lats',
	'STYLE_INSTALLED_RETURN_UNINSTALLED_STYLES'	=> 'Instal·la més estils',
	'STYLE_NAME'								=> 'Nom de l’estil',
	'STYLE_NAME_RESERVED'						=> 'No es pot instal·lar l’estil "%s" perquè el nom està reservat.',
	'STYLE_NOT_INSTALLED'						=> 'No s’ha instal·lat l’estil "%s".',
	'STYLE_PATH'								=> 'Camí de l”estil',
	'STYLE_UNINSTALL'							=> 'Desinstal·la’l',
	'STYLE_UNINSTALL_DEPENDENT'					=> 'L’estil "%s" no es pot desinstal·lar perquè es pare d’un o més estils.',
	'STYLE_UNINSTALLED'							=> 'S’ha desinstal·lat l’estil  "%s" correctament.',
	'STYLE_PHPBB_VERSION'						=> 'Versió del phpBB',
	'STYLE_USED_BY'								=> 'Utilitzat per (incloent-hi els robots)',
	'STYLE_VERSION'								=> 'Versió de l’estil',

	'UNINSTALL_PROSILVER'						=> 'No es pot desinstal·lar l’estil “prosilver”.',
	'UNINSTALL_DEFAULT'							=> 'No es pot desinstal·lar l’estil per defecte.',

	'BROWSE_STYLES_DATABASE'					=> 'Explora la base de dades d’estils',
]);

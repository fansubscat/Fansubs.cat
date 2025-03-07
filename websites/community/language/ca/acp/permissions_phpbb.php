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

/**
*	EXTENSION-DEVELOPERS PLEASE NOTE
*
*	You are able to put your permission sets into your extension.
*	The permissions logic should be added via the 'core.permissions' event.
*	You can easily add new permission categories, types and permissions, by
*	simply merging them into the respective arrays.
*	The respective language strings should be added into a language file, that
*	start with 'permissions_', so they are automatically loaded within the ACP.
*/

$lang = array_merge($lang, array(
		'ACL_CAT_ACTIONS'		=> 'Accions',
		'ACL_CAT_CONTENT'		=> 'Contingut',
		'ACL_CAT_FORUMS'		=> 'Fòrums',
		'ACL_CAT_MISC'			=> 'Miscel·lània',
		'ACL_CAT_PERMISSIONS'	=> 'Permisos',
		'ACL_CAT_PM'			=> 'Missatges privats',
		'ACL_CAT_POLLS'			=> 'Enquestes',
		'ACL_CAT_POST'			=> 'Entrada',
		'ACL_CAT_POST_ACTIONS'	=> 'Accions d’entrades',
		'ACL_CAT_POSTING'		=> 'Publicació d’entrades',
		'ACL_CAT_PROFILE'		=> 'Perfil',
		'ACL_CAT_SETTINGS'		=> 'Configuracions',
		'ACL_CAT_TOPIC_ACTIONS'	=> 'Accions de temes',
		'ACL_CAT_USER_GROUP'	=> 'Usuaris i grups',
));

// User Permissions
$lang = array_merge($lang, array(
	'ACL_U_VIEWPROFILE'	=> 'Pot veure perfils, la llista de membres i la llista d’usuaris connectats',
	'ACL_U_CHGNAME'		=> 'Pot canviar el seu nom d’usuari',
	'ACL_U_CHGPASSWD'	=> 'Pot canviar la seva contrasenya',
	'ACL_U_CHGEMAIL'	=> 'Pot canviar la seva adreça electrònica',
	'ACL_U_CHGAVATAR'	=> 'Pot canviar el seu avatar',
	'ACL_U_CHGGRP'		=> 'Pot canviar el seu grup per defecte',
	'ACL_U_CHGPROFILEINFO'	=> 'Pot canviar informació de camps del perfil',

	'ACL_U_ATTACH'		=> 'Pot adjuntar fitxers',
	'ACL_U_DOWNLOAD'	=> 'Pot baixar fitxers',
	'ACL_U_SAVEDRAFTS'	=> 'Pot desar esborranys',
	'ACL_U_CHGCENSORS'	=> 'Pot inhabilitar la censura de paraules',
	'ACL_U_SIG'			=> 'Pot usar una signatura',
	'ACL_U_EMOJI'		=> 'Pot usar emoji i caràcters de text enriquit al títol dels temes',
	
	'ACL_U_SENDPM'		=> 'Pot enviar missatges privats',
	'ACL_U_MASSPM'		=> 'Pot enviar missatges privats a múltiples usuaris',
	'ACL_U_MASSPM_GROUP'=> 'Pot enviar missatges privats a grups',
	'ACL_U_READPM'		=> 'Pot llegir missatges privats',
	'ACL_U_PM_EDIT'		=> 'Pot editar els seus missatges privats',
	'ACL_U_PM_DELETE'	=> 'Pot eliminar missatges privats de la seva carpeta',
	'ACL_U_PM_FORWARD'	=> 'Pot reenviar missatges privats',
	'ACL_U_PM_EMAILPM'	=> 'Pot enviar per correu electrònic missatges privats',
	'ACL_U_PM_PRINTPM'	=> 'Pot imprimir missatges privats',
	'ACL_U_PM_ATTACH'	=> 'Pot adjuntar fitxers als missatges privats',
	'ACL_U_PM_DOWNLOAD'	=> 'Pot baixar fitxers als missatges privats',
	'ACL_U_PM_BBCODE'	=> 'Pot usar el BBCode als missatges privats',
	'ACL_U_PM_SMILIES'	=> 'Pot usar emoticones als missatges privats',
	'ACL_U_PM_IMG'		=> 'Pot usar l’etiqueta del BBCode [img] als missatges privats',
	'ACL_U_PM_FLASH'	=> 'Pot usar l’etiqueta del BBCode [flash] als missatges privats',

	'ACL_U_SENDEMAIL'	=> 'Pot enviar correus electrònics',
	'ACL_U_SENDIM'		=> 'Pot enviar missatges instantanis',
	'ACL_U_IGNOREFLOOD'	=> 'Pot ignorar el límit d’inundació',
	'ACL_U_HIDEONLINE'	=> 'Pot ocultar la seva presència',
	'ACL_U_VIEWONLINE'	=> 'Pot veure els usuaris ocults',
	'ACL_U_SEARCH'		=> 'Pot cercar el fòrum',
));

// Forum Permissions
$lang = array_merge($lang, array(
	'ACL_F_LIST'		=> 'Pot veure el fòrum',
	'ACL_F_LIST_TOPICS' => 'Pot veure temes',
	'ACL_F_READ'		=> 'Pot llegir el fòrum',
	'ACL_F_SEARCH'		=> 'Pot cercar el fòrum',
	'ACL_F_SUBSCRIBE'	=> 'Es pot subscriure al fòrum',
	'ACL_F_PRINT'		=> 'Pot imprimir temes',
	'ACL_F_EMAIL'		=> 'Pot enviar temes per correu electrònic',
	'ACL_F_BUMP'		=> 'Pot reactivar temes',
	'ACL_F_USER_LOCK'	=> 'Pot bloquejar els seus temes',
	'ACL_F_DOWNLOAD'	=> 'Pot baixar fitxers',
	'ACL_F_REPORT'		=> 'Pot informar d’entrades',

	'ACL_F_POST'		=> 'Pot crear temes nous',
	'ACL_F_STICKY'		=> 'Pot publicar temes recurrents',
	'ACL_F_ANNOUNCE'	=> 'Pot publicar avisos',
	'ACL_F_ANNOUNCE_GLOBAL'	=> 'Pot publicar avisos globals',
	'ACL_F_REPLY'		=> 'Pot respondre als temes',
	'ACL_F_EDIT'		=> 'Pot editar les seves entrades',
	'ACL_F_DELETE'		=> 'Pot eliminar permanentment les seves entrades',
	'ACL_F_SOFTDELETE'	=> 'Pot eliminar temporalment les seves entrades<br /><em>Els moderadors, que tenen permisos d’aprovació d’entrades, poden restaurar les entrades eliminades temporalment.</em>',
	'ACL_F_IGNOREFLOOD' => 'Pot ignorar el límit d’inundació',
	'ACL_F_POSTCOUNT'	=> 'Incrementa el comptador d’entrades<br /><em>Tingueu en compte que aquesta configuració només afecta les entrades noves.</em>',
	'ACL_F_NOAPPROVE'	=> 'Pot publicar entrades sense que siguin aprovades',

	'ACL_F_ATTACH'		=> 'Pot adjuntar fitxers',
	'ACL_F_ICONS'		=> 'Pot usar les icones de tema/entrada',
	'ACL_F_BBCODE'		=> 'Pot usar el BBCode',
	'ACL_F_FLASH'		=> 'Pot usar l’etiqueta del BBCode [flash]',
	'ACL_F_IMG'			=> 'Pot usar l’etiqueta del BBCode [img]',
	'ACL_F_SIGS'		=> 'Pot usar signatures',
	'ACL_F_SMILIES'		=> 'Pot usar emoticones',
	
	'ACL_F_POLL'		=> 'Pot crear enquestes',
	'ACL_F_VOTE'		=> 'Pot votar a les enquestes',
	'ACL_F_VOTECHG'		=> 'Pot canviar el seu vot',
));

// Moderator Permissions
$lang = array_merge($lang, array(
	'ACL_M_EDIT'		=> 'Pot editar entrades',
	'ACL_M_DELETE'		=> 'Pot eliminar entrades permanentment',
	'ACL_M_SOFTDELETE'	=> 'Pot eliminar entrades de temporalment<br /><em>Els moderadors, que tenen permisos d’aprovació d’entrades, poden restaurar entrades eliminades temporalment.</em>',
	'ACL_M_APPROVE'		=> 'Pot aprovar i restaurar entrades',
	'ACL_M_REPORT'		=> 'Pot tancar i eliminar informes',
	'ACL_M_CHGPOSTER'	=> 'Pot canviar l’autor de les entrades',

	'ACL_M_MOVE'	=> 'Pot desplaçar temes',
	'ACL_M_LOCK'	=> 'Pot bloquejar temes',
	'ACL_M_SPLIT'	=> 'Pot dividir temes',
	'ACL_M_MERGE'	=> 'Pot combinar temes',

	'ACL_M_INFO'		=> 'Pot veure els detalls de les entrades',
	'ACL_M_WARN'		=> 'Pot fer advertiments a usuaris',
	'ACL_M_PM_REPORT'	=> 'Pot tancar i eliminar informes de missatges privats',
	'ACL_M_BAN'			=> 'Pot gestionar bandejos',
));

// Admin Permissions
$lang = array_merge($lang, array(
	'ACL_A_BOARD'		=> 'Pot modificar la configuració del fòrum/comprovar si hi ha actualitzacions',
	'ACL_A_SERVER'		=> 'Pot modificar la configuració de servidor/comunicacions',
	'ACL_A_JABBER'		=> 'Pot modificar la configuració del Jabber',
	'ACL_A_PHPINFO'		=> 'Pot veure la configuració del PHP',

	'ACL_A_FORUM'		=> 'Pot gestionar fòrums',
	'ACL_A_FORUMADD'	=> 'Pot crear fòrums nous',
	'ACL_A_FORUMDEL'	=> 'Pot eliminar fòrums',
	'ACL_A_PRUNE'		=> 'Pot podar fòrums',

	'ACL_A_ICONS'		=> 'Pot modificar les icones de tema/entrada i les emoticones',
	'ACL_A_WORDS'		=> 'Pot modificar les paraules censurades',
	'ACL_A_BBCODE'		=> 'Pot definir etiquetes BBCode',
	'ACL_A_ATTACH'		=> 'Pot modificar la configuració de fitxers adjunts',

	'ACL_A_USER'		=> 'Pot gestionar usuaris<br /><em>Això també inclou veure la cadena d’identificació del navegador de l’usuari a la llista d’usuaris connectats.</em>',
	'ACL_A_USERDEL'		=> 'Pot eliminar/podar usuaris',
	'ACL_A_GROUP'		=> 'Pot gestionar grups',
	'ACL_A_GROUPADD'	=> 'Pot afegir grups nous',
	'ACL_A_GROUPDEL'	=> 'Pot eliminar grups',
	'ACL_A_RANKS'		=> 'Pot gestionar els rangs',
	'ACL_A_PROFILE'		=> 'Pot gestionar els camps personalitzats del perfil',
	'ACL_A_NAMES'		=> 'Pot gestionar els noms prohibits',
	'ACL_A_BAN'			=> 'Pot gestionar els bandejos',

	'ACL_A_VIEWAUTH'	=> 'Pot veure màscares de permisos',
	'ACL_A_AUTHGROUPS'	=> 'Pot modificar els permisos de grups individuals',
	'ACL_A_AUTHUSERS'	=> 'Pot modificar els permisos d’usuaris individuals',
	'ACL_A_FAUTH'		=> 'Pot modificar la classe de permisos del fòrum',
	'ACL_A_MAUTH'		=> 'Pot modificar la classe de permisos dels moderadors',
	'ACL_A_AAUTH'		=> 'Pot modificar la classe de permisos dels administradors',
	'ACL_A_UAUTH'		=> 'Pot modificar la classe de permisos dels usuaris',
	'ACL_A_ROLES'		=> 'Pot gestionar rols',
	'ACL_A_SWITCHPERM'	=> 'Pot usar els permisos d’altres usuaris',

	'ACL_A_STYLES'		=> 'Pot gestionar els estils',
	'ACL_A_EXTENSIONS'	=> 'Pot gestionar extensions',
	'ACL_A_VIEWLOGS'	=> 'Pot veurer els registres',
	'ACL_A_CLEARLOGS'	=> 'Pot eliminar els registres',
	'ACL_A_MODULES'		=> 'Pot gestionar els mòduls',
	'ACL_A_LANGUAGE'	=> 'Pot gestionar els paquets d’idioma',
	'ACL_A_EMAIL'		=> 'Pot enviar correus eletrònics massius',
	'ACL_A_BOTS'		=> 'Pot gestionar els robots',
	'ACL_A_REASONS'		=> 'Pot gestionar les raons d’informe/denegació',
	'ACL_A_BACKUP'		=> 'Pot fer una còpia de seguretat/restaurar la base de dades',
	'ACL_A_SEARCH'		=> 'Pot gestionar la configuració de cerca i els motors',
));

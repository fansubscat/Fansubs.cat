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
	'ADMIN_SIG_PREVIEW'		=> 'Previsualització de signatura',
	'AT_LEAST_ONE_FOUNDER'	=> 'No podeu convertir aquest fundador en un usuari normal. Cal que hi hagi com a mínim un fundador habilitat al fòrum. Si voleu canviar l’estat d’aquest usuari a no fundador, abans heu d’ascendir un altre usuari per que sigui el fundador.',

	'BAN_ALREADY_ENTERED'	=> 'Aquest bandeig ja s’havia introduït anteriorment. La llista de bandeig no s’ha actualitzat.',
	'BAN_SUCCESSFUL'		=> 'S’ha introduït el bandeig correctament.',

	'CANNOT_BAN_ANONYMOUS'			=> 'No esteu autoritzat a bandejar el compte anònim. Podeu ajustar els permisos per als usuaris anònims a la pestanya de Permisos.',
	'CANNOT_BAN_FOUNDER'			=> 'No esteu autoritzat a bandejar comptes de fundadors.',
	'CANNOT_BAN_YOURSELF'			=> 'No esteu autoritzat a bandejar el vostre propi usuari.',
	'CANNOT_DEACTIVATE_BOT'			=> 'No esteu autoritzat a desactivar comptes de robots. Desactiveu el bot directament a la pàgina de bots.',
	'CANNOT_DEACTIVATE_FOUNDER'		=> 'No esteu autoritzat a desactivar comptes de fundadors.',
	'CANNOT_DEACTIVATE_YOURSELF'	=> 'No esteu autoritzat a desactivar el vostre propi compte.',
	'CANNOT_FORCE_REACT_BOT'		=> 'No esteu autoritzat a forçar la reactivació de comptes de robots. Reactiveu el bot directament a la pàgina de bots.',
	'CANNOT_FORCE_REACT_FOUNDER'	=> 'No esteu autoritzat a forçar la reactivació de comptes de fundadors.',
	'CANNOT_FORCE_REACT_YOURSELF'	=> 'No esteu autoritzat a forçar la reactivació del vostre propi compte.',
	'CANNOT_REMOVE_ANONYMOUS'		=> 'No esteu autoritzat a eliminar el compte d’usuari visitant.',
	'CANNOT_REMOVE_FOUNDER'			=> 'No esteu autoritzat a eliminar comptes de fundadors.',
	'CANNOT_REMOVE_YOURSELF'		=> 'No esteu autoritzat a eliminar el vostre propi compte.',
	'CANNOT_SET_FOUNDER_IGNORED'	=> 'No podeu ascendir a fundador usuaris ignorats.',
	'CANNOT_SET_FOUNDER_INACTIVE'	=> 'Abans d’ascendir un usuari a fundador, cal que l’activeu. Només els usuaris activats poden ser ascendits.',
	'CONFIRM_EMAIL_EXPLAIN'			=> 'Només cal que l’especifiqueu si esteu canviant l’adreça electrònica de l’usuari.',

	'DELETE_POSTS'			=> 'Elimina’n les entrades',
	'DELETE_USER'			=> 'Elimina l’usuari',
	'DELETE_USER_EXPLAIN'	=> 'Tingueu en compte que l’eliminació d’un usuari és definitiva i no es pot recuperar. Els missatges privats no llegits que hagi enviat aquest usuari s’esborraran i no estaran disponibles per als seus destinataris.',

	'FORCE_REACTIVATION_SUCCESS'	=> 'S’ha forçat la reactivació correctament.',
	'FOUNDER'						=> 'Fundador',
	'FOUNDER_EXPLAIN'				=> 'Els fundadors tenen tots els permisos administratius i no poden ser bandejats, eliminats ni modificats per membres no fundadors.',

	'GROUP_APPROVE'					=> 'Aprova el membre',
	'GROUP_DEFAULT'					=> 'Fes que sigui el grup per defecte del membre',
	'GROUP_DELETE'					=> 'Treu el membre del grup',
	'GROUP_DEMOTE'					=> 'Degrada el líder del grup',
	'GROUP_PROMOTE'					=> 'Ascendeix l’usuari a líder del grup',

	'IP_WHOIS_FOR'			=> 'Whois d’IP per %s',

	'LAST_ACTIVE'			=> 'Darrera connexió',

	'MOVE_POSTS_EXPLAIN'	=> 'Seleccioneu el fòrum al qual voleu moure totes les entrades que ha fet aquest usuari.',

    'NO_SPECIAL_RANK'		=> 'Sense rang especial',
	'NO_WARNINGS'			=> 'Sense advertiments.',
	'NOT_MANAGE_FOUNDER'	=> 'Heu intentat gestionar un usuari amb estat de fundador. Només els fundadors poden gestionar altres fundadors.',

	'QUICK_TOOLS'			=> 'Eines ràpides',

	'REGISTERED'			=> 'Registrat',
	'REGISTERED_IP'			=> 'Registrat des de l’adreça IP',
	'RETAIN_POSTS'			=> 'Conserva’n les entrades',

	'SELECT_FORM'			=> 'Formulari de selecció',
	'SELECT_USER'			=> 'Selecciona un usuari',

	'USER_ADMIN'					=> 'Administració d’usuaris',
	'USER_ADMIN_ACTIVATE'			=> 'Activa el compte',
	'USER_ADMIN_ACTIVATED'			=> 'S’ha activat l’usuari correctament.',
	'USER_ADMIN_AVATAR_REMOVED'		=> 'S’ha eliminat correctament l’avatar del compte de l’usuari.',
	'USER_ADMIN_BAN_EMAIL'			=> 'Bandeja per adreça electrònica',
	'USER_ADMIN_BAN_EMAIL_REASON'	=> 'Adreça electrònica bandejada a través de la gestió d’usuaris',
	'USER_ADMIN_BAN_IP'				=> 'Bandeja per adreça IP',
	'USER_ADMIN_BAN_IP_REASON'		=> 'Adreça IP bandejada a través de la gestió d’usuaris',
	'USER_ADMIN_BAN_NAME_REASON'	=> 'Nom d’usuari bandejat a través de la gestió d’usuaris',
	'USER_ADMIN_BAN_USER'			=> 'Bandeja per nom d’usuari',
	'USER_ADMIN_DEACTIVATE'			=> 'Desactiva el compte',
	'USER_ADMIN_DEACTIVED'			=> 'S’ha desactivat l’usuari correctament.',
	'USER_ADMIN_DEL_ATTACH'			=> 'Elimina tots els fitxers adjunts',
	'USER_ADMIN_DEL_AVATAR'			=> 'Elimina l’avatar',
	'USER_ADMIN_DEL_OUTBOX'			=> 'Buida la safata de sortida de missatges privats',
	'USER_ADMIN_DEL_POSTS'			=> 'Elimina totes les entrades',
	'USER_ADMIN_DEL_SIG'			=> 'Elimina la signatura',
	'USER_ADMIN_EXPLAIN'			=> 'Aquí podeu canviar la informació dels usuaris i certes opcions específiques.',
	'USER_ADMIN_FORCE'				=> 'Força la reactivació',
	'USER_ADMIN_LEAVE_NR'			=> 'Treu-lo del grup de nous usuaris registrats',
	'USER_ADMIN_MOVE_POSTS'			=> 'Desplaça totes les entrades',
	'USER_ADMIN_SIG_REMOVED'		=> 'S’ha eliminat correctament la signatura del compte de l’usuari.',
	'USER_ATTACHMENTS_REMOVED'		=> 'S’han eliminat correctament tots els fitxers adjuntats per aquest usuari.',
	'USER_AVATAR_NOT_ALLOWED'		=> 'No es pot mostrar l’avatar perquè els avatars estan inhabilitats.',
	'USER_AVATAR_UPDATED'			=> 'S’ha actualitzat correctament la informació de l’avatar de l’usuari.',
	'USER_AVATAR_TYPE_NOT_ALLOWED'	=> 'L’avatar actual no es pot mostrar perquè aquest tipus d’avatar està inhabilitat.',
	'USER_CUSTOM_PROFILE_FIELDS'	=> 'Camps de perfil personalitzats',
	'USER_DELETED'					=> 'S’ha eliminat correctament l’usuari.',
	'USER_GROUP_ADD'				=> 'Afegeix l’usuari al grup',
	'USER_GROUP_NORMAL'				=> 'Grups definits per usuaris dels quals l’usuari és membre',
	'USER_GROUP_PENDING'			=> 'Grups en els quals l’usuari està en mode pendent',
	'USER_GROUP_SPECIAL'			=> 'Grups predefinits dels quals l’usuari és membre',
	'USER_LIFTED_NR'				=> 'S’ha eliminat l’estat de nou usuari registrat correctament.',
	'USER_NO_ATTACHMENTS'			=> 'No hi ha cap fitxer adjunt per mostrar.',
	'USER_NO_POSTS_TO_DELETE'		=> 'L’usuari no te cap entrada per mantenir o eliminar.',
	'USER_OUTBOX_EMPTIED'			=> 'S’ha buidat la safata de sortida de missatges privats de l’usuari correctament.',
	'USER_OUTBOX_EMPTY'				=> 'La safata de sortida de missatges privats de l’usuari ja estava buida.',
	'USER_OVERVIEW_UPDATED'			=> 'S’ha actualitzat la informació de l’usuari.',
	'USER_POSTS_DELETED'			=> 'S’han eliminat correctament totes les entrades realitzades per aquest usuari.',
	'USER_POSTS_MOVED'				=> 'S’han desplaçat correctament les entrades de l’usuari al fòrum seleccionat.',
	'USER_PREFS_UPDATED'			=> 'S’han actualitzat les preferències de l’usuari.',
	'USER_PROFILE'					=> 'Perfil de l’usuari',
	'USER_PROFILE_UPDATED'			=> 'S’ha actualitzat el perfil de l’usuari.',
	'USER_RANK'						=> 'Rang de l’usuari',
	'USER_RANK_UPDATED'				=> 'S’ha actualitzat el rang de l’usuari.',
	'USER_SIG_UPDATED'				=> 'S’ha actualitzat correctament la signatura de l’usuari.',
	'USER_WARNING_LOG_DELETED'		=> 'No hi ha informació disponible. Probablement l’entrada del registre ha estat eliminada.',
	'USER_TOOLS'					=> 'Eines bàsiques',
));

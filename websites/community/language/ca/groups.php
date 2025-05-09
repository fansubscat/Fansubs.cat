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
	'ALREADY_DEFAULT_GROUP'		=> 'El grup que has seleccionat ja és el teu grup per defecte.',
	'ALREADY_IN_GROUP'			=> 'Ja ets membre del grup seleccionat.',
	'ALREADY_IN_GROUP_PENDING'	=> 'Ja has sol·licitat l’afiliació a aquest grup.',

	'CANNOT_JOIN_GROUP'			=> 'No pots afiliar-te a aquest grup. Només pots afiliar-te a grups oberts lliures.',
	'CANNOT_RESIGN_GROUP'		=> 'No pots cancel·lar l’afiliació a aquest grup. Només pots cancel·lar les afiliacions a grups oberts lliures.',
	'CHANGED_DEFAULT_GROUP'	    => 'El grup per defecte s’ha canviat correctament.',

	'GROUP_AVATAR'						=> 'Avatar del grup',
	'GROUP_CHANGE_DEFAULT'				=> 'Segur que vols canviar la teva afiliació per defecte al grup «%s»?',
	'GROUP_CLOSED'						=> 'Tancat',
	'GROUP_DESC'						=> 'Descripció del grup',
	'GROUP_HIDDEN'						=> 'Ocult',
	'GROUP_INFORMATION'					=> 'Informació del grup d’usuaris',
	'GROUP_IS_CLOSED'					=> 'Aquest grup és tancat, els membres nous només s’hi poden afiliar per invitació d’un líder del grup.',
	'GROUP_IS_FREE'						=> 'Aquest és un grup obert lliure, tots els membres nous hi són bevinguts.',
	'GROUP_IS_HIDDEN'					=> 'Aquest grup és ocult, només els membres d’aquest grup poden veure qui hi pertany.',
	'GROUP_IS_OPEN'						=> 'Aquest grup és obert, pots sol·licitar-hi l’afiliació.',
	'GROUP_IS_SPECIAL'					=> 'Aquest grup és especial, els grups especials són gestionats pels administradors del fòrum.',
	'GROUP_JOIN'						=> 'Afilia’m al grup',
	'GROUP_JOIN_CONFIRM'				=> 'Segur que vols sol·licitar l’afiliació al grup seleccionat?',
	'GROUP_JOIN_PENDING'				=> 'Sol·licitud d’afiliació al grup',
	'GROUP_JOIN_PENDING_CONFIRM'		=> 'Segur que vols sol·licitar l’afiliació al grup seleccionat?',
	'GROUP_JOINED'						=> 'T’has afiliat correctament al grup seleccionat.',
	'GROUP_JOINED_PENDING'				=> 'S’ha sol·licitat correctament l’afiliació al grup. Espera que un líder del grup aprovi la teva afiliació.',
	'GROUP_LIST'						=> 'Gestiona els usuaris',
	'GROUP_MEMBERS'						=> 'Membres del grup',
	'GROUP_NAME'						=> 'Nom del grup',
	'GROUP_OPEN'						=> 'Obert',
	'GROUP_RANK'						=> 'Rang del grup',
	'GROUP_RESIGN_MEMBERSHIP'			=> 'Cancel·la l’afiliació al grup',
	'GROUP_RESIGN_MEMBERSHIP_CONFIRM'	=> 'Segur que vols cancel·lar l’afiliació al grup seleccionat?',
	'GROUP_RESIGN_PENDING'				=> 'Cancel·la la sol·licitud d’afiliació al grup',
	'GROUP_RESIGN_PENDING_CONFIRM'		=> 'Segur que vols cancel·lar la sol·licitud d’afiliació al grup seleccionat?',
	'GROUP_RESIGNED_MEMBERSHIP'			=> 'Has abandonat correctament el grup seleccionat.',
	'GROUP_RESIGNED_PENDING'			=> 'La teva sol·licitud d’afiliació al grup seleccionat s’ha cancel·lat correctament.',
	'GROUP_TYPE'						=> 'Tipus de grup',
	'GROUP_UNDISCLOSED'					=> 'Grup ocult',
	'FORUM_UNDISCLOSED'					=> 'Està moderant un fòrum ocult',

	'LOGIN_EXPLAIN_GROUP'	=> 'Cal que iniciïs la sessió per a veure els detalls del grup.',

	'NO_LEADERS'					=> 'No ets el líder de cap grup.',
	'NOT_LEADER_OF_GROUP'			=> 'L’operació sol·licitada no es pot dur a terme perquè no ets líder del grup seleccionat.',
	'NOT_MEMBER_OF_GROUP'			=> 'L’operació sol·licitada no es pot dur a terme perquè no ets membre del grup seleccionat o encara no s’hi ha aprovat la teva afiliació.',
	'NOT_RESIGN_FROM_DEFAULT_GROUP'	=> 'No pots cancel·lar l’afiliació al teu grup per defecte.',

	'PRIMARY_GROUP'		=> 'Grup primari',

	'REMOVE_SELECTED'		=> 'Suprimeix els seleccionats',

	'USER_GROUP_CHANGE'			=> 'Del grup «%1$s» a «%2$s»',
	'USER_GROUP_DEMOTE'			=> 'Renuncia al lideratge',
	'USER_GROUP_DEMOTE_CONFIRM'	=> 'Segur que vols deixar de ser líder del grup seleccionat?',
	'USER_GROUP_DEMOTED'		=> 'Has renunciat al lideratge correctament.',
));

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
	'ABOUT_USER'			=> 'Perfil',
	'ACTIVE_IN_FORUM'		=> 'Més actiu al fòrum',
	'ACTIVE_IN_TOPIC'		=> 'Més actiu al tema',
	'ADD_FOE'				=> 'Afegeix-lo als enemics',
	'ADD_FRIEND'			=> 'Afegeix-lo als amics',
	'AFTER'					=> 'Després',

	'ALL'					=> 'Tot',

	'BEFORE'				=> 'Abans',

	'CC_SENDER'				=> 'Envia’m una còpia d’aquest correu.',
	'CONTACT_ADMIN'			=> 'Contacta amb un administrador del fòrum',

	'DEST_LANG'				=> 'Llengua',
	'DEST_LANG_EXPLAIN'		=> 'Seleciona la llengua adient per al destinatari d’aquest missatge, si està disponible.',

	'EDIT_PROFILE'			=> 'Edita el perfil',

	'EMAIL_BODY_EXPLAIN'	=> 'Aquest missatge s’enviarà com a text net, no hi incloguis HTML ni BBCode. L’adreça de resposta d’aquest missatge serà la teva adreça electrònica.',
	'EMAIL_DISABLED'		=> 'Les funcions d’enviament de correu electrònic estan desactivades.',
	'EMAIL_SENT'			=> 'S’ha enviat el correu electrònic.',
	'EMAIL_TOPIC_EXPLAIN'	=> 'Aquest missatge s’enviarà com a text net, no hi incloguis HTML ni BBCode. Tingues en compte que la informació del tema ja s’inclou al missatge. L’adreça de resposta d’aquest missatge serà la teva adreça electrònica.',
	'EMPTY_ADDRESS_EMAIL'	=> 'Cal que proporcionis una adreça electrònica vàlida per al destinatari.',
	'EMPTY_MESSAGE_EMAIL'	=> 'Cal que introdueixis un missatge per a enviar el correu.',
	'EMPTY_MESSAGE_IM'		=> 'Cal que introdueixis un missatge.',
	'EMPTY_NAME_EMAIL'		=> 'Cal que introdueixis el nom real del destinatari.',
	'EMPTY_SENDER_EMAIL'	=> 'Cal que especifiquis una adreça electrònica vàlida.',
	'EMPTY_SENDER_NAME'		=> 'Cal que especifiquis un nom.',
	'EMPTY_SUBJECT_EMAIL'	=> 'Cal que introdueixis un assumpte per al correu.',
	'EQUAL_TO'				=> 'Igual a',

	'FIND_USERNAME_EXPLAIN'	=> 'Utilitza aquest apartat per a cercar membres concrets. No cal que omplis tots els camps. Per a obtenir coincidències parcials, utilitza * com a comodí. Per a introduir dates, utilitza el format <kbd>AAAA-MM-DD</kbd>, p. ex. <samp>2004-02-29</samp>. Utilitza les caselles de selecció per a triar un o més usuaris (és possible que s’admetin diversos usuaris) i prem el botó «Selecciona els marcats» per a tornar a la pantalla anterior.',
	'FLOOD_EMAIL_LIMIT'		=> 'Ara mateix no pots enviar cap altre correu electrònic. Torna-ho a provar més tard.',

	'GROUP_LEADER'			=> 'Líder del grup',

	'HIDE_MEMBER_SEARCH'	=> 'Oculta la cerca de membres',

	'IM_ADD_CONTACT'		=> 'Afegeix un contacte',
	'IM_DOWNLOAD_APP'		=> 'Baixa l’aplicació',
	'IM_JABBER'				=> 'Tingues en compte que els usuaris poden haver demanat no rebre missatges instantanis d’usuaris desconeguts.',
	'IM_JABBER_SUBJECT'		=> 'Això és un missatge automàtic, no hi responguis! Missatge de l’usuari %1$s a les %2$s.',
	'IM_MESSAGE'			=> 'El teu missatge',
	'IM_NAME'				=> 'El teu nom',
	'IM_NO_DATA'			=> 'No hi ha informació de contacte adient per a aquest usuari.',
	'IM_NO_JABBER'			=> 'La missatgeria directa d’usuaris Jabber no està permesa en aquest fòrum. Necessitaràs un client Jabber instal·lat a la teva màquina per a posar-vos en contacte amb el destinatari que es mostra a sobre.',
	'IM_RECIPIENT'			=> 'Destinatari',
	'IM_SEND'				=> 'Envia el missatge',
	'IM_SEND_MESSAGE'		=> 'Envia el missatge',
	'IM_SENT_JABBER'		=> 'El missatge per a %1$s s’ha enviat correctament.',
	'IM_USER'				=> 'Envia un missatge instantani',

	'LAST_ACTIVE'				=> 'Darrera connexió',
	'LESS_THAN'					=> 'Menys de ',
	'LIST_USERS'				=> array(
		1	=> '%d usuari',
		2	=> '%d usuaris',
	),
	'LOGIN_EXPLAIN_TEAM'		=> 'Cal haver iniciat la sessió per a veure la llista de l’equip del fòrum.',
	'LOGIN_EXPLAIN_MEMBERLIST'	=> 'Cal haver iniciat la sessió per a veure la llista la llista de membres.',
	'LOGIN_EXPLAIN_SEARCHUSER'	=> 'Cal haver iniciat la sessió per a cercar usuaris.',
	'LOGIN_EXPLAIN_VIEWPROFILE'	=> 'Cal haver iniciat la sessió per a veure perfils.',

	'MANAGE_GROUP'			=> 'Gestiona el grup',
	'MORE_THAN'				=> 'Més de ',

	'NO_CONTACT_FORM'		=> 'El formulari de contacte amb l’administrador del fòrum està desactivat.',
	'NO_CONTACT_PAGE'		=> 'La pàgina de contacte amb l’administrador del fòrum està desactivada.',
	'NO_EMAIL'				=> 'No tens permís per a enviar correus electrònics a aquest usuari.',
	'NO_VIEW_USERS'			=> 'No tens permís per a veure la llista d’usuaris ni els perfils.',

	'ORDER'					=> 'Ordre',
	'OTHER'					=> 'Altres',

	'POST_IP'				=> 'Enviat de la IP/domini',

	'REAL_NAME'				=> 'Nom del destinatari',
	'RECIPIENT'				=> 'Destinatari',
	'REMOVE_FOE'			=> 'Elimina’l dels enemics',
	'REMOVE_FRIEND'			=> 'Elimina’l dels amics',

	'SELECT_MARKED'			=> 'Selecciona els marcats',
	'SELECT_SORT_METHOD'	=> 'Selecciona el mètode d’ordenació',
	'SENDER_EMAIL_ADDRESS'	=> 'La teva adreça electrònica',
	'SENDER_NAME'			=> 'El teu nom',
	'SEND_ICQ_MESSAGE'		=> 'Envia un missatge ICQ',
	'SEND_IM'				=> 'Missatgeria instantània',
	'SEND_JABBER_MESSAGE'	=> 'Envia un missatge Jabber',
	'SEND_MESSAGE'			=> 'Missatge',
	'SEND_YIM_MESSAGE'		=> 'Envia un missatge YIM',
	'SORT_EMAIL'			=> 'Adreça electrònica',
	'SORT_LAST_ACTIVE'		=> 'Darrera connexió',
	'SORT_POST_COUNT'		=> 'Nombre de missatges',

	'USERNAME_BEGINS_WITH'	=> 'El nom de l’usuari comença per',
	'USER_ADMIN'			=> 'Administra l’usuari',
	'USER_BAN'				=> 'Bandeja',
	'USER_FORUM'			=> 'Estadístiques de l’usuari',
	'USER_LAST_REMINDED'	=> array(
		0		=> 'No hi ha cap recordatori enviat',
		1		=> '%1$d recordatori enviat<br />» %2$s',
		2		=> '%1$d recordatoris enviats<br />» %2$s',
	),
	'USER_ONLINE'			=> 'Connectat',
	'USER_PRESENCE'			=> 'Presència als fòrums',
	'USERS_PER_PAGE'		=> 'Usuaris per pàgina',

	'VIEWING_PROFILE'		=> 'Esteu veient el perfil de: %s',
	'VIEW_FACEBOOK_PROFILE'	=> 'Mostra’n el perfil al Facebook',
	'VIEW_SKYPE_PROFILE'	=> 'Mostra’n el perfil a l’Skype',
	'VIEW_TWITTER_PROFILE'	=> 'Mostra’n el perfil a X',
	'VIEW_YOUTUBE_PROFILE'	=> 'Mostra’n el perfil a YouTube',
));

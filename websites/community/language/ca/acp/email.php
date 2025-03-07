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

// Email settings
$lang = array_merge($lang, array(
	'ACP_MASS_EMAIL_EXPLAIN'		=> 'Aquí podeu enviar un missatge per correu electrònic a tots els usuaris o tots els usuaris d’un grup específic <strong>que tinguin activada l’opció de rebre correu massiu</strong>. Per aconseguir-ho s’enviarà un correu electrònic a l’adreça proporcionapa per l’administrador, amb copia oculta a tots els destinataris. La configuració per defecte només inclou 20 destinataris per cada correu electrònic, per més destinataris s’enviaran més correus. Si esteu enviant correu a un grup molt gran de gent, sigueu pacient després de trametre el formulari i no pareu la pàgina a mitges. És normal que l’enviament massiu de correu trigui molt, rebreu una notificació quan l’script hagi acabat.',
	'ALL_USERS'						=> 'Tots els usuaris',

	'COMPOSE'				=> 'Redacció del correu',

	'EMAIL_SEND_ERROR'		=> 'S’ha produït un error durant l’enviament del correu electrònic. Comproveu el %sFitxer de registre d’errors%s per veure els misstges d’error detallats.',
	'EMAIL_SENT'			=> 'S’ha enviat el missatge.',
	'EMAIL_SENT_QUEUE'		=> 'S’ha ficat el missatge a la cua d’enviament.',

	'LOG_SESSION'			=> 'Registra la sessió de correu al fitxer de registre crític',

	'SEND_IMMEDIATELY'		=> 'Envia’l immediatament',
	'SEND_TO_GROUP'			=> 'Envia’l al grup',
	'SEND_TO_USERS'			=> 'Envia’l als usuaris',
	'SEND_TO_USERS_EXPLAIN'	=> 'Si introduïu noms aquí tindran precedència sobre el grup seleccionat a sobre. Introduïu cada nom d’usuari en una línia nova.',
	
	'MAIL_BANNED'			=> 'Envia’l també als usuaris bandejats',
	'MAIL_BANNED_EXPLAIN'	=> 'Quan envieu correu electrònic massiu a un grup, amb aquesta opció podeu triar si els usuaris bandejats també el rebran.',
	'MAIL_HIGH_PRIORITY'	=> 'Alta',
	'MAIL_LOW_PRIORITY'		=> 'Baixa',
	'MAIL_NORMAL_PRIORITY'	=> 'Normal',
	'MAIL_PRIORITY'			=> 'Prioritat del correu',
	'MASS_MESSAGE'			=> 'El vostre missatge',
	'MASS_MESSAGE_EXPLAIN'	=> 'Tingueu en compte que només podeu introduir text net. S’eliminaran totes les etiquetes HTML abans de l’enviament.',
	
	'NO_EMAIL_MESSAGE'		=> 'Cal que introduïu un missatge.',
	'NO_EMAIL_SUBJECT'		=> 'Cal que especifiqueu un assumpte per al missatge.',
));

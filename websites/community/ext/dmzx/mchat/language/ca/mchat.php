<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
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
//
// Some characters for use
// ’ » “ ” …

$lang = array_merge($lang, [
	'MCHAT_ADD'						=> 'Envia',
	'MCHAT_ARCHIVE'					=> 'Arxiu',
	'MCHAT_ARCHIVE_PAGE'			=> 'Arxiu del xat',
	'MCHAT_CUSTOM_PAGE'				=> 'Xat',
	'MCHAT_BBCODES'					=> 'Codis BB',
	'MCHAT_CUSTOM_BBCODES'			=> 'Codis BB personalitzats',
	'MCHAT_DELCONFIRM'				=> 'Segur que vols suprimir aquest missatge?',
	'MCHAT_EDIT'					=> 'Edita',
	'MCHAT_EDITINFO'				=> 'Edita el missatge a sota.',
	'MCHAT_NEW_CHAT'				=> 'Nou missatge al xat!',
	'MCHAT_SEND_PM'					=> 'Envia un missatge privat',
	'MCHAT_LIKE'					=> 'Fes m’agrada al missatge',
	'MCHAT_LIKES'					=> 'likes this message',
	'MCHAT_FLOOD'					=> 'No pots enviar missatges tan seguits.',
	'MCHAT_FOE'						=> 'Missatge enviat per <strong>%1$s</strong>, que és a la teva llista d’ignorats.',
	'MCHAT_RULES'					=> 'Normes',
	'MCHAT_WHOIS_USER'				=> 'IP whois for %1$s',
	'MCHAT_MESS_LONG'				=> 'Your message is too long. Please limit it to %1$d characters.',
	'MCHAT_NO_CUSTOM_PAGE'			=> 'The mChat page is not activated at this time.',
	'MCHAT_NO_RULES'				=> 'The mChat rules page is not activated at this time.',
	'MCHAT_NOACCESS_ARCHIVE'		=> 'You don’t have permission to view the archive.',
	'MCHAT_NOJAVASCRIPT'			=> 'Activa el JavaScript per a fer servir el xat.',
	'MCHAT_NOMESSAGE'				=> 'No hi ha cap missatge',
	'MCHAT_NOMESSAGEINPUT'			=> 'No has escrit cap missatge',
	'MCHAT_MESSAGE_DELETED'			=> 'Missatge esborrat.',
	'MCHAT_OK'						=> 'D’acord',
	'MCHAT_PAUSE'					=> 'Pausat',
	'MCHAT_PERMISSIONS'				=> 'Canvia els permisos de l’usuari',
	'MCHAT_REFRESHING'				=> 'S’està actualitzant…',
	'MCHAT_RESPOND'					=> 'Respon a aquest usuari',
	'MCHAT_SMILES'					=> 'Emoticones',
	'MCHAT_TOTALMESSAGES'			=> 'Missatges totals: <strong>%1$d</strong>',
	'MCHAT_USESOUND'				=> 'Reprodueix un so',
	'MCHAT_SOUND_ON'				=> 'El so està activat',
	'MCHAT_SOUND_OFF'				=> 'El so està desactivat',
	'MCHAT_ENTER'					=> 'Fes servir Control o Cmd + Retorn per a l’altra acció',
	'MCHAT_ENTER_SUBMIT'			=> 'Retorn envia el missatge',
	'MCHAT_ENTER_LINEBREAK'			=> 'Retorn afegeix una línia nova',
	'MCHAT_COLLAPSE_TITLE'			=> 'Commuta la visibilitat del xat',
	'MCHAT_WHO_IS_REFRESH_EXPLAIN'	=> 'S’actualitza cada <strong>%1$d</strong> segons',
	'MCHAT_MINUTES_AGO'				=> [
		0 => 'ara mateix',
		1 => 'fa %1$d minut',
		2 => 'fa %1$d minuts',
	],

	// These messages are formatted with JavaScript, hence {} and no %d
	'MCHAT_CHARACTER_COUNT'			=> '<strong>{current}</strong> caràcters',
	'MCHAT_CHARACTER_COUNT_LIMIT'	=> '<strong>{current}</strong> de {max} caràcters',
	'MCHAT_MENTION'					=> ' @{username} ',
]);

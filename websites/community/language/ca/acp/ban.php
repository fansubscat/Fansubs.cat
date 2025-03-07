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

// Banning
$lang = array_merge($lang, array(
	'1_HOUR'		=> '1 hora',
	'30_MINS'		=> '30 minuts',
	'6_HOURS'		=> '6 hores',

	'ACP_BAN_EXPLAIN'	=> 'Aquí podeu controlar el bandeig d’usuari per nom, adreça IP o adreça electrònica. Aquests mètodes eviten que un usuari accedeixi a qualsevol part del fòrum. Si voleu, podeu donar una explicació curta (3000 caràcters com a màxim) per al bandeig que es mostrarà al registre dels administradors. També podeu especificar la durada d’un bandeig. Si voleu que el bandeig acabi en una data determinada en lloc d’un període fixe de temps, seleccioneu l’opció <span style="text-decoration: underline;">Fins el -&gt;</span> per a la durada del bandeig i introduïu una data en el format <kbd>AAAA-MM-DD</kbd>.',

	'BAN_EXCLUDE'			=> 'Exclou del bandeig',
	'BAN_LENGTH'			=> 'Durada del bandeig',
	'BAN_REASON'			=> 'Raó del bandeig',
	'BAN_GIVE_REASON'		=> 'Raó que es mostra a l’usuari bandejat',
	'BAN_UPDATE_SUCCESSFUL'	=> 'S’ha actualitzat la llista de bandeig correctament.',
	'BANNED_UNTIL_DATE'		=> 'fins el %s', // Exemple: "fins el dilluns 13/Jul/2009, 14:44"
	'BANNED_UNTIL_DURATION'	=> '%1$s (fins el %2$s)', // Exemple: "7 dies (fins el dimarts 14/Jul/2009, 14:44)"

	'EMAIL_BAN'					=> 'Bandeja una o més adreces electròniques',
	'EMAIL_BAN_EXCLUDE_EXPLAIN'	=> 'Habiliteu aquesta opció per excloure l’adreca electrònica introduïda de tots els bandejos actuals.',
	'EMAIL_BAN_EXPLAIN'			=> 'Per especificar més d’una adreça electrònica, introduïu cadascuna d’elles en una línia nova. Utilitzeu * com a comodí per obtenir coincidències parcials, p.ex. <samp>*@hotmail.com</samp>, <samp>*@*.domain.tld</samp>, etc.',
	'EMAIL_NO_BANNED'			=> 'No hi ha adreces electròniques bandejades',
	'EMAIL_UNBAN'				=> 'Desbandeja o desexclou les adreces electròniques',
	'EMAIL_UNBAN_EXPLAIN'		=> 'Podeu desbandejar (o desexcloure) múltiples adreces electròniques d’un sol cop si utilitzeu la combinació de ratolí i teclat adequada per al vostre ordinador i navegador. Les adreces electròniques excloses estan ressaltades.',

	'IP_BAN'					=> 'Bandeja una o més adreces IP',
	'IP_BAN_EXCLUDE_EXPLAIN'	=> 'Habiliteu aquesta opció per excloure l’adreca IP introduïda de tots els bandejos actuals.',
	'IP_BAN_EXPLAIN'			=> 'Per especificar diverses adreces IP o noms d’amfitrió diferents, introduïu cadascun d’ells en una línia nova. Per especificar un rang d’adreces IP, separeu l’inici i el final amb un guió (-), per especificar un comodí utilitzeu un asterisc “*”.',
	'IP_HOSTNAME'				=> 'Adreces IP o noms d’amfitrió',
	'IP_NO_BANNED'				=> 'No hi ha adreces IP bandejades',
	'IP_UNBAN'					=> 'Desbandeja o desexclou les adreces IP',
	'IP_UNBAN_EXPLAIN'			=> 'Podeu desbandejar (o desexcloure) múltiples adreces IP d’un sol cop si utilitzeu la combinació de ratolí i teclat adequada per al vostre ordinador i navegador. Les adreces IP excloses estan ressaltades.',

	'LENGTH_BAN_INVALID'		=> 'La data ha de tenir el format <kbd>AAAA-MM-DD</kbd>.',

	'OPTIONS_BANNED'			=> 'Bandejat',
	'OPTIONS_EXCLUDED'			=> 'Exclòs',

	'PERMANENT'		=> 'Permanent',
	
	'UNTIL'						=> 'Fins el',
	'USER_BAN'					=> 'Bandeja un o més usuaris per nom d’usuari',
	'USER_BAN_EXCLUDE_EXPLAIN'	=> 'Habiliteu aquesta opció per excloure els usuaris introduïts de tots els bandejos actuals.',
	'USER_BAN_EXPLAIN'			=> 'Podeu bandejar múltiples usuaris d’un sol cop introduïnt cada nom en una línia nova. Utilitzeu la funció <span style="text-decoration: underline;">Cerca un membre</span> per trobar i afegir un o més usuaris automàticament.',
	'USER_NO_BANNED'			=> 'No hi ha noms d’usuari bandejats',
	'USER_UNBAN'				=> 'Desbandeja o desexclou usuaris per nom d’usuari',
	'USER_UNBAN_EXPLAIN'		=> 'Podeu desbandejar (o desexcloure) múltiples usuaris d’un sol cop si utilitzeu la combinació de ratolí i teclat adequada per al vostre ordinador i navegador. Els usuaris exclosos estan ressaltats.',
));

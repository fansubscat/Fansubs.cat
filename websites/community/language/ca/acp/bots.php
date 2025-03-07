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

// Bot settings
$lang = array_merge($lang, array(
	'BOTS'				=> 'Gestió de robots',
	'BOTS_EXPLAIN'		=> 'Els “robots”, “aranyes” o “crawlers” són agents automatitzats utilitzats habitualment pels cercadors per actualitzar les seves bases de dades. Com que no solen fer un ús adequat de les sessions, poden distorsionar els comptadors de visitants, incrementar la càrrega i, de vegades, no conseguir indexar el lloc web correctament. Aquí podeu definir un tipus especial d’usuari per solucionar aquests problemes.',
	'BOT_ACTIVATE'		=> 'Activa',
	'BOT_ACTIVE'		=> 'Robot actiu',
	'BOT_ADD'			=> 'Afegeix un robot',
	'BOT_ADDED'			=> 'S’ha afegit el robot correctament.',
	'BOT_AGENT'			=> 'Coincindència amb agent',
	'BOT_AGENT_EXPLAIN'	=> 'Cadena per trobar coincidencies amb l’agent del navegador del bot, es permeten les coincidències parcials.',
	'BOT_DEACTIVATE'	=> 'Desactiva',
	'BOT_DELETED'		=> 'S’ha eliminat el robot correctament.',
	'BOT_EDIT'			=> 'Edició del robot',
	'BOT_EDIT_EXPLAIN'	=> 'Aquí podeu afegir o editar el registre d’un robot. Podeu definir una cadena d’agent i/o una o més adreces IP (o rangs d’adreces) per trobar coincidències. Aneu amb compte quan definiu cadenes d’agent o adreces per coinciències. També podeu definir l’estil i l’idioma que es mostrarà al robot en usar el fòrum. Això us permet reduir l’ample de banda utilitzat selecionant un estil simple per als robots. Recordeu-vos de definir els permisos adequats per al grup especial d’usuaris anomenat Robots.',
	'BOT_LANG'			=> 'Idioma del robot',
	'BOT_LANG_EXPLAIN'	=> 'L’idioma que es presenta al robot quan navega pel fòrum.',
	'BOT_LAST_VISIT'	=> 'Darrera visita',
	'BOT_IP'			=> 'Adreça IP del robot',
	'BOT_IP_EXPLAIN'	=> 'Es permeten les coincidències parcials, separeu les adreces amb una coma.',
	'BOT_NAME'			=> 'Nom del robot',
	'BOT_NAME_EXPLAIN'	=> 'Només s’utilitza per a la vostra informació.',
	'BOT_NAME_TAKEN'	=> 'El nom del robot ja s’està utilitzant en aquest fòrum i no el podeu usar.',
	'BOT_NEVER'			=> 'Mai',
	'BOT_STYLE'			=> 'Estil per al robot',
	'BOT_STYLE_EXPLAIN'	=> 'L’estil del fòrum que usarà el robot.',
	'BOT_UPDATED'		=> 'S’ha actualitzat el robot correctament.',

	'ERR_BOT_AGENT_MATCHES_UA'	=> 'L’agent del robot que heu proporcionat és similar a l’agent que esteu utilitzant actualment per navegar pel fòrum. Ajusteu l’agent d’aquest robot.',
	'ERR_BOT_NO_IP'				=> 'Les adreces IP que heu proporcionat no són vàlides o bé no s’ha pogut resoldre el nom de l’amfitrió.',
	'ERR_BOT_NO_MATCHES'		=> 'Cal que proporcioneu, com a mínim, l’agent o l’adreça IP per a les coincidències d’aquest robot.',

	'NO_BOT'		=> 'No s’ha trobat cap robot amb l’ID especificat.',
	'NO_BOT_GROUP'	=> 'No s’ha trobat el grup especial de Robots.',
));

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

// User pruning
$lang = array_merge($lang, array(
	'ACP_PRUNE_USERS_EXPLAIN'	=> 'Aquesta secció us permet eliminar o desactivar usuaris del vostre fòrum. Els comptes es poden filtrar de diverses maneres: per nombre d’entrades, activitat més recent, etc. Es poden combinar diversos criteris per ajustar quins comptes es veuran afectats. Per exemple, podeu podar els usuaris amb menys de 10 entrades i que també estiguin inactius des del 2002-01-01. Utilitzeu * com comodí per als camps de text. De forma alternativa, podeu ignorar completament la selecció per criteris introduïnt una llista d’usuaris (cadascun en una línia nova) a la casella de text. Aneu amb compte amb aquesta utilitat! L’eliminació d’un usuari no es pot desfer.',

	'CRITERIA'				=> 'Criteris',

	'DEACTIVATE_DELETE'			=> 'Desactiva o elimina',
	'DEACTIVATE_DELETE_EXPLAIN'	=> 'Trieu si voleu desactivar els usuaris o eliminar-los completament. Tingueu en compte que els usuaris eliminats no es poden recuperar!',
	'DELETE_USERS'				=> 'Elimina',
	'DELETE_USER_POSTS'			=> 'Elimina les entrades dels usuaris podats',
	'DELETE_USER_POSTS_EXPLAIN' => 'Suprimeix les entrades realitzades pels usuaris eliminats, no té cap efecte si només desactiveu els usuaris.',

	'JOINED_EXPLAIN'			=> 'Introduïu una data en el format <kbd>AAAA-MM-DD</kbd>. Podeu usar tots dos camps per especificar un interval o deixar-ne un en blanc per a un rang de dates obert.',

	'LAST_ACTIVE_EXPLAIN'		=> 'Introduïu una data en el format <kbd>AAAA-MM-DD</kbd>. Introduïu <kbd>0000-00-00</kbd> per podar els usuaris que no han iniciat mai la sessió, les condicions <em>Abans</em> i <em>Després</em> s’ignoraran.',

	'POSTS_ON_QUEUE'			=> 'Entrades pendents d’aprovació',
	'PRUNE_USERS_GROUP_EXPLAIN'	=> 'Limita-ho a usuaris que pertanyin al grup seleccionat.',
	'PRUNE_USERS_GROUP_NONE'	=> 'Tots els grups',
	'PRUNE_USERS_LIST'				=> 'Usuaris que es podaran',
	'PRUNE_USERS_LIST_DELETE'		=> 'Amb els criteris seleccionats per podar usuaris s’eliminaran els comptes següents. Podeu treure usuaris individuals de la llista d’eliminació desmarcant la casella de selecció al costat del seu nom d’usuari.',
	'PRUNE_USERS_LIST_DEACTIVATE'	=> 'Amb els criteris seleccionats per podar usuaris es desactivaran els comptes següents. Podeu treure usuaris individuals de la llista de desactivació desmarcant la casella de selecció al costat del seu nom d’usuari.',

	'SELECT_USERS_EXPLAIN'		=> 'Introduïu noms d’usuari concrets a la casella de text. S’usarà aquesta llista en lloc dels criteris especificats més amunt. No es pot podar als usuaris fundadors.',

	'USER_DEACTIVATE_SUCCESS'	=> 'Els usuaris seleccionats s’han desactivat correctament.',
	'USER_DELETE_SUCCESS'		=> 'Els usuaris seleccionats s’han eliminat correctament.',
	'USER_PRUNE_FAILURE'		=> 'No hi ha cap usuari que satisfaci els criteris seleccionats.',

	'WRONG_ACTIVE_JOINED_DATE'	=> 'La data que heu introduït no es correcta, és necessari que estigui en el format <kbd>AAAA-MM-DD</kbd>.',
));

// Forum Pruning
$lang = array_merge($lang, array(
	'ACP_PRUNE_FORUMS_EXPLAIN'	=> 'Aquí podeu eliminar els temes en els quals no hi hagi hagut cap resposta o visita en el nombre de dies que trieu. Si no introduïu cap número, s’eliminaran tots els temes. Per defecte no s’eliminaran ni temes que tinguin enquestes que no hagin acabat ni temes permanents ni avisos.',

	'FORUM_PRUNE'		=> 'Poda de fòrums',

	'NO_PRUNE'			=> 'No s’ha podat cap fòrum.',

	'SELECTED_FORUM'	=> 'Fòrum seleccionat',
	'SELECTED_FORUMS'	=> 'Fòrums seleccionats',

	'POSTS_PRUNED'					=> 'Entrades podades',
	'PRUNE_ANNOUNCEMENTS'			=> 'Poda els avisos',
	'PRUNE_FINISHED_POLLS'			=> 'Poda les enquestes que s’hagin acabat',
	'PRUNE_FINISHED_POLLS_EXPLAIN'	=> 'Elimina els temes amb enquestes que ja han acabat.',
	'PRUNE_FORUM_CONFIRM'			=> 'Esteu segur que voleu podar els fòrums seleccionats amb els paràmetres especificats? Un cop eliminats, no hi ha cap manera per recuperar les entrades i els temes podats.',
	'PRUNE_NOT_POSTED'				=> 'Dies des de la darrera entrada',
	'PRUNE_NOT_VIEWED'				=> 'Dies des de la darrera lectura',
	'PRUNE_OLD_POLLS'				=> 'Poda les enquestes antigues',
	'PRUNE_OLD_POLLS_EXPLAIN'		=> 'Elimina temes amb enquestes en les que no s’hagi emès cap vot en el nombre de dies especificat per a la darrera entrada.',
	'PRUNE_STICKY'					=> 'Poda els temes recurrents',
	'PRUNE_SUCCESS'					=> 'La poda dels fòrums s’ha dut a terme correctament.',

	'TOPICS_PRUNED'		=> 'Temes podats',
));

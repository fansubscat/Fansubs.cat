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
	'ALL_AVAILABLE'			=> 'Tots els disponibles',
	'ALL_RESULTS'			=> 'Tots els resultats',

	'DISPLAY_RESULTS'		=> 'Mostra els resultats com',

	'FOUND_SEARCH_MATCHES'		=> array(
		1	=> 'La cerca ha trobat %d coincidència',
		2	=> 'La cerca ha trobat %d coincidències',
	),
	'FOUND_MORE_SEARCH_MATCHES'		=> array(
		1	=> 'La cerca ha trobat més d’%d coincidència',
		2	=> 'La cerca ha trobat més de %d coincidències',
	),

	'GLOBAL'				=> 'Avís global',

	'IGNORED_TERMS'			=> 'ignorats',
	'IGNORED_TERMS_EXPLAIN'	=> 'Les paraules següents s’han ignorat durant la cerca perquè són massa freqüents: <strong>%s</strong>.',

	'JUMP_TO_POST'			=> 'Salta a l’entrada',

	'LOGIN_EXPLAIN_EGOSEARCH'	=> 'Cal que esteu registrat i amb la sessió iniciada per visualitzar les vostres entrades.',
	'LOGIN_EXPLAIN_UNREADSEARCH'=> 'Cal que esteu registrat i amb la sessió iniciada per visualitzar les vostres entrades no llegides.',
	'LOGIN_EXPLAIN_NEWPOSTS'	=> 'Cal que esteu registrat i amb la sessió iniciada per visualitzar les entrades noves des de la vostra darrera visita.',

	'MAX_NUM_SEARCH_KEYWORDS_REFINE'	=> array(
		1	=> 'Heu especificat massa paraules per a la cerca. No introduïu més d’%1$d paraula.',
		2	=> 'Heu especificat massa paraules per a la cerca. No introduïu més de %1$d paraules.',
	),

	'NO_KEYWORDS'			=> 'Heu d’especificar com a mínim una paraula per a la cerca. Cada paraula ha de tenir un mínim de %s i no pot contenir més de %s sense tenir en compte els comodins.',
	'NO_RECENT_SEARCHES'	=> 'No s’ha fet cap cerca recentment.',
	'NO_SEARCH'				=> 'No se us permet usar el sistema de cerques.',
	'NO_SEARCH_RESULTS'		=> 'No s’ha trobat cap coincidència.',
	'NO_SEARCH_LOAD'		=> 'No se us permet usar la cerca ara mateix. El servidor esta sotmès a una càrrega elevada. Intenteu-ho de nou més tard.',
	'NO_SEARCH_TIME'		=> array(
		1	=> 'Ara mateix no es pot usar la cerca. Si us plau torneu a provar-ho d’aquí a %d segon.',
		2	=> 'Ara mateix no es pot usar la cerca. Si us plau torneu a provar-ho d’aquí a %d segons.',
	),
	'NO_SEARCH_UNREADS'		=> 'La cerca d’entrades no llegides està inhabilitada en aquest fòrum.',
	'WORD_IN_NO_POST'		=> 'No s’ha trobat cap entrada perquè la paraula <strong>%s</strong> no apareix en cap entrada.',
	'WORDS_IN_NO_POST'		=> 'No s’ha trobat cap entrada perquè les paraules <strong>%s</strong> no apareixen en cap entrada.',

	'POST_CHARACTERS'		=> 'caràcters de les entrades',
	'PHRASE_SEARCH_DISABLED'	=> 'Aquest fòrum no permet la cerca per frase exacta.',

	'RECENT_SEARCHES'		=> 'Cerques recents',
	'RESULT_DAYS'			=> 'Limita els resultats als darrers',
	'RESULT_SORT'			=> 'Ordena els resultats per',
	'RETURN_FIRST'			=> 'Retorna els primers',
	'RETURN_FIRST_EXPLAIN'	=> 'Poseu 0 per mostrar l’entrada sencera.',
	'GO_TO_SEARCH_ADV'		=> 'Ves a la cerca avançada',

	'SEARCHED_FOR'				=> 'Paraules utilitzades en la cerca',
	'SEARCHED_TOPIC'			=> 'Tema on s’ha fet la cerca',
	'SEARCHED_QUERY'			=> 'Consulta que s’ha cercat',
	'SEARCH_ALL_TERMS'			=> 'Cerca totes les paraules o utilitza la consulta literalment',
	'SEARCH_ANY_TERMS'			=> 'Cerca qualsevol paraula',
	'SEARCH_AUTHOR'				=> 'Cerca per autor',
	'SEARCH_AUTHOR_EXPLAIN'		=> 'Utilitza * com a comodí per a coinicidències parcials.',
	'SEARCH_FIRST_POST'			=> 'Només la primera entrada dels temes',
	'SEARCH_FORUMS'				=> 'Cerca als fòrums',
	'SEARCH_FORUMS_EXPLAIN'		=> 'Seleccioneu el fòrum o fòrums en els quals voleu realitzar la cerca. Es cerca automàticament en els subfòrums si no desactiveu l’opció “Cerca als subfòrums” a sota.',
	'SEARCH_IN_RESULTS'			=> 'Cerca als resultats',
	'SEARCH_KEYWORDS_EXPLAIN'	=> 'Escriviu <strong>+</strong> al davant de les paraules que s’han de trobar i <strong>-</strong> al davant de les que no s’han de trobar. Poseu una llista de paraules entre claudàtors separades per <strong>|</strong> si voleu trobar-ne només una. Utilitzeu * com a comodí per a coincidències parcials.',
	'SEARCH_MSG_ONLY'			=> 'Només el text del missatge',
	'SEARCH_OPTIONS'			=> 'Opcions de cerca',
	'SEARCH_QUERY'				=> 'Consulta de cerca',
	'SEARCH_SUBFORUMS'			=> 'Cerca als subfòrums',
	'SEARCH_TITLE_MSG'			=> 'Assumptes i text dels missatges',
	'SEARCH_TITLE_ONLY'			=> 'Només títols de tema',
	'SEARCH_WITHIN'				=> 'Cerca a',
	'SORT_ASCENDING'			=> 'Ascendent',
	'SORT_AUTHOR'				=> 'Autor',
	'SORT_DESCENDING'			=> 'Descendent',
	'SORT_FORUM'				=> 'Fòrum',
	'SORT_POST_SUBJECT'			=> 'Assumpte de l’entrada',
	'SORT_TIME'					=> 'Data de l’entrada',
	'SPHINX_SEARCH_FAILED'		=> 'La cerca ha fallat: %s',
	'SPHINX_SEARCH_FAILED_LOG'	=> 'No s’ha pogut dur a terme la cerca. S’ha registrat més informació sobre aquesta fallada al fitxer de registre d’errors.',

	'TOO_FEW_AUTHOR_CHARS'	=> array(
		1	=> 'Heu d’especificar, com a mínim, %d caràcter del nom de l’autor.',
		2	=> 'Heu d’especificar, com a mínim, %d caràcters del nom de l’autor.',
	),
));

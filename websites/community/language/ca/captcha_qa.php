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
	'CAPTCHA_QA'				=> 'Preguntes i respostes',
	'CONFIRM_QUESTION_EXPLAIN'	=> 'Aquesta pregunta és un mitjà per evitar la tramesa automatitzada de formularis per part dels robots de brossa.',
	'CONFIRM_QUESTION_WRONG'	=> 'Heu proporcionat una resposta no vàlida a la pregunta.',
	'CONFIRM_QUESTION_MISSING'	=> 'No s’han pogut recuperar les preguntes pel CAPTCHA. Contacteu amb un administrador del fòrum.',

	'QUESTION_ANSWERS'			=> 'Respostes',
	'ANSWERS_EXPLAIN'			=> 'Si us plau, introduïu respostes vàlides a la pregunta, una per línia.',
	'CONFIRM_QUESTION'			=> 'Pregunta',

	'ANSWER'					=> 'Resposta',
	'EDIT_QUESTION'				=> 'Edita la pregunta',
	'QUESTIONS'					=> 'Preguntes',
	'QUESTIONS_EXPLAIN'			=> 'Cada cop que es trameti un formulari on hagueu habilitat el connector de Preguntes i respostes, es farà als usuaris una de les preguntes especificades aquí. Per usar aquest connector cal que definiu com a mínim una pregunta en l’idioma per defecte. Aquestes preguntes haurien de ser fàcils per a l’audiència del vostre fòrum, però més enllà de la capacitat d’un robot capaç de realitzar una cerca a Google™. Només cal una única pregunta. Si comenceu a rebre registres automatitzats d’usuaris, haurieu de canviar la pregunta. Habiliteu la comprovació estricta si les vostres preguntes depenen de les majúscules, les minúscules, els signes de puntuació o els espais en blanc.',
	'QUESTION_DELETED'			=> 'Pregunta eliminada',
	'QUESTION_LANG'				=> 'Idioma',
	'QUESTION_LANG_EXPLAIN'		=> 'L’idioma en què estan escrites la pregunta i les respostes.',
	'QUESTION_STRICT'			=> 'Comprovació estricta',
	'QUESTION_STRICT_EXPLAIN'	=> 'Habiliteu aquesta opció per que es comprovin les majúscules, les minúscules els signes de puntuació i els espais en blanc.',

	'QUESTION_TEXT'				=> 'Pregunta',
	'QUESTION_TEXT_EXPLAIN'		=> 'La pregunta que es mostra a l’usuari.',

	'QA_ERROR_MSG'				=> 'Si us plau ompliu tots els camps i introduïu una resposta com a mínim.',
	'QA_LAST_QUESTION'			=> 'No podeu eliminar totes les preguntes mentre el connector està activat.',
));

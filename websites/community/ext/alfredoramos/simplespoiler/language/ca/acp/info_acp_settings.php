<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

/**
 * @ignore
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
 * @ignore
 */
if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'SPOILER_DEPTH_LIMIT' => 'Profunditat màxima d’imbricació dels espòilers',
	'SPOILER_DEPTH_LIMIT_EXPLAIN' => 'Profunditat màxima d’imbricació dels espòilers en una publicació. Definiu-ho a <samp>0</samp> per a una profunditat il·limitada.'
]);

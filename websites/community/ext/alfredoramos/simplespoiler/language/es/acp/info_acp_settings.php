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
	'SPOILER_DEPTH_LIMIT' => 'Profundidad máxima de anidamiento para spoilers',
	'SPOILER_DEPTH_LIMIT_EXPLAIN' => 'Profundidad máxima de anidamiento para spoilers por mensaje. Ajuste este valor en <samp>0</samp> para una profundidad ilimitada.'
]);

<?php
/**
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

$lang = array_merge($lang, [
	'PHPBB_VERSION_ERROR'	=> 'Seu fórum parece estar usando uma versão antiga do phpBB. É necessário o phpBB ' . \phpbb\mediaembed\ext::PHPBB_MINIMUM . ' ou mais recente para usar esta extensão.',
	'S9E_MEDIAEMBED_ERROR'	=> 'Detectamos a extensão s9e/mediaembed. O plugin Media Embed do phpBB não pode ser instalado até que você desabilite e remova os dados e todos os arquivos relacionados à extensão s9e/mediaembed.',
]);

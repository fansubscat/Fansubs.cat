<?php
/**
 *
 * VigLink extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
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
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_VIGLINK_SETTINGS'			=> 'Configuració del VigLink',
	'ACP_VIGLINK_SETTINGS_EXPLAIN'	=> 'VigLink és un servei proporcionat per tercers que monetitza de forma discreta els enllaços que publiquin els usuaris del vostre fòrum sense que en canviï l’experiència. Quan els usuaris cliquen a enllaços dirigits a productes o serveis externs i compren alguna cosa, els venedors paguen a Viglink una comissió, de la qual una part és donada al projecte del phpBB. En escollir que s’habiliti VigLink i donant-ne els guanys al projecte del phpBB, ajudeu la nostra organització de codi lliure i assegureu que en continuï la seguretat financera.',
	'ACP_VIGLINK_SETTINGS_CHANGE'	=> 'Podeu canviar aquesta configuració en qualsevol moment al tauler de “<a href="%1$s">configuració del VigLink</a>”.',
	'ACP_VIGLINK_SUPPORT_EXPLAIN'	=> 'No se us redirigirà més a aquesta pàgina un cop hagueu indicat les vostres opcions preferides a sota fent clic al botó Tramet.',
	'ACP_VIGLINK_ENABLE'			=> 'Habilita VigLink',
	'ACP_VIGLINK_ENABLE_EXPLAIN'	=> 'Habilita l’ús dels serveis de VigLink.',
	'ACP_VIGLINK_EARNINGS'			=> 'Obteniu els vostres propis guanys (opcional)',
	'ACP_VIGLINK_EARNINGS_EXPLAIN'  => 'Podeu obtenir els vostres propis guanys registrant-vos amb un compte VigLink Convert.',
	'ACP_VIGLINK_DISABLED_PHPBB'	=> 'El phpBB ha deshabilitat els serveis de VigLink.',
	'ACP_VIGLINK_CLAIM'				=> 'Obteniu els vostres guanys',
	'ACP_VIGLINK_CLAIM_EXPLAIN'		=> 'Podeu obtenir els guanys del vostre fòrum amb els enllaços VigLink monetitzats en lloc de donar-los al projecte del phpBB. Per gestionar la configuració del vostre compte, registreu-vos amb un compte de tipus “VigLink Convert” fent clic a “Converteix compte”',
	'ACP_VIGLINK_CONVERT_ACCOUNT'	=> 'Converteix compte',
	'ACP_VIGLINK_NO_CONVERT_LINK'	=> 'No s’ha pogut obtenir l’enllaç de conversió de compte VigLink.',
));

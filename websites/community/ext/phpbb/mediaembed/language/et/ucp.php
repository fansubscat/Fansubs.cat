<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
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

$lang = array_merge($lang, [
	'MEDIA_EMBED_PRIVACY_POLICY' => '
		<br><br>
		<h3>Teistelt veebisaitidelt manustatud sisu</h3>
		„%1$s” võib sisaldada postitusi või sisu, millesse on manustatud materjali välistelt veebisaitidelt, sealhulgas YouTube’ist, Facebookist, Twitterist ja sarnastelt platvormidelt. Nende väliste saitide manustatud sisu toimib samamoodi nagu algse veebisaidi otsene külastamine.
		<br><br>Välised veebisaidid võivad koguda sinu kohta andmeid, kasutada küpsiseid, lisada kolmandate osapoolte jälgimist ja jälgida sinu suhtlust manustatud sisuga, sealhulgas juhul, kui sul on sellel veebisaidil konto ja oled sisse logitud.
		<br><br>Selline tegevus ei ole „%1$s” kontrolli all ning sellele kehtivad vastavate väliste veebisaitide privaatsuspõhimõtted ja kasutustingimused. Soovitame tutvuda kõigi manustatud sisu kaudu kasutatavate kolmandate osapoolte teenuste privaatsus- ja küpsisepõhimõtetega.
	',
]);

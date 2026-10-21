<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 * Language : French [fr]
 * Translators :
 * 1. Fred rimbert (https://forums.caforum.fr) (2.0.3) (01.2026)
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
		<h3>Contenu intégré d‘autres sites Web</h3>
		“%1$s" peut inclure des publications ou du contenu qui contient du matériel intégré provenant de sites Web externes, y compris, mais sans s’y limiter, YouTube, Facebook, Twitter et des plateformes similaires. Le contenu intégré de ces sites externes se comporte de la même manière que si vous aviez visité directement le site d’origine.
		<br><br>Ces sites Web externes peuvent collecter des données vous concernant, utiliser des cookies, intégrer un suivi supplémentaire de tiers et surveiller votre interaction avec le contenu intégré, y compris suivre votre interaction si vous avez un compte et êtes connecté à ce site Web.
		<br><br>Veuillez noter que cette activité est indépendante de la volonté de "%1$s" et qu’elle est régie par les politiques de confidentialité et les conditions d’utilisation des sites Web externes respectifs. Nous vous encourageons à consulter les politiques de confidentialité et de cookies de tout service tiers avec lequel vous interagissez via du contenu intégré.
	',
]);

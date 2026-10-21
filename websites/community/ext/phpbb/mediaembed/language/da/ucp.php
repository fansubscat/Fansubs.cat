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
		<h3>Indlejret indhold fra andre websteder</h3>
		“%1$s” kan indeholde indlæg eller indhold med indlejret materiale fra eksterne websteder, herunder blandt andet YouTube, Facebook, Twitter og lignende platforme. Indlejret indhold fra disse eksterne websteder fungerer på samme måde, som hvis du havde besøgt det oprindelige websted direkte.
		<br><br>Disse eksterne websteder kan indsamle oplysninger om dig, bruge cookies, indlejre yderligere tredjepartssporing og overvåge din interaktion med det indlejrede indhold, herunder spore din interaktion, hvis du har en konto og er logget ind på webstedet.
		<br><br>Bemærk, at denne aktivitet er uden for “%1$s”s kontrol og reguleres af de pågældende eksterne websteders privatlivspolitikker og servicevilkår. Vi anbefaler, at du gennemgår privatlivs- og cookiepolitikkerne for alle tredjepartstjenester, du bruger via indlejret indhold.
	',
]);

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
		<h3>Vložený obsah z iných webových stránok</h3>
		„%1$s“ môže obsahovať príspevky alebo obsah s vloženým materiálom z externých webových stránok vrátane YouTube, Facebooku, Twitteru a podobných platforiem. Vložený obsah z týchto externých stránok sa správa rovnako, ako keby ste navštívili pôvodnú stránku priamo.
		<br><br>Tieto externé stránky môžu o vás zhromažďovať údaje, používať súbory cookie, vkladať ďalšie sledovanie tretích strán a monitorovať vašu interakciu s vloženým obsahom vrátane prípadov, keď máte na danej stránke účet a ste prihlásení.
		<br><br>Upozorňujeme, že táto činnosť je mimo kontroly stránky „%1$s“ a riadi sa zásadami ochrany osobných údajov a podmienkami používania príslušných externých stránok. Odporúčame preskúmať zásady ochrany osobných údajov a používania súborov cookie všetkých služieb tretích strán, s ktorými komunikujete prostredníctvom vloženého obsahu.
	',
]);

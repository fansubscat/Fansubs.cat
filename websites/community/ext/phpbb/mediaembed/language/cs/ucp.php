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
		<h3>Vložený obsah z jiných webů</h3>
		„%1$s“ může obsahovat příspěvky nebo jiný obsah s vloženým materiálem z externích webů, mimo jiné z YouTube, Facebooku, Twitteru a podobných platforem. Vložený obsah z těchto externích webů se chová stejně, jako kdybyste navštívili původní web přímo.
		<br><br>Tyto externí weby o vás mohou shromažďovat údaje, používat soubory cookie, vkládat další sledování třetích stran a sledovat vaši interakci s vloženým obsahem, včetně sledování interakce, pokud máte na daném webu účet a jste přihlášeni.
		<br><br>Upozorňujeme, že tato činnost je mimo kontrolu webu „%1$s“ a řídí se zásadami ochrany osobních údajů a podmínkami používání příslušných externích webů. Doporučujeme prostudovat zásady ochrany osobních údajů a používání souborů cookie všech služeb třetích stran, se kterými prostřednictvím vloženého obsahu pracujete.
	',
]);

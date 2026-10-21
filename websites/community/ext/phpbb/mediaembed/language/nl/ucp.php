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
		<h3>Ingesloten inhoud van andere websites</h3>
		“%1$s” kan berichten of inhoud bevatten met ingesloten materiaal van externe websites, waaronder YouTube, Facebook, Twitter en vergelijkbare platforms. Ingesloten inhoud van deze externe websites werkt hetzelfde alsof je de oorspronkelijke website rechtstreeks had bezocht.
		<br><br>Deze externe websites kunnen gegevens over je verzamelen, cookies gebruiken, aanvullende tracking door derden insluiten en je interactie met ingesloten inhoud volgen, ook wanneer je een account hebt en bij die website bent aangemeld.
		<br><br>Deze activiteiten vallen buiten de controle van “%1$s” en worden beheerst door het privacybeleid en de servicevoorwaarden van de betreffende externe websites. We raden je aan het privacy- en cookiebeleid te bekijken van elke externe dienst waarmee je via ingesloten inhoud communiceert.
	',
]);

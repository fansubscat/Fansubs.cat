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
		<h3>Conținut încorporat de pe alte site-uri web</h3>
		„%1$s” poate include mesaje sau conținut cu materiale încorporate de pe site-uri web externe, inclusiv YouTube, Facebook, Twitter și platforme similare. Conținutul încorporat de pe aceste site-uri externe se comportă la fel ca atunci când vizitați direct site-ul web de origine.
		<br><br>Aceste site-uri externe pot colecta date despre dumneavoastră, pot utiliza module cookie, pot include monitorizare suplimentară de la terți și vă pot urmări interacțiunea cu conținutul încorporat, inclusiv dacă aveți un cont și sunteți autentificat pe site-ul respectiv.
		<br><br>Rețineți că această activitate nu se află sub controlul „%1$s” și este guvernată de politicile de confidențialitate și condițiile de utilizare ale site-urilor externe respective. Vă recomandăm să consultați politicile de confidențialitate și privind modulele cookie ale serviciilor terțe cu care interacționați prin conținut încorporat.
	',
]);

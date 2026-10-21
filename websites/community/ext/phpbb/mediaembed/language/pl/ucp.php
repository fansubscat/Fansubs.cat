<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Polska wersja językowa phpBB Media Embed 2.0.5 - 21.06.2026, Mateusz Dutko (vader) www.rnavspotters.pl
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
		<h3>Osadzone treści z innych stron internetowych</h3>
		“%1$s” mogą zawierać posty lub treści z osadzonymi materiałami pochodzącymi z zewnętrznych stron internetowych, w tym między innymi z serwisów YouTube, Facebook, Twitter i podobnych platform. Osadzone treści z tych zewnętrznych stron działają tak samo, jakbyś odwiedził bezpośrednio stronę, z której pochodzą.
		<br><br>Te zewnętrzne strony internetowe mogą gromadzić dane o użytkowniku, wykorzystywać pliki cookie, osadzać dodatkowe narzędzia śledzące stron trzecich oraz monitorować interakcje użytkownika z osadzonymi treściami, w tym śledzić te interakcje, jeśli użytkownik posiada konto i jest zalogowany na tej stronie.
		<br><br>Należy pamiętać, że takie działania pozostają poza kontrolą serwisu „%1$s” i podlegają politykom prywatności oraz warunkom korzystania z usług poszczególnych zewnętrznych stron internetowych. Zachęcamy do zapoznania się z politykami prywatności i politykami dotyczącymi plików cookie wszelkich usług stron trzecich, z którymi wchodzisz w interakcję za pośrednictwem osadzonych treści.
	',
]);

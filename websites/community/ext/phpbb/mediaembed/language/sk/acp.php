<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Slovak translation by Senky (https://github.com/senky)
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
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
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Nastavenia vkladania médií',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tu môžete konfigurovať nastavenia pre plugin vkladania médií.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zobraziť <samp>[media]</samp> BB kód pri prispievaní',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Ak je toto nezaškrtnuté, BB kód nebude zobrazený, no používatelia budú môcť používať <samp>[media]</samp> kód v ich príspevkoch',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Možnosti',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Povoliť v podpisoch',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Povolí v podpisoch používateľov vložiť obsah médií.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Ukladanie obsahu do vyrovnávacej pamäte',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Povoliť vyrovnávaciu pamäť Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'V niektorých prípadoch môže byť načítavanie médií z iných stránok pomalšie, najmä pri opakovanom načítavaní rovnakého obsahu (napríklad pri úprave príspevku). Povolením sa informácie získané službou Media Embed uložia lokálne do vyrovnávacej pamäte, čo by malo zlepšiť výkon.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konvertovať obyčajné URL adresy',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Povoľte konverziu obyčajných URL adries (bez značiek <samp>[media]</samp> alebo <samp>[url]</samp>) na vložený mediálny obsah. Zmena ovplyvní iba nové príspevky, pretože existujúce už boli spracované.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Veľkosť obsahu',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Povoliť obsah na celú šírku',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Povoľte rozšírenie väčšiny obsahu Media Embed na celú šírku oblasti príspevku pri zachovaní pôvodného pomeru strán.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Vlastná maximálna šírka obsahu',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Toto pole použite na určenie vlastnej maximálnej šírky jednotlivých stránok. Hodnota prepíše predvolenú veľkosť a možnosť plnej šírky vyššie. Každú stránku zadajte na nový riadok vo formáte <samp class="error">siteId:width</samp> s jednotkou <samp class="error">px</samp> alebo <samp class="error">%</samp>. Príklad:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Umiestnením ukazovateľa na stránku na stránke Spravovať stránky zobrazíte jej identifikátor.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Vyprázdniť vyrovnávaciu pamäť Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Vyrovnávacia pamäť Media Embed sa automaticky vyprázdni raz denne. Týmto tlačidlom ju môžete vyprázdniť ručne ihneď.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID stránky: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Táto stránka má konflikt s existujúcim BB kódom: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Vyskytli sa nasledujúce chyby:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: „%1$s“ nie je platný identifikátor stránky',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: „%2$s“ nie je platná šírka v jednotkách „px“ alebo „%%“',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Spravovať stránky vkladania médií',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tu môžete spravovať stránky, pre ktoré chcete povoliť získavanie obsahu pluginu vkladania médií vkladanie médií.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie sú žiadne stránky médií na zobrazenie.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Nasledujúce stránky už nie sú podporované alebo nefungujú. Znova odošlite túto stránku, aby sa odstránili.',
]);

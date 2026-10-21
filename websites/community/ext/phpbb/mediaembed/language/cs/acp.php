<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed nastavení',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Zde můžete konfigurovat nastaven í pro plugin Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zobrazit <samp>[media]</samp> BBCode na stránce s příspěvkem.',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Pokud je zakázáno, BBCode tlačítko nebude zobrazeno, přesto mohou uživatelé ve svých příspěvcích využít <samp>[media]</samp>.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Možnosti',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Povolí v podpisech uživatelů',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Povolí zobrazovat v podpisech uživatelů vložená média.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Ukládání obsahu do mezipaměti',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Povolit mezipaměť Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'V některých případech může být načítání médií z jiných webů pomalejší, zejména při opakovaném načítání stejného obsahu (například při úpravě příspěvku). Povolením se informace získané službou Media Embed uloží místně do mezipaměti, což by mělo zlepšit výkon.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Převádět prosté URL adresy',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Povolte pro převod prostých URL adres (bez značek <samp>[media]</samp> nebo <samp>[url]</samp>) na vložený mediální obsah. Změna tohoto nastavení ovlivní pouze nové příspěvky, protože stávající již byly zpracovány.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Velikost obsahu',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Povolit obsah v plné šířce',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Povolte pro rozšíření většiny obsahu Media Embed na celou šířku oblasti příspěvku při zachování původního poměru stran.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Vlastní maximální šířka obsahu',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Toto pole slouží k určení vlastní maximální šířky pro jednotlivé weby. Hodnota přepíše výchozí velikost a předchozí volbu plné šířky. Každý web zadejte na nový řádek ve formátu <samp class="error">siteId:width</samp> s jednotkou <samp class="error">px</samp> nebo <samp class="error">%</samp>. Příklad:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Umístěním ukazatele na web na stránce Správa webů zobrazíte jeho identifikátor.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Vyprázdnit mezipaměť Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Mezipaměť Media Embed se automaticky vyprázdní jednou denně. Tímto tlačítkem ji můžete vyprázdnit ručně ihned.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID stránky: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Došlo ke konfliktu této stránky s extistujícím BBCode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Došlo k následujícím chybám:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: „%1$s“ není platný identifikátor webu',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: „%2$s“ není platná šířka v jednotkách „px“ nebo „%%“',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Spravovat Media Embed stránky',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Zde můžete spravovat, jaké stránky mohou zobrazit obsaz z Media Embed pluginu.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nejsou zde žádné stránky s médii k zobrazení.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Následující weby již nejsou podporovány nebo nefungují. Odešlete tuto stránku znovu, aby byly odstraněny.',
]);

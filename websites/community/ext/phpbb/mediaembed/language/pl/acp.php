<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Polska wersja językowa phpBB Media Embed 1.1.2 - 10.09.2020, Mateusz Dutko (vader) www.rnavspotters.pl
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
	'ACP_MEDIA_SETTINGS'				=> 'Ustawienia osadzania multimediów',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tutaj można dokonać konfiguracji rozszerzenia Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Wyświetl znacznik BBcode <samp>[MEDIA]</samp> na forum',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Jeśli wybrano Nie, to znacznik <samp>[MEDIA]</samp> nie będzie wyświetlony, jednakże nadal można go używać na forum.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Options',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Znacznik <samp>[MEDIA]</samp> w podpisach',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Zezwól na używanie multimediów w sygnaturze.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Content caching',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Pamięć podręczna osadzania multimediów',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'W niektórych przypadkach ładowanie multimediów może działać wolniej, np. podczas ponownego ładowania treści przy edycji posta. Włączenie pamięci podręcznej może poprawić wydajność osadzania multimediów.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konwersja adresów URL',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Jeśli wybrano Tak, to adresy URL zostaną przekonwertowane bez użycia znacznika BBCode <samp>[media]</samp> lub <samp>[url]</samp>. Ta opcja wpłynie tylko na nowo osadzone multimedia. Dotychczasowe adresy URL nie zostaną przetworzone.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Content sizing',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Enable full width content',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Enable this to expand most Media Embed content to fill the full width of the post content area while maintaining its native aspect ratio.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Custom max-width content',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Use this field to define custom max-width values for individual sites. This will override the default size and the full width option above. Enter each site on a new line, using the format <samp class="error">siteId:width</samp> with either <samp class="error">px</samp> or <samp class="error">%</samp>. For example:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Hover your mouse over a site on the Manage sites page to reveal the site id name to use here.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Wyczyść pamięć podręczną',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Pamięć podręczna osadzania multimediów jest codziennie, automatycznie czyszczona. Można ją usunąć teraz poprzez kliknięcie przycisku.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID strony: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Ta strona ma konflikt z istniejącym znacznikiem BBCode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'The following errors were encountered:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” is not a valid site id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” is not a valid width in “px” or “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Zarządzaj stronami osadzania multimediów',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tutaj można dokonać konfiguracji wyświetlania elementów stron przez rozszerzenie Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie ma żadnych stron do wyświetlenia.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Poniższe strony nie są dłużej wspierane. Kliknij przycisk wyślij, aby zaktualizować listę.',
]);

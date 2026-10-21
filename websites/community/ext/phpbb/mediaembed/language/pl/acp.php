<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
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
	// Settings
	'ACP_MEDIA_SETTINGS'				=> 'Ustawienia osadzania multimediów',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Tutaj można dokonać konfiguracji rozszerzenia Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Wyświetl znacznik BBcode <samp>[media]</samp> na forum',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Jeśli wybrano Nie, to znacznik <samp>[media]</samp> nie będzie wyświetlony, jednakże nadal można go używać na forum.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opcje',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Znacznik <samp>[media]</samp> w podpisach',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Zezwól na używanie multimediów w sygnaturze.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Buforowanie zawartości',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Pamięć podręczna osadzania multimediów',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'W niektórych przypadkach ładowanie multimediów może działać wolniej, np. podczas ponownego ładowania treści przy edycji posta. Włączenie pamięci podręcznej może poprawić wydajność osadzania multimediów.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konwersja adresów URL',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Jeśli wybrano Tak, to adresy URL zostaną przekonwertowane bez użycia znacznika BBCode <samp>[media]</samp> lub <samp>[url]</samp>. Ta opcja wpłynie tylko na nowo osadzone multimedia. Dotychczasowe adresy URL nie zostaną przetworzone.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Rozmiar zawartości',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Tryb pełnoekranowy',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Włącz tę opcję, aby rozszerzyć większość osadzonych multimediów do szerokości obszaru treści posta, zachowując jednocześnie jego natywne proporcje.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Niestandardowy tryb pełnoekranowy',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Użyj tego pola, aby zdefiniować maksymalną szerokość multimediów dla poszczególnych witryn. Zastąpi to domyślną szerokość zdefiniowana w polu wyżej. Wprowadź każdą witrynę w nowym wierszu, używając formatu <samp class="error">siteId:szerokość</samp> przy pomocy <samp class="error">px</samp> lub <samp class="error">%</samp>. Na przykład:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Wskazówka:</strong> Najedź kursorem myszy na witrynę na stronie ”Zarządzaj stronami”, aby wyświetlić nazwę identyfikatora witryny do użycia w tym miejscu.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Wyczyść pamięć podręczną',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Pamięć podręczna osadzania multimediów jest codziennie, automatycznie czyszczona. Można ją usunąć teraz poprzez kliknięcie przycisku.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID strony: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Ta strona ma konflikt z istniejącym znacznikiem BBCode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Wystąpiły następujące błędy:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” nie jest prawidłową stroną internetową',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” nie jest prawidłową wartością szerokości w “px” lub “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Zarządzaj stronami osadzania multimediów',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Tutaj można dokonać konfiguracji wyświetlania elementów stron przez rozszerzenie Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nie ma żadnych stron do wyświetlenia.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Poniższe strony nie są dłużej wspierane. Kliknij przycisk wyślij, aby zaktualizować listę.',
]);

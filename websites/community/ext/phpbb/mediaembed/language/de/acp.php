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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed Einstellungen',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Hier kannst du die Einstellungen für das Media Embed PlugIn anpassen.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Zeige <samp>[media]</samp> BBCode auf der Posting Seite',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Wenn nicht erlaubt, wird der BBCode Button nicht angezeigt, dennoch können die User das <samp>[media]</samp> in ihren Posts verwenden.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Optionen',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Erlaubt in den Benutzer Signaturen',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Erlaube in den Usersignaturen Embedded Media Inhalte.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Inhaltszwischenspeicherung',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Media-Embed-Cache aktivieren',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Beim Laden von Medien anderer Websites kann die Leistung in manchen Fällen langsamer als üblich sein, besonders wenn derselbe Inhalt mehrfach geladen wird (z. B. beim Bearbeiten eines Beitrags). Durch Aktivierung werden die von Media Embed erfassten Informationen lokal zwischengespeichert, was die Leistung verbessern sollte.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konvertiere reine URLs',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Aktiviere diese Einstellung, um reine URLs (nicht eingeschlossen durch <samp>[media]</samp> oder <samp>[url]</samp> Tags) in Embedded Media Inhalte zu konvertieren. Beachte dass Änderungen sich nur auf neue Posts auswirken, da existierende Posts bereits geparsed wurden.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Inhaltsgröße',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Inhalte in voller Breite aktivieren',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Aktivieren, um die meisten Media-Embed-Inhalte auf die volle Breite des Beitragsbereichs zu erweitern und dabei ihr ursprüngliches Seitenverhältnis beizubehalten.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Benutzerdefinierte maximale Inhaltsbreite',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Mit diesem Feld können benutzerdefinierte maximale Breiten für einzelne Websites festgelegt werden. Dies überschreibt die Standardgröße und die obige Option für volle Breite. Jede Website in einer neuen Zeile im Format <samp class="error">siteId:width</samp> mit <samp class="error">px</samp> oder <samp class="error">%</samp> eingeben. Beispiel:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tipp:</strong> Den Mauszeiger auf der Seite Websites verwalten über eine Website bewegen, um ihre hier zu verwendende Website-ID anzuzeigen.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Media-Embed-Cache leeren',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Der Media-Embed-Cache wird automatisch einmal täglich geleert. Mit dieser Schaltfläche kann er jetzt manuell geleert werden.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Seiten id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Diese Seite hat einen konflickt mit einem ebreits existierenden BBCode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Folgende Fehler sind aufgetreten:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: „%1$s“ ist keine gültige Website-ID',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: „%2$s“ ist keine gültige Breite in „px“ oder „%%“',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Verwaltung der Media Embed Seiten',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Hier kannst du die Seiten verwalten welche durch das Media Embed PlugIn angezeigt werden dürfen.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Es gibt keine Media Seiten zum anzeigen.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Folgende Websites werden nicht mehr unterstützt oder funktionieren nicht mehr. Sende diese Seite erneut ab, um sie zu entfernen.',
]);

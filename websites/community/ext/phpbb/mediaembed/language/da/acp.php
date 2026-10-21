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
	'ACP_MEDIA_SETTINGS'				=> 'Indstillinger for medieindlejring',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Her kan du konfigurere indstillingerne til Media Embed PlugIn.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Vis <samp>[media]</samp>-BBkode på siden hvor indlæg skrives',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Hvis den er valgt fra vises BBkode-knappen ikke, men brugerne kan dog stadig bruge <samp>[media]</samp>-tagget i deres indlæg',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Indstillinger',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Tillad i brugersignaturer',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Tillad at brugersignaturer viser indlejret medieindhold.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Caching af indhold',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Aktivér Media Embed-cache',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'I visse tilfælde kan indlæsning af medier fra andre websteder være langsommere end normalt, især når samme indhold indlæses flere gange (f.eks. ved redigering af et indlæg). Aktivering gemmer de oplysninger, Media Embed indsamler, lokalt i cachen og bør forbedre ydeevnen.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Konvertér almindelige URL-adresser',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Aktivér for at konvertere almindelige URL-adresser (som ikke er omgivet af <samp>[media]</samp>- eller <samp>[url]</samp>-tags) til indlejret medieindhold. Ændringen påvirker kun nye indlæg, da eksisterende indlæg allerede er behandlet.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Størrelse på indhold',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Aktivér indhold i fuld bredde',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Aktivér for at udvide det meste Media Embed-indhold til hele indlæggets bredde, samtidig med at det oprindelige billedformat bevares.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Tilpasset maksimal indholdsbredde',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Brug feltet til at angive en tilpasset maksimal bredde for enkelte websteder. Dette tilsidesætter standardstørrelsen og indstillingen for fuld bredde ovenfor. Indtast hvert websted på en ny linje i formatet <samp class="error">siteId:width</samp> med enten <samp class="error">px</samp> eller <samp class="error">%</samp>. Eksempel:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Hold musen over et websted på siden Administrer websteder for at se det websteds-id, der skal bruges her.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Ryd Media Embed-cache',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embed-cachen ryddes automatisk én gang dagligt, men knappen kan bruges til at rydde den manuelt nu.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Sted-id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Stedet er i konflikt med en eksisterende BBkode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Følgende fejl opstod:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” er ikke et gyldigt websteds-id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” er ikke en gyldig bredde i “px” eller “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Håndter steder for medieindlejring',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Her kan du håndtere stederne som du vil tillade Media Embed PlugIn at vise indhold fra.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Der er ikke nogen mediesteder at vise.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Følgende websteder understøttes eller fungerer ikke længere. Indsend siden igen for at fjerne dem.',
]);

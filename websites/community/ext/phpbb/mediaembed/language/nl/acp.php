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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed Instellingen',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Hier kunt u instellingen voor de Media Embed PlugIn configureren.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Toon de <samp>[media]</samp> BBCode op de reactie pagina',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Als dit is niet toegestaan zal de BBCode knop niet worden getoond maar gebruikers kunnen nog steeds de <samp>[media]</samp> tag in berichten gebruiken',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opties',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Toestaan in onderschriften',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Toestaan dat onderschriften ook ingevoegde media inhoud (Embed Media Content) bevatten.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Inhoudscache',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Media Embed-cache inschakelen',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In sommige gevallen kan het laden van media van andere websites trager zijn dan normaal, vooral wanneer dezelfde inhoud meerdere keren wordt geladen (bijvoorbeeld bij het bewerken van een bericht). Als je dit inschakelt, wordt de informatie die Media Embed van websites verzamelt lokaal opgeslagen, wat de prestaties zou moeten verbeteren.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Gewone URL’s converteren',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Schakel dit in om gewone URL’s (niet omsloten door <samp>[media]</samp>- of <samp>[url]</samp>-tags) om te zetten in ingesloten media. Deze wijziging is alleen van toepassing op nieuwe berichten, omdat bestaande berichten al zijn verwerkt.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Inhoudsgrootte',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Inhoud over volledige breedte inschakelen',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Schakel dit in om de meeste Media Embed-inhoud over de volledige breedte van het bericht uit te breiden met behoud van de oorspronkelijke beeldverhouding.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Aangepaste maximale inhoudsbreedte',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Gebruik dit veld om aangepaste maximale breedtes voor afzonderlijke websites in te stellen. Dit overschrijft de standaardgrootte en de optie voor volledige breedte hierboven. Voer elke website op een nieuwe regel in met de notatie <samp class="error">siteId:width</samp> en gebruik <samp class="error">px</samp> of <samp class="error">%</samp>. Voorbeeld:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Beweeg op de pagina Websites beheren met de muis over een website om de website-ID te zien die je hier moet gebruiken.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Media Embed-cache legen',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'De Media Embed-cache wordt eenmaal per dag automatisch geleegd. Met deze knop kun je de cache nu handmatig legen.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Website-ID: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Deze website heeft een conflict met een bestaande BBCode: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'De volgende fouten zijn opgetreden:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” is geen geldige website-ID',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” is geen geldige breedte in “px” of “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Beheer Media Embed Websites',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Hier kunt u de websites beheren van welke u de de Media Embed PlugIn wilt toestaan inhoud te tonen.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Er zijn geen media websites om te tonen.',
	'ACP_MEDIA_SITES_MISSING'			=> 'De volgende websites worden niet meer ondersteund of werken niet meer. Verzend deze pagina opnieuw om ze te verwijderen.',
]);

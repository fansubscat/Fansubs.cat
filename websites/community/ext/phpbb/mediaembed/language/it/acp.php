<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @Italian language By alex75 https://www.phpbb-store.it
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
	'ACP_MEDIA_SETTINGS'				=> 'Impostazioni PlugIn Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Qui puoi configurare le impostazioni per il PlugIn Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Visualizza BBCode <samp>[MEDIA]</samp> nella pagina di scrittura',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Se non consentito, il pulsante BBCode non verrà visualizzato, tuttavia gli utenti possono ancora utilizzare il tag <samp> [media] </samp> nei loro messaggi',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opzioni',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Consenti nelle firme utente',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Consenti di visualizzare il contenuto multimediale incorporato nelle firme utente.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Cache del contenuto',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Abilita la cache del Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'In alcuni casi puoi notare una performance più lenta del normale quando carichi media da altri siti, specialmente se carichi lo stesso contenuto piu\' volte (per esempio mentre modifichi un post). Abilitare questo mettera\' le informazioni raccolte da Media Embed in cache locale e dovrebbe migliorare la performance.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Converti URL semplici',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Abilitare per convertire URL semplici (non racchiuso tra i tag <samp>[media]</samp> o <samp>[url]</samp> tags) ed incorporare i media. Tieni presente che la modifica di questa impostazione avrà effetto solo sui nuovi post, in quanto i post esistenti sono già stati analizzati.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Dimensionamento contenuti',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Abilita contenuti a piena larghezza',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Abilitalo per espandere la maggior parte dei contenuti di Media Embed per riempire la piena larghezza dell\'area del contenuto del post mantenendo il suo aspect ratio nativo.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Massima larghezza personalizzata del contenuto',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Usa questo campo per definire valori personalizzati di larghezza massima per i siti individuali. Cio\' sovrascrivera\' le dimensioni predefinite e l\'opzione di piena larghezza soprastante. Inserisci ogni sito in una nuova riga, usando il formato <samp class="error">Id Sito:larghezza</samp> con o <samp class="error">px</samp> o <samp class="error">%</samp>. Per esempio:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Consiglio:</strong> tieni il cursore del mouse su di un sito nella pagina Gestisci Siti per rivelare l\'Id Sito da usare qui.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Vuota la cache del Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'La cache del Media Embed viene automaticamente svuotata una volta al giorno, ad ogni modo questo pulsante puo\' essere usato per vuotarla manualmente in questo momento.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID sito: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Questo sito è in conflitto con un BBCode esistente: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Sono stati riscontrati i seguenti errori:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” non e\' un Id Sito valido',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” non e\' una larghezza valida in “px” o “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gestisci siti Per il PlugIn Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Qui puoi gestire i siti che vuoi consentire al Plugin Media Embed di visualizzare il contenuto.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Non ci sono siti con media da visualizzare.',
	'ACP_MEDIA_SITES_MISSING'			=> 'I seguenti siti non sono piu\' supportati o funzionanti. Si prega di risottomettere questa pagina per rimuoverli.',
]);

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
	'ACP_MEDIA_SETTINGS'				=> 'Setări de încorporare media',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Aici puteți configura setările pentru Media Embed PlugIn.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Afișați <samp>[MEDIA]</samp> BBCode pe pagina de postare',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Dacă nu este permis, butonul BBCode nu va fi afișat, totuși utilizatorii pot folosi în continuare eticheta <samp>[media]</samp> în postările lor.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opțiuni',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Permiteți în semnăturile utilizatorilor',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Permiteți semnăturilor utilizatorilor să afișeze conținut media încorporat.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Memorarea în cache a conținutului',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Activați cacheul Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'În unele cazuri, este posibil să observați o performanță mai lentă decât cea normală atunci când încărcați conținut media de pe alte site-uri, mai ales când încărcați același conținut de mai multe ori (de exemplu, când editați o postare). Activarea acestui lucru va stoca în cache informațiile pe care Media Embed le adună de pe site-uri la nivel local și ar trebui să îmbunătățească performanța.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convertiți adrese URL simple',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Activați această opțiune pentru a converti adresele URL simple (nu sunt incluse în etichete <samp>[media]</samp> sau <samp>[url]</samp>) în conținut media încorporat. Rețineți că modificarea acestei setări va afecta numai postările noi, deoarece postările existente au fost deja analizate.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Dimensiunea conținutului',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Activați conținutul cu lățime completă',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Activați această opțiune pentru a extinde majoritatea conținutului Media Embed pentru a umple întreaga lățime a zonei de conținut postare, păstrând în același timp raportul de aspect nativ.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Conținut personalizat cu lățime maximă',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Utilizați acest câmp pentru a defini valori personalizate pentru lățimea maximă pentru site-uri individuale. Aceasta va suprascrie dimensiunea implicită și opțiunea de lățime completă de mai sus. Introduceți fiecare site pe o nouă linie, folosind formatul <samp class="error">siteId:width</samp> fie cu <samp class="error">px</samp>, fie cu <samp class="error">%</samp>. De exemplu:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Sfat:</strong> treceți mouse-ul peste un site din pagina Gestionați site-urile pentru a dezvălui numele de cod al site-ului de utilizat aici.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Ștergeți memoria cache încorporată',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Cacheul Media Embed este curățat automat o dată pe zi, totuși acest buton poate fi folosit pentru a-și curăța manual memoria cache acum.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Id-ul site-ului: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Acest site intră în conflict cu un BBCode existent: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Au fost întâlnite următoarele erori:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” nu este un ID valid de site',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” nu este o lățime validă în „px” sau “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gestionați site-urile de încorporare media',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Aici puteți gestiona site-urile din care doriți să permiteți pluginului Media Embed să afișeze conținut.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Nu există site-uri media de afișat.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Următoarele site-uri nu mai sunt acceptate sau nu mai funcționează. Vă rugăm să retrimiteți această pagină pentru a le elimina.',
]);

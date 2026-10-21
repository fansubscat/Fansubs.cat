<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * Estonian translation by phpBBeesti.net
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
	'ACP_MEDIA_SETTINGS'				=> 'Manustatud meedia seaded',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Sellel leheküljel on sul võimalik seadistada laienduse manustatud meedia seadeid.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Näita <samp>[media]</samp> BBkoodi positamise leheküljel',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Kui pole lubatud, siis BBkoodi nuppu ei näidata, kuid siiski on kasutajatel võimalik kasutada e <samp>[media]</samp> silti oma postitustes.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Valikud',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Luba kasutaja signatuurides',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Kas soovid lubada manustatud meediat oma kasutaja signatuuridest või siiski mitte.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Sisu vahemällu salvestamine',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Luba Media Embedi vahemälu',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Mõnel juhul võib meedia laadimine teistelt saitidelt olla tavapärasest aeglasem, eriti sama sisu korduval laadimisel (näiteks postituse muutmisel). Selle lubamisel salvestatakse Media Embedi saitidelt kogutud teave kohalikku vahemällu ja jõudlus peaks paranema.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Teisenda tavalised URL-id',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Luba tavaliste URL-ide (mis pole <samp>[media]</samp> või <samp>[url]</samp> märgendites) teisendamine manustatud meediasisuks. Muudatus mõjutab ainult uusi postitusi, sest olemasolevad postitused on juba töödeldud.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Sisu suurus',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Luba täislaiuses sisu',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Luba enamiku Media Embedi sisu laiendamine postituse sisuala täislaiusele, säilitades algse kuvasuhte.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Sisu kohandatud maksimaalne laius',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Kasuta seda välja üksikute saitide kohandatud maksimaalse laiuse määramiseks. See alistab vaikesuuruse ja ülaltoodud täislaiuse valiku. Sisesta iga sait uuele reale vormingus <samp class="error">siteId:width</samp>, kasutades ühikut <samp class="error">px</samp> või <samp class="error">%</samp>. Näide:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Vihje:</strong> Siin kasutatava saidi ID nägemiseks vii hiirekursor saitide haldamise lehel saidi kohale.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Tühjenda Media Embedi vahemälu',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Media Embedi vahemälu tühjendatakse automaatselt kord päevas, kuid selle nupuga saab vahemälu kohe käsitsi tühjendada.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Lehekülje ID: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'See lehekülg on konfliktis juba eksisteeriva BBkoodiga: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Ilmnesid järgmised vead:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: „%1$s” ei ole kehtiv saidi ID',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: „%2$s” ei ole kehtiv laius ühikutes „px” või „%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Halda Manustatud Meedia Lehekülgesi',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Sellel leheküljel on sul võimalik hallata veebilehti, kust sa soovid lubada manustada sisu.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Ei ole kuvada ühtegi meedia lehekülge.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Järgmised saidid pole enam toetatud või ei tööta. Nende eemaldamiseks esita leht uuesti.',
]);

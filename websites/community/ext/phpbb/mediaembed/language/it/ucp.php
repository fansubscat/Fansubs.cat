<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025 phpBB Limited <https://www.phpbb.com>
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
	'MEDIA_EMBED_PRIVACY_POLICY' => '
		<br><br>
		<h3>Contenuti incorporati da altri siti web</h3>
		“%1$s” può includere messaggi o contenuti con materiale incorporato da siti web esterni, inclusi YouTube, Facebook, Twitter e piattaforme simili. I contenuti incorporati da questi siti esterni si comportano come se avessi visitato direttamente il sito web di origine.
		<br><br>Questi siti web esterni possono raccogliere dati su di te, usare cookie, incorporare ulteriore tracciamento di terze parti e monitorare la tua interazione con i contenuti incorporati, anche se disponi di un account e hai effettuato l’accesso al sito web.
		<br><br>Tieni presente che tali attività esulano dal controllo di “%1$s” e sono regolate dalle informative sulla privacy e dai termini di servizio dei rispettivi siti web esterni. Ti invitiamo a consultare le informative sulla privacy e sui cookie di qualsiasi servizio di terze parti con cui interagisci tramite contenuti incorporati.
	',
]);

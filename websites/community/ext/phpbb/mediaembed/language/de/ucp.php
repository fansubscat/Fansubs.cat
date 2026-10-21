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
		<h3>Eingebettete Inhalte anderer Websites</h3>
		„%1$s“ kann Beiträge oder Inhalte mit eingebettetem Material externer Websites enthalten, darunter YouTube, Facebook, Twitter und ähnliche Plattformen. Eingebettete Inhalte dieser externen Websites verhalten sich so, als hättest du die ursprüngliche Website direkt besucht.
		<br><br>Diese externen Websites können Daten über dich sammeln, Cookies verwenden, zusätzliche Nachverfolgung durch Dritte einbetten und deine Interaktion mit eingebetteten Inhalten überwachen. Dies schließt die Nachverfolgung deiner Interaktion ein, wenn du ein Konto besitzt und auf der betreffenden Website angemeldet bist.
		<br><br>Bitte beachte, dass solche Aktivitäten außerhalb der Kontrolle von „%1$s“ liegen und den Datenschutzrichtlinien und Nutzungsbedingungen der jeweiligen externen Websites unterliegen. Wir empfehlen, die Datenschutz- und Cookie-Richtlinien aller Drittanbieterdienste zu prüfen, mit denen du über eingebettete Inhalte interagierst.
	',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2025 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 * @正體中文化 竹貓星球 <http://phpbb-tw.net/phpbb/>
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
		<h3>其他網站的嵌入內容</h3>
		「%1$s」可能包含嵌入外部網站（包括但不限於 YouTube、Facebook、Twitter 及類似平台）的貼文或內容。嵌入來自這些外部網站的內容的行為與您直接訪問原始網站的行為相同。
		<br><br>這些外部網站可能會收集您的資料、使用 Cookies、嵌入額外的第三方追蹤程式並監控您與嵌入內容的互動，包括在您擁有帳戶並登入網站的情況下追蹤您的互動。
		<br><br>請注意，此類活動不受「%1$s」的控制，並受相應外部網站的隱私權政策和服務條款約束。我們建議您查看透過嵌入內容與之互動的任何第三方服務的隱私權和 Cookies 政策。
	',
]);

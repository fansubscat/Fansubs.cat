<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @简体中文语言　David Yin <https://www.phpbbchinese.com/>
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
		<h3>来自其它网站的嵌入内容</h3>
		“%1$s” 的帖子或内容可能包含来自外部网站的嵌入内容，包括但不限于 YouTube，Facebook，Twitter，和其它类似的平台。来自这些外部网站的嵌入内容表现与你直接访问原始网站相同。
		<br><br>这些外部网站可能会收集有关你的数据，使用 cookie，嵌入第三方的跟踪功能，以及监察你同这些嵌入内容的互动，包括在你拥有账户并登入该网站的情况下的跟踪你的互动。
		<br><br>请注意，此类活动不受 “%1$s” 的控制，并受相应外部网站的隐私证词和服务条款的约束。我们建议你查看通过嵌入内容与之互动的任何第三方服务的隐私和 Cookie 政策。
	',
]);

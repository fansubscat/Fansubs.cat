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
		<h3>Embedded Content from Other Websites</h3>
		“%1$s” may include posts or content that contain embedded material from external websites, including but not limited to YouTube, Facebook, Twitter, and similar platforms. Embedded content from these external sites behaves in the same way as if you had visited the originating website directly.
		<br><br>These external websites may collect data about you, use cookies, embed additional third-party tracking, and monitor your interaction with the embedded content, including tracking your interaction if you have an account and are logged in to that website.
		<br><br>Please note that such activity is beyond the control of “%1$s” and is governed by the privacy policies and terms of service of the respective external websites. We encourage you to review the privacy and cookie policies of any third-party services you interact with through embedded content.
	',
]);

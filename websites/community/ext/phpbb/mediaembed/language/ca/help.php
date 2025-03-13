<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
 * DO NOT CHANGE
 */
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, [
	'HELP_EMBEDDING_MEDIA'			=> 'Embedding Media',
	'HELP_EMBEDDING_MEDIA_QUESTION'	=> 'How to embed media from other sites into posts',
	'HELP_EMBEDDING_MEDIA_ANSWER'	=> 'Users can embed content such as videos and audio from allowed sites using
										the <strong>[media][/media]</strong> tags, or from simply posting a supported
										URL in plain text. For example:<br /><br />
										<strong>[media]</strong>%1$s<strong>[/media]</strong>
										<br /><br />As noted above, the link could also be used without the
										<strong>[media]</strong> tags.
										<br /><br />The example shown here would generate:<br /><br />%2$s
										<br /><br />The following sites are supported:<br /><samp>%3$s.</samp>
										<br /><br />For complete documentation on supported sites and example URLs,
										visit the <a href="https://s9etextformatter.readthedocs.io/Plugins/MediaEmbed/Sites/">
										MediaEmbed Plugin Documentation</a>.',
	'HELP_EMBEDDING_MEDIA_DEMO'		=>	'https://youtu.be/QH2-TGUlwu4',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Russian translation by HD321kbps
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
	'ACP_MEDIA_SETTINGS'				=> 'Настройки Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Здесь вы можете настроить параметры для Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Показывать <samp>[MEDIA]</samp> бб-код на странице размещения сообщений',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Если это запрещено, кнопка бб-кода не отображается, но пользователи могут использовать <samp>[media]</samp> бб-код в своих сообщениях.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Options',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Разрешить в подписях пользователей',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Разрешить в подписях пользователей показывать Media Embed.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Content caching',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Включить кэш Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Иногда ссылки на медиа контент не содержат всю необходимую информацию для их встраивания в сообщение. В этом случае дополнительная информация со страницы, на которой находится данный контент, должна быть загружена, проверена и обработана. Этот процесс производится только один раз для каждой такой ссылки при обработке сообщения, но если сообщение обрабатывается несколько раз (например, при его редактировании), локальная копия внешних данных может быть сохранена в кэше для увеличения производительности.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Преобразовывать ссылки',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Если включено, простые ссылки (не обрамлённые тегами <samp>[media]</samp> или <samp>[url]</samp>) будут преобразовываться во встроенный медиа контент. Учтите, что действие данной настройки распространяется только на новые сообщения, так как старые сообщения уже сохранены в базе данных.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Content sizing',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Enable full width content',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Enable this to expand most Media Embed content to fill the full width of the post content area while maintaining its native aspect ratio.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Custom max-width content',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Use this field to define custom max-width values for individual sites. This will override the default size and the full width option above. Enter each site on a new line, using the format <samp class="error">siteId:width</samp> with either <samp class="error">px</samp> or <samp class="error">%</samp>. For example:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Hover your mouse over a site on the Manage sites page to reveal the site id name to use here.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Очистить кэш Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Кэш Media Embed очищается автоматически один раз в день (если кэш включен выше). Здесь можно очистить кэш вручную.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Id сайта: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Этот сайт не подключен у существующему бб-коду: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'The following errors were encountered:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” is not a valid site id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” is not a valid width in “px” or “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Управление сайтами для Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Здесь, вы можете управлять сайтами, которые вы хотите подключать к Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Нет сайтов для отображения.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);

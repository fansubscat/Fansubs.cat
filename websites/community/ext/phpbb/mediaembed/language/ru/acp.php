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
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Показывать <samp>[media]</samp> бб-код на странице размещения сообщений',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Если это запрещено, кнопка бб-кода не отображается, но пользователи могут использовать <samp>[media]</samp> бб-код в своих сообщениях.',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Настройки',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Разрешить в подписях пользователей',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Разрешить в подписях пользователей показывать Media Embed.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Кэширование содержимого',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Включить кэш Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Иногда ссылки на медиа контент не содержат всю необходимую информацию для их встраивания в сообщение. В этом случае дополнительная информация со страницы, на которой находится данный контент, должна быть загружена, проверена и обработана. Этот процесс производится только один раз для каждой такой ссылки при обработке сообщения, но если сообщение обрабатывается несколько раз (например, при его редактировании), локальная копия внешних данных может быть сохранена в кэше для увеличения производительности.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Преобразовывать ссылки',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Если включено, простые ссылки (не обрамлённые тегами <samp>[media]</samp> или <samp>[url]</samp>) будут преобразовываться во встроенный медиа контент. Учтите, что действие данной настройки распространяется только на новые сообщения, так как старые сообщения уже сохранены в базе данных.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Размер содержимого',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Включить содержимое на всю ширину',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Включите, чтобы развернуть большинство содержимого Media Embed на всю ширину области сообщения с сохранением исходного соотношения сторон.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Пользовательская максимальная ширина содержимого',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Используйте это поле для задания максимальной ширины отдельных сайтов. Значение переопределяет размер по умолчанию и настройку полной ширины выше. Введите каждый сайт с новой строки в формате <samp class="error">siteId:width</samp>, используя <samp class="error">px</samp> или <samp class="error">%</samp>. Пример:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Совет:</strong> Наведите указатель мыши на сайт на странице управления сайтами, чтобы увидеть его идентификатор.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Очистить кэш Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'Кэш Media Embed очищается автоматически один раз в день (если кэш включен выше). Здесь можно очистить кэш вручную.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Id сайта: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Этот сайт не подключен у существующему бб-коду: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Возникли следующие ошибки:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: «%1$s» не является допустимым идентификатором сайта',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: «%2$s» не является допустимой шириной в «px» или «%%»',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Управление сайтами для Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Здесь, вы можете управлять сайтами, которые вы хотите подключать к Media Embed.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Нет сайтов для отображения.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Следующие сайты больше не поддерживаются или не работают. Повторно отправьте эту страницу, чтобы удалить их.',
]);

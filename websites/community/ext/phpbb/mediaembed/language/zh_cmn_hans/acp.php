<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * @简体中文语言　David Yin <https://www.phpbbchinese.com/>
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
	'ACP_MEDIA_SETTINGS'				=> 'Media Embed Settings',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> '这里可设置 Media Embed PlugIn 的相关参数。',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '在发帖页面显示 <samp>[MEDIA]</samp> BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> '若禁用， BBCode 按钮就不会显示，但用户还是可以在帖子中使用 <samp>[media]</samp> 标签。',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Options',
	'ACP_MEDIA_ALLOW_SIG'				=> '允许使用在用户签名',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> '允许在用户签名处显示嵌入媒体内容。',
	'ACP_MEDIA_CACHE_LEGEND'			=> '内容缓存',
	'ACP_MEDIA_ENABLE_CACHE'			=> '启用媒体内容缓存',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> '在某些情况下，当从其它网站加载媒体时，您可能会注意到比正常的性能慢，特别是在多次加载相同的内容时（例如，当编辑一个帖子时）。启用此功能将缓存媒体嵌入从网站收集的信息，应能改善性能。',
	'ACP_MEDIA_PARSE_URLS'				=> '转换普通 URL 网址',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> '启用它来转换普通网址（没有包含在<samp>[media]</samp> or <samp>[url]</samp> 标签中） 为嵌入式媒体内容。请注意，此改变将只影响新发帖子，因为旧帖子已经被解析过了。',
	'ACP_MEDIA_WIDTH_LEGEND'			=> '内容尺寸',
	'ACP_MEDIA_FULL_WIDTH'				=> '启用全宽媒体内容',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> '启用此功能可将大多数嵌入媒体的内容扩展到帖子内容区域，同时保持其原始长宽比。',
	'ACP_MEDIA_MAX_WIDTH'				=> '自定义内容的最大宽度',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> '使用这个字段来定义单个网站的自定义最大宽度值。 这将覆盖默认尺寸和上面的全宽选项。 每一行输入一个网站，使用下面格式 <samp class="error">siteId:width</samp> with either <samp class="error">px</samp> 或者 <samp class="error">%</samp>。 例如：<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">提示：</strong> 将鼠标悬停在管理网站页面的一个网站上，以显示在这里使用的网站ID名称。</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> '清理媒体嵌入缓存',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> '媒体嵌入缓存会每天自动清除一次，但是这个按钮可以用来手动清除其缓存。',
	'ACP_MEDIA_SITE_TITLE'				=> '网站 id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> '此网站与现有的 BBCode: [%s] 有冲突',
	'ACP_MEDIA_ERROR_MSG'				=> '出现下面错误：<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” 是无效的网站 id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” 是无效的的宽度 “px” 或 “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> '管理可嵌入媒体的网站',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> '你可以管理你所允许 Media Embed PlugIn 在帖子中显示内容的网站。',
	'ACP_MEDIA_SITES_ERROR'				=> '没有可用的媒体网站用于显示。.',
	'ACP_MEDIA_SITES_MISSING'			=> '以下网站已不再被支持或工作。请重新提交此页面以删除它们。',
]);

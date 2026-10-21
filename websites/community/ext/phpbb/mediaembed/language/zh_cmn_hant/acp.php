<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
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
	// Settings
	'ACP_MEDIA_SETTINGS'				=> '媒體嵌入設置',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> '您可以在此處配置媒體嵌入外掛的設定。',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> '顯示 <samp>[media]</samp> BBCode 在發文的頁面',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> '如果不允許，BBCode 按鈕將不會顯示，但會員仍然可以在他們的文章中使用 <samp>[media]</samp> 標籤。',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> '選項',
	'ACP_MEDIA_ALLOW_SIG'				=> '允許會員簽名',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> '允許會員簽名顯示嵌入的媒體內容。',
	'ACP_MEDIA_CACHE_LEGEND'			=> '內容快取',
	'ACP_MEDIA_ENABLE_CACHE'			=> '啟用媒體嵌入快取',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> '在某些情況下，您可能會注意到從其他站點加載媒體時的性能比正常速度慢，尤其是在多次加載相同內容時（例如：編輯文章時）。啟用此功能將快取 Media Embed 從本地站點收集的資料以提高性能。',
	'ACP_MEDIA_PARSE_URLS'				=> '轉換普通網址',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> '啟用此選項可將純網址（未包含在 <samp>[media]</samp> 或 <samp>[url]</samp> 標籤中）轉換為嵌入的媒體內容。請注意，更改此設置只會影響新文章，因為現有文章已被解析。',
	'ACP_MEDIA_WIDTH_LEGEND'			=> '內容尺寸',
	'ACP_MEDIA_FULL_WIDTH'				=> '啟用全寬內容',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> '啟用此選項可將大多數 Media Embed 內容延伸至貼文內容區域的完整寬度，同時保持原始長寬比。',
	'ACP_MEDIA_MAX_WIDTH'				=> '自訂內容最大寬度',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> '使用此欄位為個別網站設定自訂最大寬度。這會覆寫預設尺寸及上方的全寬選項。每個網站另起一行，使用 <samp class="error">siteId:width</samp> 格式並指定 <samp class="error">px</samp> 或 <samp class="error">%</samp>。例如：<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">提示：</strong>在「管理網站」頁面將滑鼠移到網站上，即可查看此處需使用的網站 ID。</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> '清除媒體嵌入快取',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> '媒體嵌入快取每天自動清除一次，但是現在可以使用此按鈕手動清除之。',
	'ACP_MEDIA_SITE_TITLE'				=> '站點 id：%s',
	'ACP_MEDIA_SITE_DISABLED'			=> '此站點與現有 BBCode 衝突：[%s]',
	'ACP_MEDIA_ERROR_MSG'				=> '發生下列錯誤：<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s ::「%1$s」不是有效的網站 ID',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s ::「%2$s」不是有效的「px」或「%%」寬度',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> '管理媒體嵌入站點',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> '您可以在此處管理要允許媒體嵌入外掛顯示內容的站點。',
	'ACP_MEDIA_SITES_ERROR'				=> '沒有可顯示的媒體網站。',
	'ACP_MEDIA_SITES_MISSING'			=> '以下站點不再受支持或不再工作。 請重新送出此頁面以將其刪除。',
]);

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
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
	'ACP_MEDIA_SETTINGS'				=> 'Configurações do Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Aqui você pode configurar as configurações para o Media Embed PlugIn.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Mostrar BBCode <samp>[MEDIA]</samp> na página de postagem',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Se não for permitido, o botão BBCode não será exibido, no entanto, os usuários ainda podem usar a tag <samp>[media]</samp> em suas postagens',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Options',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Permitir em assinaturas de usuários',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Permitir que assinaturas de usuários exiba conteúdo do Media Embed.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Content caching',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Habilitar cache de incorporação de mídia',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Em alguns casos, você pode notar um desempenho mais lento do que o normal ao carregar mídia de outros sites, especialmente ao carregar o mesmo conteúdo várias vezes (por exemplo, ao editar uma postagem). Ativar isso irá armazenar em cache as informações que o Media Embed coleta de sites localmente e deve melhorar o desempenho.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Converter URLs simples',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Habilite isto para converter URLs simples (não incluídos nas tags <samp>[media]</samp> ou <samp>[url]</samp>) em conteúdo de mídia incorporado. Observe que alterar esta configuração afetará apenas as novas postagens, pois as postagens existentes já foram analisadas.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Content sizing',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Enable full width content',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Enable this to expand most Media Embed content to fill the full width of the post content area while maintaining its native aspect ratio.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Custom max-width content',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Use this field to define custom max-width values for individual sites. This will override the default size and the full width option above. Enter each site on a new line, using the format <samp class="error">siteId:width</samp> with either <samp class="error">px</samp> or <samp class="error">%</samp>. For example:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Tip:</strong> Hover your mouse over a site on the Manage sites page to reveal the site id name to use here.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Limpar cache de incorporação de mídia',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'O cache do Media Embed é purgado automaticamente uma vez por dia, no entanto, este botão pode ser usado para limpar manualmente seu cache agora.',
	'ACP_MEDIA_SITE_TITLE'				=> 'Site id: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Este site está em conflito com um BBCode existente: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'The following errors were encountered:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” is not a valid site id',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” is not a valid width in “px” or “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gerenciar sites do Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Aqui você pode gerenciar os sites que deseja permitir que o Media Embed PlugIn mostre o conteúdo.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Não há sites de mídia para exibir.',
	'ACP_MEDIA_SITES_MISSING'			=> 'The following sites are no longer supported or working. Please re-submit this page to remove them.',
]);

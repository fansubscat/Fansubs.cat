<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 * Brazilian Portuguese translation by eunaumtenhoid (c) 2017 [ver 1.0.1] (https://github.com/phpBBTraducoes)
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
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Mostrar BBCode <samp>[media]</samp> na página de postagem',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Se não for permitido, o botão BBCode não será exibido, no entanto, os usuários ainda podem usar a tag <samp>[media]</samp> em suas postagens',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opções',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Permitir em assinaturas de usuários',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Permitir que assinaturas de usuários exiba conteúdo do Media Embed.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Cache de conteúdo',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Habilitar o cache do Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'Em alguns casos, o carregamento de mídia de outros sites pode ser mais lento que o normal, especialmente ao carregar o mesmo conteúdo várias vezes (por exemplo, ao editar uma publicação). Habilitar esta opção armazenará localmente as informações coletadas pelo Media Embed e deve melhorar o desempenho.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Converter URLs simples',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Habilite para converter URLs simples (sem as tags <samp>[media]</samp> ou <samp>[url]</samp>) em conteúdo de mídia incorporado. A alteração afeta apenas novas publicações, pois as existentes já foram processadas.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Dimensionamento do conteúdo',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Habilitar conteúdo em largura total',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Habilite para expandir a maior parte do conteúdo Media Embed por toda a largura da área da publicação, mantendo a proporção original.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Largura máxima personalizada do conteúdo',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Use este campo para definir larguras máximas personalizadas para sites individuais. Isso substituirá o tamanho padrão e a opção de largura total acima. Insira cada site em uma nova linha no formato <samp class="error">siteId:width</samp>, usando <samp class="error">px</samp> ou <samp class="error">%</samp>. Exemplo:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Dica:</strong> Passe o mouse sobre um site na página Gerenciar sites para exibir o ID do site que deve ser usado aqui.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Limpar o cache do Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'O cache do Media Embed é limpo automaticamente uma vez por dia, mas este botão permite limpá-lo manualmente agora.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID do site: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Este site está em conflito com um BBCode existente: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Foram encontrados os seguintes erros:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” não é um ID de site válido',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” não é uma largura válida em “px” ou “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gerenciar sites do Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Aqui você pode gerenciar os sites que deseja permitir que o Media Embed PlugIn mostre o conteúdo.',
	'ACP_MEDIA_SITES_ERROR'				=> 'Não há sites de mídia para exibir.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Os sites a seguir não são mais compatíveis ou deixaram de funcionar. Reenvie esta página para removê-los.',
]);

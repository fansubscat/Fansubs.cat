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
	'ACP_MEDIA_SETTINGS'				=> 'Ajustes de Media Embed',
	'ACP_MEDIA_SETTINGS_EXPLAIN'		=> 'Aquí puedes configurar los ajustes del PlugIn Media Embed.',
	'ACP_MEDIA_BBCODE_LEGEND'			=> 'BBCode',
	'ACP_MEDIA_DISPLAY_BBCODE'			=> 'Mostrar BBCode <samp>[MEDIA]</samp> en la página de publicación',
	'ACP_MEDIA_DISPLAY_BBCODE_EXPLAIN'	=> 'Si se deshabilita, el botón BBCode no será mostrado, pero los usuarios aún podrán seguir usando la etiqueta <samp>[media]</samp> en sus mensajes',
	'ACP_MEDIA_OPTIONS_LEGEND'			=> 'Opciones',
	'ACP_MEDIA_ALLOW_SIG'				=> 'Permitir en firmas de usuario',
	'ACP_MEDIA_ALLOW_SIG_EXPLAIN'		=> 'Permitir que las firmas de usuario muestren contenido multimedia incorporado.',
	'ACP_MEDIA_CACHE_LEGEND'			=> 'Contenido caché',
	'ACP_MEDIA_ENABLE_CACHE'			=> 'Habilitar caché de Media Embed',
	'ACP_MEDIA_ENABLE_CACHE_EXPLAIN'	=> 'En algunos casos, puedes notar un rendimiento más lento de lo normal al cargar medios de otros sitios, especialmente al cargar el mismo contenido varias veces (por ejemplo, al editar un mensaje). Habilitar esto almacenará en caché la información que Media Embed recopila de los sitios localmente y debería mejorar el rendimiento.',
	'ACP_MEDIA_PARSE_URLS'				=> 'Convertir URLs simples',
	'ACP_MEDIA_PARSE_URLS_EXPLAIN'		=> 'Habilita esto para convertir URLs simples (no envueltas en etiquetas <samp>[media]</samp> o <samp>[url]</samp>) en contenido multimedia incrustado. Ten en cuenta que cambiar esta configuración solo afectará a los nuevos mensajes, ya que los mensajes existentes ya se han analizado.',
	'ACP_MEDIA_WIDTH_LEGEND'			=> 'Tamaño del contenido',
	'ACP_MEDIA_FULL_WIDTH'				=> 'Habilitar contenido de ancho completo',
	'ACP_MEDIA_FULL_WIDTH_EXPLAIN'		=> 'Habilita esto para expandir la mayoría del contenido de Media Embed para llenar todo el ancho del área de contenido del mensaje mientras mantiene su relación de aspecto nativa.',
	'ACP_MEDIA_MAX_WIDTH'				=> 'Contenido de ancho máximo personalizado',
	'ACP_MEDIA_MAX_WIDTH_EXPLAIN'		=> 'Utiliza este campo para definir valores personalizados de ancho máximo para sitios individuales. Esto anulará el tamaño predeterminado y la opción de ancho completo anterior. Ingresa cada sitio en una nueva línea, usando el formato <samp class="error">IDSitio:anchura</samp> con <samp class="error">px</samp> o <samp class="error">%</samp>. Por ejemplo:<br><br><samp class="error">youtube:80%</samp><br><samp class="error">funnyordie:480px</samp><br><br><i><strong class="error">Sugerencia:</strong> Pasa el ratón sobre un sitio en la página Administrar sitios para revelar el ID del sitio que se usarás aquí.</i>',
	'ACP_MEDIA_PURGE_CACHE'				=> 'Purgar caché de Media Embed',
	'ACP_MEDIA_PURGE_CACHE_EXPLAIN'		=> 'El caché de Media Embed se purga automáticamente una vez al día, sin embargo, este botón se puede usar para purgar manualmente tu caché ahora.',
	'ACP_MEDIA_SITE_TITLE'				=> 'ID del sitio: %s',
	'ACP_MEDIA_SITE_DISABLED'			=> 'Este sitio está en conflicto con un BBCode existente: [%s]',
	'ACP_MEDIA_ERROR_MSG'				=> 'Los siguientes errores fueron encontrados:<br><br>%s',
	'ACP_MEDIA_INVALID_SITE'			=> '%1$s:%2$s :: “%1$s” no es un ID de sitio válido',
	'ACP_MEDIA_INVALID_WIDTH'			=> '%1$s:%2$s :: “%2$s” no es un ancho válido en “px” o “%%”',

	// Manage sites
	'ACP_MEDIA_MANAGE'					=> 'Gestionar sitios de Media Embed',
	'ACP_MEDIA_MANAGE_EXPLAIN'			=> 'Aquí puedes gestionar los sitios que deseas permitir en el PlugIn Media Embed, y mostrar su contenido.',
	'ACP_MEDIA_SITES_ERROR'				=> 'No hay sitios para mostrar.',
	'ACP_MEDIA_SITES_MISSING'			=> 'Estos sitios ya no son compatibles o funcionan. Por favor envíe este formulario para eliminarlos.',
]);

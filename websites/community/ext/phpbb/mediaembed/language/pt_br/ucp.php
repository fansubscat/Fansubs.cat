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
		<h3>Conteúdo incorporado de outros sites</h3>
		“%1$s” pode incluir publicações ou conteúdo com material incorporado de sites externos, incluindo YouTube, Facebook, Twitter e plataformas semelhantes. O conteúdo incorporado desses sites externos se comporta da mesma forma que se você tivesse visitado diretamente o site de origem.
		<br><br>Esses sites externos podem coletar dados sobre você, usar cookies, incorporar rastreamento adicional de terceiros e monitorar sua interação com o conteúdo incorporado, inclusive se você tiver uma conta e estiver conectado ao site.
		<br><br>Observe que essa atividade está fora do controle de “%1$s” e é regida pelas políticas de privacidade e pelos termos de serviço dos respectivos sites externos. Recomendamos que você consulte as políticas de privacidade e de cookies de todos os serviços de terceiros com os quais interage por meio de conteúdo incorporado.
	',
]);

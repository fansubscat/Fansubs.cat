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
		“%1$s” pode incluir mensagens ou conteúdo com material incorporado de sites externos, incluindo YouTube, Facebook, Twitter e plataformas semelhantes. O conteúdo incorporado destes sites externos comporta-se da mesma forma que se tivesse visitado diretamente o site de origem.
		<br><br>Estes sites externos podem recolher dados sobre si, utilizar cookies, incorporar rastreio adicional de terceiros e monitorizar a sua interação com o conteúdo incorporado, inclusive se tiver uma conta e tiver iniciado sessão nesse site.
		<br><br>Tenha em atenção que esta atividade está fora do controlo de “%1$s” e é regida pelas políticas de privacidade e pelos termos de serviço dos respetivos sites externos. Recomendamos que consulte as políticas de privacidade e de cookies de todos os serviços de terceiros com os quais interage através de conteúdo incorporado.
	',
]);

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
		<h3>Contenido incrustado de otros sitios web</h3>
		“%1$s” puede incluir mensajes o contenido con material incrustado de sitios web externos, como YouTube, Facebook, Twitter y plataformas similares, entre otros. El contenido incrustado de estos sitios externos se comporta igual que si hubieras visitado directamente el sitio web de origen.
		<br><br>Estos sitios web externos pueden recopilar datos sobre ti, usar cookies, incorporar seguimiento adicional de terceros y supervisar tu interacción con el contenido incrustado, incluso si tienes una cuenta y has iniciado sesión en dicho sitio web.
		<br><br>Ten en cuenta que dicha actividad escapa al control de “%1$s” y se rige por las políticas de privacidad y las condiciones de servicio de los respectivos sitios web externos. Te recomendamos revisar las políticas de privacidad y cookies de cualquier servicio de terceros con el que interactúes mediante contenido incrustado.
	',
]);

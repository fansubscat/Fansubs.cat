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
		<h3>Diğer Web Sitelerinden Gömülü İçerik</h3>
		“%1$s”; YouTube, Facebook, Twitter ve benzeri platformlar dâhil olmak üzere haricî web sitelerinden gömülü materyal içeren mesajlar veya içerikler barındırabilir. Bu haricî sitelerden gömülen içerik, kaynak web sitesini doğrudan ziyaret etmişsiniz gibi çalışır.
		<br><br>Bu haricî web siteleri hakkınızda veri toplayabilir, çerez kullanabilir, ek üçüncü taraf takibi yerleştirebilir ve gömülü içerikle etkileşiminizi izleyebilir. İlgili web sitesinde hesabınız varsa ve oturum açtıysanız etkileşimleriniz de takip edilebilir.
		<br><br>Bu tür faaliyetlerin “%1$s” denetimi dışında olduğunu ve ilgili haricî web sitelerinin gizlilik politikaları ile hizmet koşullarına tabi olduğunu unutmayın. Gömülü içerik aracılığıyla etkileşim kurduğunuz tüm üçüncü taraf hizmetlerinin gizlilik ve çerez politikalarını incelemenizi öneririz.
	',
]);

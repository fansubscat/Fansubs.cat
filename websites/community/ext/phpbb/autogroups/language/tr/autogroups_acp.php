<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_AUTOGROUPS_MANAGE'			=> 'Otomatik grupları yönet',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Bu formu kullanarak Otomatik Grup ayarı ekleyebilir, düzenleyebilir, görebilir ve silebilirsiniz.',
	'ACP_AUTOGROUPS_ADD'			=> 'Otomatik Grup ekle',
	'ACP_AUTOGROUPS_EDIT'			=> 'Otomatik Grup düzenle',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grup',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Kullanıcıların otomatik olarak ekleneceği/silineceği bir grup seçin.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Otomatik Grup tipi',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Kullanıcıların bu gruba ekleneceği veya kaldırılacağı durum türünü seçin.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Minimum değer',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Eğer minimum değeri aşarlarsa kullanıcılar bu gruba eklenecek.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Maksimum değer',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Eğer maksimum değeri aşarlarsa kullanıcılar bu gruptan kaldırılacak. Eğer kullanıcıların kaldırılmasını istemiyorsanız bu alanı boş bırakın.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Grup varsayılanlarını ayarla',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Bunu kullanıcıların yeni varsayılan grubu yap.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Bu, varsayılan kullanıcı grubu aşağıdakilerden biri olan kullanıcıları etkilemeyecektir: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Kullanıcıları bilgilendir',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Kullanıcılar Otomatik olarak bu gruba eklendiğinde veya kaldırıldığında bir bildirim gönder.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Hariç tutulan gruplar',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Bu grupların üyelerini hariç tut',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Bu listede seçili <em>herhangi bir grubun</em> üyesi yok sayılacak. Bu Otomatik Grubun panonuzun <em>tüm üyelerine</em> uygulanmasını istiyorsanız bu alanı boş bırakın. <samp>CTRL</samp> (veya Mac\'te <samp>&#8984;CMD</samp>) tuşunu basılı tutarak ve grupları seçerek birden fazla grup seçin.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'Bir hata meydana geldi. Hariç tutulan gruplar alanında da bu koşul için grup seçilemez.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Grup varsayılan muafiyetlerini ayarla',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Eğer o bu listede seçili ise Otomatik Gruplar bir kullanıcının varsayılan grubunu değiştirmeyecek. Çoklu grup seçmek için <samp>CTRL</samp> (veya MAClerde <samp>&#8984;CMD</samp>) tuşuna basın ve grupları seçin.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Yeni Otomatik Grup oluştur',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Otomarik Grup başarıyla konfigüre edildi.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Bu Otomatik Grup konfigürasyonunu silmek istediğinize emin misiniz?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Otomatik Grup başarıyla silindi.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Hiç Otomatik Grup yok.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Grup mevcut değil',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Bir hata oluştu. Geçerli bir kullanıcı grubu seçilmedi.<br />Otomatik Gruplar yalnızca Grup Yönetimi sayfasında oluşturulabilen kullanıcı tanımlı gruplarla kullanılabilir.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Bir hata oluştu. Minimum ve maksimum değerler aynı değer olarak ayarlanamaz.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Kullanıcı yaşı',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Son ziyaretten sonra geçen gün',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Üyelik Günü',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Gönderi',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Uyarı',
));

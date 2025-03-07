<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Spanish translation by ThE KuKa (www.phpbb-es.com)
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Gestionar Auto Grupos',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Usando este formulario puede añadir, editar, ver y borrar configuraciones de Auto Grupos.',
	'ACP_AUTOGROUPS_ADD'			=> 'Añadir Auto Grupo',
	'ACP_AUTOGROUPS_EDIT'			=> 'Editar Auto Grupo',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grupo',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Elija un grupo para agregar/eliminar automáticamente usuarios.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Tipo de Auto Grupo',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Elija el tipo de condición en la que se pueden agregar o quitar de este grupo de usuarios.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Valor mínimo',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Los usuarios serán añadidos a este grupo si superan el valor mínimo.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Valor máximo',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Los usuarios serán eliminados de este grupo si superan el valor máximo. Deje este campo vacío si no desea que los usuarios sean eliminados.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Establecer grupo por defecto',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Hacer este nuevo grupo predeterminado del usuario.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Esto no afectará a los usuarios cuyo grupo de usuarios predeterminado sea uno de los siguientes: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notificar a usuarios',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Enviar una notificación a los usuarios después de ser añadido o eliminado automáticamente de este grupo.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Establecer exenciones predeterminadas del grupo',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'Auto Grupos no cambiará de grupo predeterminado de un usuario si se ha seleccionado en esta lista. Seleccione varios grupos mediante la pulsación de <samp>CTRL</samp> (o <samp>&#8984;CMD</samp> en Mac) y seleccione los grupos.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Crear nuevo Auto Grupo',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Auto Grupo configurado correctamente.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> '¿Está seguro de querer borrar esta configuración de Auto Grupos?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Auto Grupo borrado correctamente.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'No hay Auto Grupos.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'No hay grupos disponibles',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Ocurrio un error. Un grupo de usuario válido no fue seleccionado.<br />Auto Grupos sólo se puede utilizar con los grupos definidos por el usuario, estos se pueden crear en la página Administrar grupos.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Ocurrio un error. Los valores mínimos y máximos no se pueden establecer con el mismo valor.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Edad del usuario',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Días desde la última visita',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Días como miembro',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Mensajes',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Advertencias',
));

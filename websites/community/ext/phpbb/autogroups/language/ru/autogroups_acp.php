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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Управление АвтоГруппами',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Используя эту форму, вы можете создавать, редактировать, просматривать и удалять конфигурации АвтоГрупп.',
	'ACP_AUTOGROUPS_ADD'			=> 'Добавить АвтоГруппу',
	'ACP_AUTOGROUPS_EDIT'			=> 'Редактировать АвтоГруппу',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Группа',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Выберите группу для автоматического добавления/удаления пользователей в/из нее.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Тип АвтоГруппы',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Выберите условие, по которому пользователи могут быть добавлены или исключены из этой группы.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Минимальное значение',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Пользователи будут добавлены в группу, если достигнут минимального значения.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Максимальное значение',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Пользователи будут исключены из этой группы, если превысят максимальное значение. Оставьте это поле пустым, если не хотите, чтобы участники были исключены из группы.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Сделать группой по-умолчанию',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Сделать выбранную группу группой пользователя по умолчанию.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Это не затронет пользователей, чья группа по умолчанию одна из следующих: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Уведомить пользователя',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Отправить уведомление пользователю после автоматического добавления/удаления в группу.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Установить исключения для групп по умолчанию',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'АвтоГруппы не изменят группу пользователя по умолчанию, если она отмечена в этом списке. Выберите несколько групп с нажатой клавишей <samp>CTRL</samp> (или <samp>&#8984;CMD</samp> на Mac).',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Создать новую АвтоГруппу',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'АвтоГруппа успешно сконфигурирована.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Вы уверенны, что хотите удалить конфигурацию этой АвтоГруппы?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'АвтоГруппа успешно удалена.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Нет АвтоГрупп.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Нет доступных групп',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Произошла ошибка. Не выбрана действующая группа пользователей.<br />АвтоГруппы могут использоваться только с определенными группами пользователей, создать которые можно на странице управления группами.',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Произошла ошибка. Минимальное и максимальное значения не могут быть установлены на одно и то же значение.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Возраст участника',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Дни после последнего посещения',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Количество дней регистрации',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Сообщений',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Предупреждений',
));

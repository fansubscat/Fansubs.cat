<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
* Brazilian Portuguese translation by MarcosFPo and update by eunaumtenhoid (c) 2017 [ver 2.0.1] (https://github.com/phpBBTraducoes)
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
	'ACP_AUTOGROUPS_MANAGE'			=> 'Gerenciar Autogrupos',
	'ACP_AUTOGROUPS_MANAGE_EXPLAIN'	=> 'Usando este formulário, você pode adicionar, editar, visualizar e excluir configurações de Autogrupos.',
	'ACP_AUTOGROUPS_ADD'			=> 'Adicionar Autogrupo',
	'ACP_AUTOGROUPS_EDIT'			=> 'Editar Autogrupo',

	'ACP_AUTOGROUPS_GROUP_NAME'				=> 'Grupo',
	'ACP_AUTOGROUPS_GROUP_NAME_EXPLAIN'		=> 'Escolha um grupo para nele automaticamente adicionar/remover usuários.',
	'ACP_AUTOGROUPS_CONDITION_NAME'			=> 'Tipo de Autogrupo',
	'ACP_AUTOGROUPS_CONDITION_NAME_EXPLAIN'	=> 'Escolha o tipo de condição que fará cada usuário ser adicionado ou removido do grupo.',
	'ACP_AUTOGROUPS_MIN_VALUE'				=> 'Valor mínimo',
	'ACP_AUTOGROUPS_MIN_VALUE_EXPLAIN'		=> 'Usuários serão adicionados para este grupo, se eles excederam ao valor mínimo.',
	'ACP_AUTOGROUPS_MAX_VALUE'				=> 'Valor máximo',
	'ACP_AUTOGROUPS_MAX_VALUE_EXPLAIN'		=> 'Usuários serão removidos deste grupo, se eles excederam ao valor máximo. Deixe vazio, se não deseja que os usuários sejam removidos do grupo.',
	'ACP_AUTOGROUPS_DEFAULT'				=> 'Configure o grupo padrão',
	'ACP_AUTOGROUPS_DEFAULT_EXPLAIN'		=> 'Transforme o grupo em novo grupo padrão do usuário.',
	'ACP_AUTOGROUPS_DEFAULT_EXEMPTION'		=> 'Isso não afetará os usuários cujo grupo de usuários padrão é um dos seguintes: %s.',
	'ACP_AUTOGROUPS_NOTIFY'					=> 'Notificar o usuário',
	'ACP_AUTOGROUPS_NOTIFY_EXPLAIN'			=> 'Enviar uma notificação para usuários depois que foram automaticamente adicionados ou removidos deste grupo.',

	'ACP_AUTOGROUPS_EXCLUDED_GROUPS'		=> 'Excluded groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP'			=> 'Exclude members of these groups',
	'ACP_AUTOGROUPS_EXCLUDE_GROUP_EXPLAIN'	=> 'Members belonging to <em>any group</em> selected in this list will be ignored. Leave this field blank if you want this Auto Group applied to <em>all members</em> of your board. Select multiple groups by holding <samp>CTRL</samp> (or <samp>&#8984;CMD</samp> on Mac) and selecting the groups.',
	'ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS'	=> 'An error occurred. The group for this condition can not also be selected in the excluded groups field.',
	'ACP_AUTOGROUPS_EXEMPT_GROUP'			=> 'Configure as exceções para o grupo padrão',
	'ACP_AUTOGROUPS_EXEMPT_GROUP_EXPLAIN'	=> 'O grupo padrão do usuário não será automaticamente alterado se estiver selecionado nesta lista. Selecione diversos grupos segurando <samp>CTRL</samp> (ou <samp>&#8984;CMD</samp> no Mac) e clicando sobre o grupo.',

	'ACP_AUTOGROUPS_CREATE_RULE'	=> 'Criar novo Autogrupo',
	'ACP_AUTOGROUPS_SUBMIT_SUCCESS'	=> 'Autogrupo configurado com sucesso.',
	'ACP_AUTOGROUPS_DELETE_CONFIRM'	=> 'Você deseja excluir a configuração deste Autogrupo?',
	'ACP_AUTOGROUPS_DELETE_SUCCESS'	=> 'Autogrupo excluído com sucesso.',
	'ACP_AUTOGROUPS_EMPTY'			=> 'Não existem Autogrupos.',
	'ACP_AUTOGROUPS_NO_GROUPS'		=> 'Não existem grupos disponíveis',
	'ACP_AUTOGROUPS_INVALID_GROUPS'	=> 'Um erro ocorreu. Um grupo de usuários válido não estava selecionado. <br /> Autogrupos somente pode ser usado com grupos definidos de usuários, que podem ser criados em na página "Gerenciar grupos".',
	'ACP_AUTOGROUPS_INVALID_RANGE'	=> 'Um erro ocorreu. Os valores mínimos e máximos não podem ser configurados para o mesmo valor.',

	// Conditions
	'AUTOGROUPS_TYPE_BIRTHDAYS'		=> 'Idade do usuário',
	'AUTOGROUPS_TYPE_LASTVISIT'		=> 'Dias desde a última visita',
	'AUTOGROUPS_TYPE_MEMBERSHIP'	=> 'Dias que o usuário é membro',
	'AUTOGROUPS_TYPE_POSTS'			=> 'Posts',
	'AUTOGROUPS_TYPE_WARNINGS'		=> 'Advertência',
));

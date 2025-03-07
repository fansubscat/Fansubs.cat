<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\controller;

/**
 * Admin controller
 */
class admin_controller implements admin_interface
{
	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\group\helper */
	protected $group_helper;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\log\log */
	protected $log;

	/** @var \phpbb\autogroups\conditions\manager */
	protected $manager;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string The database table the auto group rules are stored in */
	protected $autogroups_rules_table;

	/** @var string The database table the auto group types are stored in */
	protected $autogroups_types_table;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor
	 *
	 * @param \phpbb\cache\driver\driver_interface $cache                    Cache driver interface
	 * @param \phpbb\db\driver\driver_interface    $db                       Database object
	 * @param \phpbb\group\helper                  $group_helper             Group helper object
	 * @param \phpbb\language\language             $language                 Language object
	 * @param \phpbb\log\log                       $log                      The phpBB log system
	 * @param \phpbb\autogroups\conditions\manager $manager                  Auto groups condition manager object
	 * @param \phpbb\request\request               $request                  Request object
	 * @param \phpbb\template\template             $template                 Template object
	 * @param \phpbb\user                          $user                     User object
	 * @param string                               $autogroups_rules_table   Name of the table used to store auto group rules data
	 * @param string                               $autogroups_types_table   Name of the table used to store auto group types data
	 * @access public
	 */
	public function __construct(\phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\group\helper $group_helper, \phpbb\language\language $language, \phpbb\log\log $log, \phpbb\autogroups\conditions\manager $manager, \phpbb\request\request $request, \phpbb\template\template $template, \phpbb\user $user, $autogroups_rules_table, $autogroups_types_table)
	{
		$this->cache = $cache;
		$this->db = $db;
		$this->group_helper = $group_helper;
		$this->language = $language;
		$this->log = $log;
		$this->manager = $manager;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->autogroups_rules_table = $autogroups_rules_table;
		$this->autogroups_types_table = $autogroups_types_table;
	}

	/**
	 * {@inheritdoc}
	 */
	public function display_autogroups()
	{
		// Get all auto groups data from the database
		$autogroup_rows = $this->get_all_autogroups();

		// Process all auto groups data for display in the template
		foreach ($autogroup_rows as $row)
		{
			$this->template->assign_block_vars('autogroups', array(
				'GROUP_NAME'		=> $row['group_name'],
				'CONDITION_NAME'	=> $this->manager->get_condition_lang($row['autogroups_type_name']),
				'MIN_VALUE'			=> $row['autogroups_min_value'],
				'MAX_VALUE'			=> $row['autogroups_max_value'],

				'S_DEFAULT'	=> $row['autogroups_default'],
				'S_NOTIFY'	=> $row['autogroups_notify'],

				'EXCLUDED_GROUPS'	=> implode('<br>', $this->get_excluded_groups($row['autogroups_excluded_groups'])),

				'U_EDIT'	=> "{$this->u_action}&amp;action=edit&amp;autogroups_id=" . $row['autogroups_id'],
				'U_DELETE'	=> "{$this->u_action}&amp;action=delete&amp;autogroups_id=" . $row['autogroups_id'],
				'U_SYNC'	=> "{$this->u_action}&amp;action=sync&amp;autogroups_id=" . $row['autogroups_id'] . '&amp;hash=' . generate_link_hash('sync' . $row['autogroups_id']),
			));
		}

		$this->template->assign_vars(array(
			'U_ACTION'				=> $this->u_action,
			'U_ADD_AUTOGROUP_RULE'	=> "{$this->u_action}&amp;action=add",
		));

		// Display the group exemption select box
		$exempt_groups = $this->get_exempt_groups();
		$this->build_groups_menu(array_keys($exempt_groups));
	}

	/**
	 * {@inheritdoc}
	 */
	public function save_autogroup_rule($autogroups_id = 0)
	{
		// Process auto group form data if form was submitted
		if ($this->request->is_set_post('submit'))
		{
			$this->submit_autogroup_rule($autogroups_id);
		}

		// Get data for the auto group so we can display it
		$autogroups_data = $this->get_autogroup($autogroups_id);

		// If we have no auto group data yet, zero out all default values
		if (empty($autogroups_data))
		{
			$autogroups_data = array_fill_keys([
				'autogroups_group_id',
				'autogroups_type_id',
				'autogroups_min_value',
				'autogroups_max_value',
				'autogroups_default',
				'autogroups_notify',
			], 0);
		}

		// Format autogroups_excluded_groups specifically to be an array type
		$autogroups_data['autogroups_excluded_groups'] = !empty($autogroups_data['autogroups_excluded_groups']) ? json_decode($autogroups_data['autogroups_excluded_groups'], true) : array();

		// Process the auto group data for display in the template
		$this->build_groups_menu($autogroups_data['autogroups_excluded_groups'], false, 'excluded_groups');
		$this->build_groups_menu(array($autogroups_data['autogroups_group_id']), true);
		$this->build_conditions_menu($autogroups_data['autogroups_type_id']);
		$this->template->assign_vars(array(
			'S_ADD'			=> (bool) !$autogroups_id,
			'S_EDIT'		=> (bool) $autogroups_id,

			'MIN_VALUE'		=> (int) $autogroups_data['autogroups_min_value'],
			'MAX_VALUE'		=> (int) $autogroups_data['autogroups_max_value'],

			'S_DEFAULT'		=> (bool) $autogroups_data['autogroups_default'],
			'S_NOTIFY'		=> (bool) $autogroups_data['autogroups_notify'],

			'EXEMPT_GROUPS'	=> implode(', ', $this->get_exempt_groups()),

			'U_FORM_ACTION'	=> $this->u_action . '&amp;action=' . ($autogroups_id ? 'edit' : 'add') . '&amp;autogroups_id=' . $autogroups_id,
			'U_ACTION'		=> $this->u_action,
			'U_BACK'		=> $this->u_action,
		));
	}

	/**
	 * {@inheritdoc}
	 */
	public function delete_autogroup_rule($autogroups_id)
	{
		// Delete and auto group rule
		$sql = 'DELETE FROM ' . $this->autogroups_rules_table . '
			WHERE autogroups_id = ' . (int) $autogroups_id;
		$this->db->sql_query($sql);

		// Log the action
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_AUTOGROUPS_DELETE_LOG', time());

		// If AJAX was used, show user a result message
		if ($this->request->is_ajax())
		{
			$json_response = new \phpbb\json_response;
			$json_response->send(array(
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('ACP_AUTOGROUPS_DELETE_SUCCESS'),
				'REFRESH_DATA'	=> array(
					'time'	=> 3
				)
			));
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function resync_autogroup_rule($autogroups_id)
	{
		// If the link hash is invalid, stop and show an error message to the user
		if (!check_link_hash($this->request->variable('hash', ''), 'sync' . $autogroups_id))
		{
			trigger_error($this->language->lang('FORM_INVALID') . $this->get_back_link(), E_USER_WARNING);
		}

		try
		{
			$this->manager->sync_autogroups($autogroups_id);
		}
		catch (\Exception $e)
		{
			trigger_error($e->getMessage() . $this->get_back_link(), E_USER_WARNING);
		}
	}

	/**
	 * {@inheritdoc}
	 */
	public function submit_autogroups_options()
	{
		// Get data from the form
		$group_ids = $this->request->variable('group_ids', array(0));

		// Use a confirmation box routine before setting the data
		if (confirm_box(true))
		{
			// Set selected groups to true, unselected to false
			$this->set_exempt_groups($group_ids);
			$this->cache->destroy('sql', GROUPS_TABLE);
		}
		else
		{
			confirm_box(false, $this->language->lang('CONFIRM_OPERATION'), build_hidden_fields(array(
				'generalsubmit' => true,
				'group_ids' => $group_ids,
			)));
		}
	}

	/**
	 * Submit auto group rule form data
	 *
	 * @param int $autogroups_id An auto group identifier
	 *                           A value of 0 is new, otherwise we're updating
	 * @return void
	 * @access protected
	 */
	protected function submit_autogroup_rule($autogroups_id = 0)
	{
		$data = array(
			'autogroups_type_id'	=> $this->request->variable('autogroups_type_id', 0),
			'autogroups_min_value'	=> $this->request->variable('autogroups_min_value', 0),
			'autogroups_max_value'	=> $this->request->variable('autogroups_max_value', 0),
			'autogroups_group_id'	=> $this->request->variable('autogroups_group_id', 0),
			'autogroups_default'	=> $this->request->variable('autogroups_default', false),
			'autogroups_notify'		=> $this->request->variable('autogroups_notify', false),
			'autogroups_excluded_groups' => $this->request->variable('autogroups_excluded_groups', array(0)),
		);

		// Prevent form submit when no user groups are available or selected
		if (!$data['autogroups_group_id'])
		{
			trigger_error($this->language->lang('ACP_AUTOGROUPS_INVALID_GROUPS') . $this->get_back_link($autogroups_id), E_USER_WARNING);
		}

		// Prevent form submit when min and max values are identical
		if ($data['autogroups_min_value'] == $data['autogroups_max_value'])
		{
			trigger_error($this->language->lang('ACP_AUTOGROUPS_INVALID_RANGE') . $this->get_back_link($autogroups_id), E_USER_WARNING);
		}

		// Prevent form submit when the target group is also in the excluded groups array
		if (in_array($data['autogroups_group_id'], $data['autogroups_excluded_groups']))
		{
			trigger_error($this->language->lang('ACP_AUTOGROUPS_INVALID_EXCLUDE_GROUPS') . $this->get_back_link($autogroups_id), E_USER_WARNING);
		}

		// Format autogroups_excluded_groups for storage in the db
		$data['autogroups_excluded_groups'] = !empty($data['autogroups_excluded_groups']) ? json_encode($data['autogroups_excluded_groups']) : '';

		if ($autogroups_id != 0) // Update existing auto group data
		{
			$sql = 'UPDATE ' . $this->autogroups_rules_table . '
				SET ' . $this->db->sql_build_array('UPDATE', $data) . '
				WHERE autogroups_id = ' . (int) $autogroups_id;
			$this->db->sql_query($sql);
		}
		else // Insert new auto group data
		{
			$sql = 'INSERT INTO ' . $this->autogroups_rules_table . ' ' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
			$autogroups_id = (int) $this->db->sql_nextid();
		}

		// Apply the auto group to all users
		$this->manager->sync_autogroups($autogroups_id);

		// Log the action
		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'ACP_AUTOGROUPS_SAVED_LOG', time());

		// Output message to user after submitting the form
		trigger_error($this->language->lang('ACP_AUTOGROUPS_SUBMIT_SUCCESS') . $this->get_back_link());
	}

	/**
	 * Get one auto group rule from the database
	 *
	 * @param int $id An auto group rule identifier
	 * @return array An auto group rule and it's associated data
	 * @access public
	 */
	protected function get_autogroup($id)
	{
		$sql = 'SELECT *
			FROM ' . $this->autogroups_rules_table . '
			WHERE autogroups_id = ' . (int) $id;
		$result = $this->db->sql_query($sql);
		$autogroups_data = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $autogroups_data ?: [];
	}

	/**
	 * Get all auto group rules from the database
	 *
	 * @return array Array of auto group rules and their associated data
	 * @access public
	 */
	protected function get_all_autogroups()
	{
		$sql_array = array(
			'SELECT'	=> 'agr.*, agt.autogroups_type_name, g.group_name',
			'FROM'	=> array(
				$this->autogroups_rules_table => 'agr',
				$this->autogroups_types_table => 'agt',
				GROUPS_TABLE => 'g',
			),
			'WHERE'	=> 'agr.autogroups_type_id = agt.autogroups_type_id
				AND agr.autogroups_group_id = g.group_id',
			'ORDER_BY'	=> 'g.group_name ASC, autogroups_min_value ASC',
		);
		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $rows;
	}

	/**
	 * Set the user groups marked as exempt from default switching.
	 * Sets the 'autogroup_default_exempt' field for all groups in
	 * $group_ids to true, while all other groups are set to false.
	 *
	 * @param array $group_ids An array of group ids
	 * @param bool  $flag      True or false
	 */
	protected function set_exempt_groups($group_ids, $flag = true)
	{
		$sql = 'UPDATE ' . GROUPS_TABLE . '
			SET autogroup_default_exempt = ' . (int) $flag . '
			WHERE ' . $this->db->sql_in_set('group_id', $group_ids, !$flag, true);
		$this->db->sql_query($sql);

		// Recursively recall this function with false, to set all other groups to false
		if ($flag !== false)
		{
			$this->set_exempt_groups($group_ids, false);
		}
	}

	/**
	 * Get an array of user groups marked as exempt from default switching
	 *
	 * @return array An array of exempted groups: array('group_id' => 'group_name')
	 * @access protected
	 */
	protected function get_exempt_groups()
	{
		$groups = array();

		foreach ($this->query_groups('autogroup_default_exempt = 1') as $row)
		{
			$groups[$row['group_id']] = $this->group_helper->get_name_string('full', $row['group_id'], $row['group_name'], $row['group_colour']);
		}

		return $groups;
	}

	/**
	 * Get an array of user groups marked as excluded from auto grouping
	 *
	 * @param string $excluded_groups A json encoded string of an array of group ids
	 * @return array An array of groups: array('group_id' => 'group_name')
	 * @access protected
	 */
	protected function get_excluded_groups($excluded_groups)
	{
		$groups = array();

		if (!empty($excluded_groups))
		{
			$excluded_groups = json_decode($excluded_groups, true);

			foreach ($this->query_groups() as $row)
			{
				if (in_array($row['group_id'], $excluded_groups))
				{
					$groups[$row['group_id']] = $this->group_helper->get_name_string('full', $row['group_id'], $row['group_name'], $row['group_colour']);
				}
			}
		}

		return $groups;
	}

	/**
	 * Build template vars for a select menu of user groups
	 *
	 * @param array  $selected                  An array of identifiers for selected group(s)
	 * @param bool   $exclude_predefined_groups Exclude GROUP_SPECIAL
	 * @param string $block                     Name of the template block vars array
	 * @return void
	 * @access protected
	 */
	protected function build_groups_menu($selected, $exclude_predefined_groups = false, $block = 'groups')
	{
		foreach ($this->query_groups() as $group)
		{
			if ($exclude_predefined_groups && $group['group_type'] == GROUP_SPECIAL)
			{
				continue;
			}
			$this->template->assign_block_vars($block, array(
				'GROUP_ID'		=> $group['group_id'],
				'GROUP_NAME'	=> $this->group_helper->get_name($group['group_name']),

				'S_SELECTED'	=> in_array($group['group_id'], $selected),
			));
		}
	}

	/**
	 * Build template vars for a select menu of auto group conditions
	 *
	 * @param int $selected An identifier for the selected group
	 * @return void
	 * @access protected
	 */
	protected function build_conditions_menu($selected)
	{
		$conditions = $this->manager->get_autogroups_type_ids();

		foreach ($conditions as $condition_name => $condition_id)
		{
			$this->template->assign_block_vars('conditions', array(
				'CONDITION_ID'		=> $condition_id,
				'CONDITION_NAME'	=> $this->manager->get_condition_lang($condition_name),

				'S_SELECTED'		=> $condition_id == $selected,
			));
		}
	}

	/**
	 * Get group data, always excluding BOTS, Guests
	 *
	 * @param string $where_sql Optional additional SQL where conditions
	 * @return array An array of group data rows (group_id, group_name, group_type)
	 */
	protected function query_groups($where_sql = '')
	{
		$sql = 'SELECT group_id, group_name, group_type, group_colour
			FROM ' . GROUPS_TABLE . '
			WHERE ' . $this->db->sql_in_set('group_name', array('BOTS', 'GUESTS'), true, true) .
				($where_sql ? ' AND ' . $this->db->sql_escape($where_sql) : '') . '
			ORDER BY group_name';
		$result = $this->db->sql_query($sql, 3600);
		$groups = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		return $groups ?: array();
	}

	/**
	 * Return the ACP action back link. For editing an auto group it will take you
	 * back to that auto group's edit page. Otherwise it will take you back to main
	 * auto groups ACP page.
	 *
	 * @param int $autogroups_id
	 * @return string
	 */
	protected function get_back_link($autogroups_id = 0)
	{
		$back_link = $autogroups_id ? '&amp;action=edit&amp;autogroups_id=' . $autogroups_id : '';

		return adm_back_link($this->u_action . $back_link);
	}

	/**
	 * {@inheritdoc}
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}
}

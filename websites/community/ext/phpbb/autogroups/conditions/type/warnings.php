<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\conditions\type;

/**
 * Auto Groups Warnings class
 */
class warnings extends \phpbb\autogroups\conditions\type\base
{
	/**
	 * Get condition type
	 *
	 * @return string Condition type
	 * @access public
	 */
	public function get_condition_type()
	{
		return 'phpbb.autogroups.type.warnings';
	}

	/**
	 * Get condition field (this is the field to check)
	 *
	 * @return string Condition field name
	 * @access public
	 */
	public function get_condition_field()
	{
		return 'user_warnings';
	}

	/**
	 * Get condition type name
	 *
	 * @return string Condition type name
	 * @access public
	 */
	public function get_condition_type_name()
	{
		return $this->language->lang('AUTOGROUPS_TYPE_WARNINGS');
	}

	/**
	 * Get users to apply to this condition
	 * Warnings is called by cron or by events when warnings are issued
	 * to user(s). By default, get users that have between the min/max
	 * values assigned to this type and any users currently in groups
	 * assigned to this type, otherwise use the user_id(s) supplied in
	 * the $options arg.
	 *
	 * @param array $options Array of optional data
	 * @return array Array of users ids as keys and their condition data as values
	 * @access public
	 */
	public function get_users_for_condition($options = array())
	{
		// The user data this condition needs to check
		$condition_data = array(
			$this->get_condition_field(),
		);

		// Merge default options, empty user array as the default
		$options = array_merge(array(
			'users'		=> array(),
		), $options);

		$sql_array = array(
			'SELECT' => 'u.user_id, u.' . implode(', u.', $condition_data),
			'FROM' => array(
				USERS_TABLE => 'u',
			),
			'LEFT_JOIN' => array(
				array(
					'FROM' => array(USER_GROUP_TABLE => 'ug'),
					'ON' => 'u.user_id = ug.user_id',
				),
			),
			'WHERE' => $this->sql_where_clause($options) . '
				AND ' . $this->db->sql_in_set('u.user_type', $this->ignore_user_types(), true),
			'GROUP_BY' => 'u.user_id, u.' . implode(', u.', $condition_data),
		);

		$sql = $this->db->sql_build_query('SELECT_DISTINCT', $sql_array);
		$result = $this->db->sql_query($sql);

		$user_data = array();
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_data[$row['user_id']] = $row;
		}
		$this->db->sql_freeresult($result);

		return $user_data;
	}

	/**
	 * Helper to generate the needed sql where clause. If user ids were
	 * supplied, use them. Otherwise find all qualified users to check.
	 *
	 * @param array $options Array of optional data
	 * @return string SQL where clause
	 * @access protected
	 */
	protected function sql_where_clause($options)
	{
		// If we have user id data, return a sql_in_set of user_ids
		if (!empty($options['users']))
		{
			return $this->db->sql_in_set('u.user_id', $this->helper->prepare_users_for_query($options['users']));
		}

		$sql_where = $group_ids = array();
		$extremes = array('min' => '>=', 'max' => '<=');

		// Get auto group rule data for this type
		$group_rules = $this->get_group_rules($this->get_condition_type());
		foreach ($group_rules as $group_rule)
		{
			$where = array();
			foreach ($extremes as $end => $sign)
			{
				if (!empty($group_rule['autogroups_' . $end . '_value']))
				{
					$where[] = "u.user_warnings $sign " . $group_rule['autogroups_' . $end . '_value'];
				}
			}

			if (count($where))
			{
				$sql_where[] = '(' . implode(' AND ', $where) . ')';
			}

			$group_ids[] = $group_rule['autogroups_group_id'];
		}

		return '(' . (count($sql_where) ? implode(' OR ', $sql_where) . ' OR ' : '') . $this->db->sql_in_set('ug.group_id', $group_ids, false, true) . ')';
	}
}

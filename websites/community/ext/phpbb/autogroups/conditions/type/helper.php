<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\conditions\type;

/**
 * Auto Groups conditions type helper class
 */
class helper
{
	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\notification\manager */
	protected $notification_manager;

	/** @var string */
	protected $phpbb_root_path;

	/** @var string */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param \phpbb\db\driver\driver_interface $db                     Database object
	 * @param \phpbb\notification\manager       $notification_manager   Notification manager
	 * @param string                            $phpbb_root_path        phpBB root path
	 * @param string                            $php_ext                phpEx
	 *
	 * @access public
	 */
	public function __construct(\phpbb\db\driver\driver_interface $db, \phpbb\notification\manager $notification_manager, $phpbb_root_path, $php_ext)
	{
		$this->db = $db;
		$this->notification_manager = $notification_manager;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Get user's group ids
	 *
	 * @param array $user_id_ary An array of user ids to check
	 * @return array An array of usergroup ids each user belongs to
	 * @access public
	 */
	public function get_users_groups($user_id_ary)
	{
		$group_id_ary = array();

		$sql = 'SELECT user_id, group_id
			FROM ' . USER_GROUP_TABLE . '
			WHERE ' . $this->db->sql_in_set('user_id', $user_id_ary, false, true);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_id_ary[$row['user_id']][] = $row['group_id'];
		}
		$this->db->sql_freeresult($result);

		return $group_id_ary;
	}

	/**
	 * Get users that should not have their default status changed
	 *
	 * @return array An array of user ids
	 * @access public
	 */
	public function get_default_exempt_users()
	{
		$user_id_ary = array();

		// Get users whose default group is autogroup_default_exempt
		$sql_array = array(
			'SELECT'	=> 'u.user_id',
			'FROM'		=> array(
				USERS_TABLE	=> 'u',
			),
			'LEFT_JOIN'	=> array(
				array(
					'FROM'	=> array(GROUPS_TABLE => 'g'),
					'ON'	=> 'g.group_id = u.group_id',
				),
			),
			'WHERE'		=> 'g.autogroup_default_exempt = 1',
		);

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$user_id_ary[] = $row['user_id'];
		}
		$this->db->sql_freeresult($result);

		return $user_id_ary;
	}

	/**
	 * Prepare user ids for querying
	 *
	 * @param mixed $user_ids User id(s) expected as int or array
	 * @return array An array of user id(s)
	 * @access public
	 */
	public function prepare_users_for_query($user_ids)
	{
		if (!is_array($user_ids))
		{
			$user_ids = array($user_ids);
		}

		// Cast each array value to integer
		return array_map('intval', $user_ids);
	}

	/**
	 * Send notifications
	 *
	 * @param string $type       Type of notification to send (group_added|group_removed)
	 * @param array $user_id_ary Array of user(s) to notify
	 * @param int $group_id      The usergroup identifier
	 * @return void
	 * @access public
	 */
	public function send_notifications($type, $user_id_ary, $group_id)
	{
		if (!function_exists('get_group_name'))
		{
			include $this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext;
		}

		$this->notification_manager->add_notifications("phpbb.autogroups.notification.type.$type", array(
			'user_ids'		=> $user_id_ary,
			'group_id'		=> $group_id,
			'group_name'	=> get_group_name($group_id),
		));
	}
}

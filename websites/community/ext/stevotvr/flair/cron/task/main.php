<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\cron\task;

use phpbb\config\config;
use phpbb\cron\task\base;
use phpbb\db\driver\driver_interface;
use phpbb\notification\manager;

/**
* Profile Flair main cron task.
*/
class main extends base
{
	/* The interval of the cron task in seconds */
	const INTERVAL = 120;

	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var manager
	 */
	protected $notification_manager;

	/**
	 * The name of the flair_notif table.
	 *
	 * @var string
	 */
	protected $notification_table;

	/**
	 * @param config           $config
	 * @param driver_interface $db
	 * @param manager          $notification_manager
	 * @param string           $notification_table   The name of the flair_notif table
	 */
	public function __construct(config $config, driver_interface $db, manager $notification_manager, $notification_table)
	{
		$this->config = $config;
		$this->db = $db;
		$this->notification_manager = $notification_manager;
		$this->notification_table = $notification_table;
	}

	/**
	 * @inheritDoc
	 */
	public function run()
	{
		$notifications = array();

		$sql = 'SELECT notification_id, user_id, flair_id, flair_name, updated
				FROM ' . $this->notification_table . '
				WHERE updated < ' . (time() - (self::INTERVAL / 2));
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$notifications[(int) $row['notification_id']] = $row;
		}
		$this->db->sql_freeresult();

		if (!empty($notifications))
		{
			$sql = 'DELETE FROM ' . $this->notification_table . '
    				WHERE ' . $this->db->sql_in_set('notification_id', array_keys($notifications));
			$this->db->sql_query($sql);

			foreach ($notifications as $notification)
			{
				$params = array(
					'notification_id'	=> $notification['notification_id'],
					'user_id'			=> $notification['user_id'],
					'flair_id'			=> $notification['flair_id'],
					'flair_name'		=> $notification['flair_name'],
				);
				$this->notification_manager->add_notifications('stevotvr.flair.notification.type.flair', $params);
			}
		}

		$this->config->set('stevotvr_flair_cron_last_run', time());
	}

	/**
	 * @inheritDoc
	 */
	public function should_run()
	{
		if (!$this->config['stevotvr_flair_notify_users'])
		{
			return false;
		}

		return (time() - (int) $this->config['stevotvr_flair_cron_last_run']) > self::INTERVAL;
	}
}

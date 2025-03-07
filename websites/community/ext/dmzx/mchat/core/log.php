<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2018 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\core;

use phpbb\cache\driver\driver_interface as cache_interface;
use phpbb\event\dispatcher_interface;
use phpbb\user;
use phpbb\db\driver\driver_interface as db_interface;

class log
{
	/** @var settings */
	protected $mchat_settings;

	/** @var user */
	protected $user;

	/** @var db_interface */
	protected $db;

	/** @var cache_interface */
	protected $cache;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var array */
	protected $log_types;

	/**
	 * Constructor
	 *
	 * @param settings				$mchat_settings
	 * @param user					$user
	 * @param db_interface			$db
	 * @param cache_interface		$cache
	 * @param dispatcher_interface	$dispatcher
	 */
	public function __construct(
		settings $mchat_settings,
		user $user,
		db_interface $db,
		cache_interface $cache,
		dispatcher_interface $dispatcher
	)
	{
		$this->mchat_settings	= $mchat_settings;
		$this->user				= $user;
		$this->db				= $db;
		$this->cache			= $cache;
		$this->dispatcher		= $dispatcher;
	}

	/**
	 * Returns an array with all registered log types
	 *
	 * @return array
	 */
	public function get_types()
	{
		if (!$this->log_types)
		{
			// Default log types
			$log_types = [
				1 => 'edit',
				2 => 'del',
			];

			/**
			 * Event that allows adding log types
			 *
			 * @event dmzx.mchat.log_types_init
			 * @var array	log_types	Array containing log types
			 * @since 2.1.0-RC1
			 */
			$vars = [
				'log_types',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.log_types_init', compact($vars)));

			$this->log_types = $log_types;
		}

		return $this->log_types;
	}

	/**
	 * Returns the log type ID for the given string type
	 *
	 * @param string $type
	 * @return int
	 */
	public function get_type_id($type)
	{
		return (int) array_search($type, $this->get_types());
	}

	/**
	 * @param string $log_type The log type, one of edit|del or a custom type
	 * @param int $message_id The ID of the message to which this log entry belongs
	 * @return int The ID of the newly added log row, or 0 if no log row was added
	 */
	public function add_log($log_type, $message_id)
	{
		$log_row = [
			'log_type'		=> $this->get_type_id($log_type),
			'user_id'		=> (int) $this->user->data['user_id'],
			'message_id'	=> (int) $message_id,
			'log_ip'		=> $this->user->ip,
			'log_time'		=> time(),
		];

		$insert_log = true;

		/**
		 * Event that allows adding log types
		 *
		 * @event dmzx.mchat.log_add_before
		 * @var string	log_type	The log type, one of edit|del or a custom type
		 * @var int		message_id	ID of the message to which this log entry belongs
		 * @var array	log_row		Array that is about to be added to the mchat_log table
		 * @var bool	insert_log	Whether or not to add the log_row
		 * @since 2.1.2
		 */
		$vars = [
			'log_type',
			'message_id',
			'log_row',
			'insert_log',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.log_add_before', compact($vars)));

		if (!$insert_log)
		{
			return 0;
		}

		$sql = 'INSERT INTO ' . $this->mchat_settings->get_table_mchat_log() . ' ' . $this->db->sql_build_array('INSERT', $log_row);

		$this->db->sql_query($sql);

		$log_id = (int) $this->db->sql_nextid();

		$this->cache->destroy('sql', $this->mchat_settings->get_table_mchat_log());

		return $log_id;
	}

	/**
	 * Fetches log entries from the database and sorts them
	 *
	 * @param int $log_id The ID of the latest log entry that the user has
	 * @return array
	 */
	public function get_logs($log_id)
	{
		$sql_array = [
			'SELECT'	=> 'ml.*',
			'FROM'		=> [$this->mchat_settings->get_table_mchat_log() => 'ml'],
			'WHERE'		=> 'ml.log_id > ' . (int) $log_id,
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql, 3600);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$log_rows = array_merge(array_fill_keys($this->get_types(), []), [
			'latest' => (int) $log_id,
		]);

		$log_types = $this->get_types();
		$edit_delete_limit = $this->mchat_settings->cfg('mchat_edit_delete_limit');
		$time_limit = $edit_delete_limit ? time() - $edit_delete_limit : 0;

		foreach ($rows as $log_row)
		{
			$log_rows['latest'] = max($log_rows['latest'], (int) $log_row['log_id']);

			$log_type = $log_row['log_type'];

			if (isset($log_types[$log_type]))
			{
				if ($log_row['user_id'] != $this->user->data['user_id'] && $log_row['log_time'] > $time_limit)
				{
					$log_type_name = $log_types[$log_type];
					$log_rows[$log_type_name][] = (int) $log_row['message_id'];
				}
			}

			/**
			 * Event that allows processing log messages
			 *
			 * @event dmzx.mchat.action_refresh_process_log_row
			 * @var array	log_row		The log data (read only)
			 * @since 2.0.0-RC6
			 * @changed 2.1.2 Removed response
			 */
			$vars = [
				'log_row',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.action_refresh_process_log_row', compact($vars)));

			unset($log_row);
		}

		return $log_rows;
	}

	/**
	 * Fetches the highest log ID
	 *
	 * @return int
	 */
	public function get_latest_id()
	{
		$sql_array = [
			'SELECT'	=> 'ml.log_id',
			'FROM'		=> [$this->mchat_settings->get_table_mchat_log() => 'ml'],
			'ORDER_BY'	=> 'log_id DESC',
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 1);
		$max_log_id = (int) $this->db->sql_fetchfield('log_id');
		$this->db->sql_freeresult($result);

		return $max_log_id;
	}
}

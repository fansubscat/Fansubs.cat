<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\conditions;

use phpbb\exception\runtime_exception;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Auto Groups service manager class
 */
class manager
{
	/** @var array Array with auto group types */
	protected $autogroups_types;

	/** @var ContainerInterface */
	protected $phpbb_container;

	/** @var \phpbb\cache\driver\driver_interface */
	protected $cache;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var string The database table the auto group rules are stored in */
	protected $autogroups_rules_table;

	/** @var string The database table the auto group types are stored in */
	protected $autogroups_types_table;

	/**
	 * Constructor
	 *
	 * @param array                                $autogroups_types       Array with auto group types
	 * @param ContainerInterface                   $phpbb_container        Service container interface
	 * @param \phpbb\cache\driver\driver_interface $cache                  Cache driver interface
	 * @param \phpbb\db\driver\driver_interface    $db                     Database object
	 * @param \phpbb\language\language             $language               Language object
	 * @param string                               $autogroups_rules_table Name of the table used to store auto group rules data
	 * @param string                               $autogroups_types_table Name of the table used to store auto group types data
	 *
	 * @access public
	 */
	public function __construct($autogroups_types, ContainerInterface $phpbb_container, \phpbb\cache\driver\driver_interface $cache, \phpbb\db\driver\driver_interface $db, \phpbb\language\language $language, $autogroups_rules_table, $autogroups_types_table)
	{
		$this->autogroups_types = $autogroups_types;
		$this->phpbb_container = $phpbb_container;
		$this->cache = $cache;
		$this->db = $db;
		$this->language = $language;
		$this->autogroups_rules_table = $autogroups_rules_table;
		$this->autogroups_types_table = $autogroups_types_table;
	}

	/**
	 * Check auto groups conditions and execute them
	 *
	 * @return void
	 * @access public
	 */
	public function check_conditions()
	{
		foreach ($this->autogroups_types as $autogroups_type => $data)
		{
			$this->check_condition($autogroups_type);
		}
	}

	/**
	 * Check auto groups condition and execute it
	 *
	 * @param string $type_name Name of the condition
	 * @param array  $options   Array of optional data
	 *
	 * @return void
	 * @access public
	 */
	public function check_condition($type_name, $options = array())
	{
		// Get an instance of the condition type to check
		$condition = $this->phpbb_container->get($type_name);

		// Get the user id array of users to check
		$check_users = $condition->get_users_for_condition($options);

		// Check the users and auto group them
		$condition->check($check_users, $options);
	}

	/**
	 * Add new condition type
	 *
	 * @param string $autogroups_type_name The name of the auto group type
	 *
	 * @return int The identifier of the new condition type
	 * @access public
	 */
	public function add_autogroups_type($autogroups_type_name)
	{
		// Insert the type name into the database
		$sql = 'INSERT INTO ' . $this->autogroups_types_table . ' ' .
			$this->db->sql_build_array('INSERT', array(
				'autogroups_type_name' => (string) $autogroups_type_name
			)
		);
		$this->db->sql_query($sql);

		// Return the id of the newly inserted condition type
		return (int) $this->db->sql_nextid();
	}

	/**
	 * Purge all conditions of a certain type
	 * (Note: This method is not used directly by Auto Groups, but is
	 * used in the purge step of extensions extending Auto Groups.)
	 *
	 * @param string $autogroups_type_name The name of the auto group type
	 *
	 * @return void
	 * @access public
	 */
	public function purge_autogroups_type($autogroups_type_name)
	{
		try
		{
			// Get the id of the condition
			$condition_type_id = $this->get_autogroups_type_id($autogroups_type_name);

			// Delete any rules associated with the condition id
			$sql = 'DELETE FROM ' . $this->autogroups_rules_table . '
				WHERE autogroups_type_id = ' . (int) $condition_type_id;
			$this->db->sql_query($sql);

			// Delete any types associated with the condition id
			$sql = 'DELETE FROM ' . $this->autogroups_types_table . '
				WHERE autogroups_type_id = ' . (int) $condition_type_id;
			$this->db->sql_query($sql);

			// Clear any cached autogroups data
			$this->cache->destroy('_autogroups_type_ids');
		}
		catch (runtime_exception $e)
		{
			// Continue
		}
	}

	/**
	 * Purge all autogroups rules related to a certain group_id
	 *
	 * @param int $group_id Group identifier
	 * @return void
	 * @access public
	 */
	public function purge_autogroups_group($group_id)
	{
		// Delete any rules associated with the group id
		$sql = 'DELETE FROM ' . $this->autogroups_rules_table . '
			WHERE autogroups_group_id = ' . (int) $group_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Get the condition type id from the name
	 *
	 * @param string $autogroups_type_name The name of the auto group type
	 *
	 * @return int The condition_type_id
	 * @throws runtime_exception
	 */
	public function get_autogroups_type_id($autogroups_type_name)
	{
		// Get cached auto groups ids if they exist
		$autogroups_type_ids = $this->cache->get('_autogroups_type_ids');

		// Get auto groups ids from the db if no cache data exists, cache result
		if ($autogroups_type_ids === false)
		{
			$autogroups_type_ids = array();

			$sql = 'SELECT autogroups_type_id, autogroups_type_name
				FROM ' . $this->autogroups_types_table;
			$result = $this->db->sql_query($sql);
			while ($row = $this->db->sql_fetchrow($result))
			{
				$autogroups_type_ids[$row['autogroups_type_name']] = (int) $row['autogroups_type_id'];
			}
			$this->db->sql_freeresult($result);

			$this->cache->put('_autogroups_type_ids', $autogroups_type_ids);
		}

		// Add auto group type name to db if it exists as service but is not in db, cache result
		if (!isset($autogroups_type_ids[$autogroups_type_name]))
		{
			if (!isset($this->autogroups_types[$autogroups_type_name]))
			{
				throw new runtime_exception('AUTOGROUPS_TYPE_NOT_EXIST', array($autogroups_type_name));
			}

			$autogroups_type_ids[$autogroups_type_name] = $this->add_autogroups_type($autogroups_type_name);

			$this->cache->put('_autogroups_type_ids', $autogroups_type_ids);
		}

		return $autogroups_type_ids[$autogroups_type_name];
	}

	/**
	 * Get condition type ids (as an array)
	 *
	 * @return array Array of condition type ids
	 * @access public
	 */
	public function get_autogroups_type_ids()
	{
		$autogroups_type_ids = array();

		foreach ($this->autogroups_types as $type_name => $data)
		{
			$autogroups_type_ids[$type_name] = $this->get_autogroups_type_id($type_name);
		}

		return $autogroups_type_ids;
	}

	/**
	 * Get the condition type name from the condition or rule id
	 *
	 * @param int     $type_id      The id of the auto group type
	 * @param int     $rule_id      The id of the auto group rule
	 *
	 * @return string|false The condition type name, false on error
	 * @access public
	 */
	public function get_autogroups_type_name($type_id = 0, $rule_id = 0)
	{
		$sql_array = array(
			'SELECT'	=> 'agt.autogroups_type_name',
			'FROM'		=> array(
				$this->autogroups_types_table => 'agt',
			),
		);

		if ($type_id)
		{
			$sql_array['WHERE'] = 'agt.autogroups_type_id = ' . (int) $type_id;
		}
		else if ($rule_id)
		{
			$sql_array['LEFT_JOIN'] = array(
				array(
					'FROM'	=>	array($this->autogroups_rules_table	=> 'agr'),
					'ON'	=> 'agt.autogroups_type_id = agr.autogroups_type_id',
				),
			);
			$sql_array['WHERE'] = 'agr.autogroups_id = ' . (int) $rule_id;
		}
		else
		{
			return false;
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$autogroups_type_name = $this->db->sql_fetchfield('autogroups_type_name');
		$this->db->sql_freeresult($result);

		return $autogroups_type_name;
	}

	/**
	 * Get the condition language var from the condition type class
	 *
	 * @param string     $autogroups_type_name      The name of the auto group type
	 *
	 * @return string The condition type name
	 * @access public
	 */
	public function get_condition_lang($autogroups_type_name)
	{
		try
		{
			$condition = $this->phpbb_container->get($autogroups_type_name);
		}
		catch (\InvalidArgumentException $e)
		{
			return $this->language->lang('AUTOGROUPS_TYPE_NOT_EXIST', $autogroups_type_name);
		}

		return $condition->get_condition_type_name();
	}

	/**
	 * Run auto groups check against users for a given condition/type
	 * Called in the ACP when adding/editing or via the Resync button
	 *
	 * @param int     $autogroups_rule_id      The id of the auto group rule
	 *
	 * @return void
	 * @access public
	 */
	public function sync_autogroups($autogroups_rule_id)
	{
		// Purge cached rules table queries
		$this->cache->destroy('sql', $this->autogroups_rules_table);

		// Get the auto group type name used by the specified auto group rule
		$autogroups_type_name = $this->get_autogroups_type_name(0, $autogroups_rule_id);

		// If auto group type exists, run it
		if ($autogroups_type_name !== false)
		{
			$this->check_condition($autogroups_type_name, array(
				'action'	=> 'sync',
			));
		}
	}
}

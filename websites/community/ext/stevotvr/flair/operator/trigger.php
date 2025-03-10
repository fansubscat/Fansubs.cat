<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\operator;

use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair flair trigger operator.
 */
class trigger extends operator implements trigger_interface
{
	/**
	 * @var user_interface
	 */
	protected $user_operator;

	/**
	 * Set up the operator.
	 *
	 * @param user_interface $user_operator
	 */
	public function setup(user_interface $user_operator)
	{
		$this->user_operator = $user_operator;
	}

	/**
	 * @inheritDoc
	 */
	public function get_flair_triggers($flair_id)
	{
		return $this->get_trigger_rows('flair_id = ' . (int) $flair_id);
	}

	/**
	 * Get a list of trigger entities from the database.
	 *
	 * @param string $where The WHERE clause of the database query
	 *
	 * @return array An associative array of trigger names to values
	 */
	protected function get_trigger_rows($where)
	{
		$triggers = array();

		$sql = 'SELECT *
				FROM ' . $this->trigger_table . '
				WHERE ' . $where;
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$triggers[$row['trig_name']] = (int) $row['trig_value'];
		}
		$this->db->sql_freeresult();

		return $triggers;
	}

	/**
	 * @inheritDoc
	 */
	public function set_trigger($flair_id, $trigger_name, $trigger_value)
	{
		if ($trigger_value < 0 || $trigger_value > 16777215)
		{
			throw new out_of_bounds('trig_value');
		}

		if (truncate_string($trigger_name, 255) !== $trigger_name)
		{
			throw new unexpected_value('trig_name', 'TOO_LONG');
		}

		if (!preg_match('/^[a-z_]+$/', $trigger_name))
		{
			throw new unexpected_value('trig_name', 'BAD_TRIG_NAME');
		}

		$this->unset_trigger($flair_id, $trigger_name);

		if (!$trigger_value)
		{
			return;
		}

		$sql_ary = array(
			'flair_id'		=> (int) $flair_id,
			'trig_name'		=> $trigger_name,
			'trig_value'	=> (int) $trigger_value,
		);
		$sql = 'INSERT INTO ' . $this->trigger_table . '
				' . $this->db->sql_build_array('INSERT', $sql_ary);
		$this->db->sql_query($sql);
	}

	/**
	 * @inheritDoc
	 */
	public function unset_trigger($flair_id, $trigger_name)
	{
		$sql = 'DELETE FROM ' . $this->trigger_table . '
				WHERE flair_id = ' . (int) $flair_id . "
					AND trig_name = '" . $this->db->sql_escape($trigger_name) . "'";
		$this->db->sql_query($sql);
	}

	/**
	 * @inheritDoc
	 */
	public function dispatch($user_id, $trigger_name, $trigger_value)
	{
		$flair_ids = array();

		$sql = 'SELECT flair_id
				FROM ' . $this->trigger_table . "
				WHERE trig_name = '" . $this->db->sql_escape($trigger_name) . "'
					AND trig_value <= " . (int) $trigger_value;
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$flair_ids[(int) $row['flair_id']] = true;
		}
		$this->db->sql_freeresult();

		if (empty($flair_ids))
		{
			return;
		}

		$sql = 'SELECT flair_id
				FROM ' . $this->user_table . '
				WHERE user_id = ' . (int) $user_id . '
					AND ' . $this->db->sql_in_set('flair_id', array_keys($flair_ids));
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			unset($flair_ids[(int) $row['flair_id']]);
		}
		$this->db->sql_freeresult();

		foreach (array_keys($flair_ids) as $flair_id)
		{
			$this->user_operator->add_flair($user_id, $flair_id);
		}
	}
}

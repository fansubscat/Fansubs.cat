<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\migrations\v10x;

/**
 * Migration stage 2: Initial data
 */
class m2_initial_data extends \phpbb\db\migration\migration
{
	/**
	 * Assign migration file dependencies for this migration
	 *
	 * @return array Array of migration files
	 * @static
	 * @access public
	 */
	public static function depends_on()
	{
		return array('\phpbb\autogroups\migrations\v10x\m1_initial_schema');
	}

	/**
	 * Add or update data in the database
	 *
	 * @return array Array of table data
	 * @access public
	 */
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'add_autogroups_type_data'))),
		);
	}

	/**
	 * Add auto group types to the database
	 *
	 * @return void
	 * @access public
	 */
	public function add_autogroups_type_data()
	{
		// Load the insert buffer class to perform a buffered multi insert
		$insert_buffer = new \phpbb\db\sql_insert_buffer($this->db, $this->table_prefix . 'autogroups_types');

		// Auto group types array
		$types = array(
			'birthdays',
			'membership',
			'posts',
			'warnings',
		);

		// Insert data
		foreach ($types as $type)
		{
			$insert_buffer->insert(array(
				'autogroups_type_name'	=> 'phpbb.autogroups.type.' . $type,
			));
		}

		// Flush the buffer
		$insert_buffer->flush();
	}
}

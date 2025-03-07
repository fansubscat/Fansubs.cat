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
 * Migration stage 4: Notification data
 */
class m4_notification_data extends \phpbb\db\migration\migration
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
	 * Add table columns schema to the database:
	 *    auto groups:
	 *        autogroups_id
	 *
	 * @return array Array of table columns schema
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'autogroups_rules'	=> array(
					'autogroups_notify'		=> array('BOOL', 0),
				),
			),
		);
	}

	/**
	 * Drop table columns schema from the database
	 *
	 * @return array Array of table columns schema
	 * @access public
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'autogroups_rules'	=> array(
					'autogroups_notify',
				),
			),
		);
	}
}

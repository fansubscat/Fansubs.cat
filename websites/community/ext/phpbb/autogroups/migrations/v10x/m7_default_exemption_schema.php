<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\migrations\v10x;

/**
 * Migration stage 7: Group default exemption schema
 */
class m7_default_exemption_schema extends \phpbb\db\migration\migration
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
	 *    groups:
	 *        autogroup_default_exempt
	 *
	 * @return array Array of table columns schema
	 * @access public
	 */
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'groups'	=> array(
					'autogroup_default_exempt'	=> array('BOOL', 0),
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
				$this->table_prefix . 'groups'	=> array(
					'autogroup_default_exempt',
				),
			),
		);
	}
}

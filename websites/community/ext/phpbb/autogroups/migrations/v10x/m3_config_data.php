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
 * Migration stage 3: Config data
 */
class m3_config_data extends \phpbb\db\migration\migration
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
			array('config.add', array('autogroups_default_exempt', $this->get_initial_groups())),
		);
	}

	/**
	 * Get the group ids of Administrators and Global Moderators
	 *
	 * @return string Serialized string of group ids
	 * @access protected
	 */
	protected function get_initial_groups()
	{
		$group_ids = array();

		$sql = 'SELECT group_id
			FROM ' . GROUPS_TABLE . '
			WHERE ' . $this->db->sql_in_set('group_name', array('ADMINISTRATORS', 'GLOBAL_MODERATORS'));
		$result = $this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow($result))
		{
			$group_ids[] = $row['group_id'];
		}
		$this->db->sql_freeresult($result);

		return count($group_ids) ? serialize($group_ids) : '';
	}
}

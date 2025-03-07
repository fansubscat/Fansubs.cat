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
 * Migration stage 8: Group default exemption data
 */
class m8_default_exemption_data extends \phpbb\db\migration\migration
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
		return array(
			'\phpbb\autogroups\migrations\v10x\m3_config_data',
			'\phpbb\autogroups\migrations\v10x\m7_default_exemption_schema'
		);
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
			// Remove deprecated config item
			array('config.remove', array('autogroups_default_exempt')),

			// Enable default exemption for Administrators and Global Mods
			array('custom', array(array($this, 'update_default_exempt_groups'))),
		);
	}

	/**
	 * Set Administrators and Global Moderators to default exempt
	 *
	 * @return void
	 * @access public
	 */
	public function update_default_exempt_groups()
	{
		$sql = 'UPDATE ' . GROUPS_TABLE . '
			SET autogroup_default_exempt = 1
			WHERE ' . $this->db->sql_in_set('group_name', array('ADMINISTRATORS', 'GLOBAL_MODERATORS'));
		$this->db->sql_query($sql);
	}
}

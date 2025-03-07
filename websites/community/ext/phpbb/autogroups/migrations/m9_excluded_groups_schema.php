<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2015 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\migrations;

/**
 * Migration stage 9: Exclude groups schema
 */
class m9_excluded_groups_schema extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritDoc}
	 */
	public function effectively_installed()
	{
		return $this->db_tools->sql_column_exists($this->table_prefix . 'autogroups_rules', 'autogroups_excluded_groups');
	}

	/**
	 * {@inheritDoc}
	 */
	public static function depends_on()
	{
		return ['\phpbb\autogroups\migrations\v10x\m1_initial_schema'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function update_schema()
	{
		return [
			'add_columns'	=> [
				$this->table_prefix . 'autogroups_rules'	=> [
					'autogroups_excluded_groups'	=> ['VCHAR_UNI', ''],
				],
			],
		];
	}

	/**
	 * {@inheritDoc}
	 */
	public function revert_schema()
	{
		return [
			'drop_columns'	=> [
				$this->table_prefix . 'autogroups_rules'	=> [
					'autogroups_excluded_groups',
				],
			],
		];
	}
}

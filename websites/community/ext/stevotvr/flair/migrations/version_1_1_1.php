<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\migrations;

use phpbb\db\migration\migration;

/**
 * Profile Flair migration for version 1.1.1.
 */
class version_1_1_1 extends migration
{
	/**
	 * @inheritDoc
	 */
	static public function depends_on()
	{
		return array('\stevotvr\flair\migrations\version_1_0_0');
	}

	/**
	 * @inheritDoc
	 */
	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'fix_triggers_pk'))),
		);
	}

	/**
	 * Fix the primary key for the triggers table.
	 */
	public function fix_triggers_pk()
	{
		$table_name = $this->table_prefix . 'flair_triggers';

		$sql = 'SELECT * FROM ' . $table_name;
		$this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset();
		$this->db->sql_freeresult();

		$this->db_tools->sql_table_drop($table_name);

		$migration = new version_1_0_0($this->config, $this->db, $this->db_tools, $this->phpbb_root_path, $this->php_ext, $this->table_prefix);
		$update_schema = $migration->update_schema();
		$table_data = $update_schema['add_tables'][$table_name];
		$table_data['PRIMARY_KEY'] = array('flair_id', 'trig_name');
		$this->db_tools->sql_create_table($table_name, $table_data);

		$this->db->sql_multi_insert($table_name, $rows);
	}
}

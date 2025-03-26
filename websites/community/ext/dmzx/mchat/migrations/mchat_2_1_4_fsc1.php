<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2020 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_1_4_fsc1 extends migration
{
	public static function depends_on()
	{
		return [
			'\dmzx\mchat\migrations\mchat_2_1_4',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['mchat_version', '2.1.4-FSC1']],
		];
	}

	public function update_schema()
	{
		return array(
			'add_columns' => array(
				$this->table_prefix . 'mchat' => array(
					'system_message' => array('BOOL', 0),
					'deleted' => array('BOOL', 0),
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_columns' => array(
				$this->table_prefix . 'mchat' => array(
					'system_message',
					'deleted',
				),
			),
		);
	}
}

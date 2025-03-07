<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2018 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_1_1 extends migration
{
	public static function depends_on()
	{
		return [
			'\dmzx\mchat\migrations\mchat_2_1_0',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['mchat_version', '2.1.1']],

			['config.remove', ['mchat_input_area']],
			['permission.remove', ['u_mchat_input_area', true]],
		];
	}

	public function update_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'users' => [
					'user_mchat_input_area',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_mchat_input_area' => ['BOOL', 1],
				],
			],
		];
	}
}

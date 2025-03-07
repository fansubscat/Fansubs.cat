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

class mchat_2_1_0_rc1 extends migration
{
	public static function depends_on()
	{
		return [
			'\dmzx\mchat\migrations\mchat_2_0_3',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['mchat_version', '2.1.0-RC1']],

			['config.remove', ['mchat_navbar_link']],

			['config.add', ['mchat_max_input_height', 150]],

			['config.add', ['mchat_log_enabled', 1]],

			// Message reparser
			['config.add', ['dmzx.mchat.text_reparser.mchat_messages_cron_interval', 10]],
			['config.add', ['dmzx.mchat.text_reparser.mchat_messages_last_cron', 0]],

			// Remove pause on input
			['config.remove', ['mchat_pause_on_input']],
			['permission.remove', ['u_mchat_pause_on_input', true]],
		];
	}

	public function update_schema()
	{
		return [
			'drop_columns' => [
				$this->table_prefix . 'users' => [
					'user_mchat_pause_on_input',
				],
			],
		];
	}

	public function revert_schema()
	{
		return [
			'add_columns' => [
				$this->table_prefix . 'users' => [
					'user_mchat_pause_on_input' => ['BOOL', 0],
				],
			],
		];
	}
}

<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2019 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_1_3 extends migration
{
	public static function depends_on()
	{
		return [
			'\dmzx\mchat\migrations\mchat_2_1_2',
		];
	}

	public function update_data()
	{
		return [
			['config.update', ['mchat_version', '2.1.3']],
			['config.add', ['mchat_flood_messages', 0]],
		];
	}
}

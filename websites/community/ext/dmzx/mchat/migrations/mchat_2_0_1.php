<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2017 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use dmzx\mchat\core\settings;
use phpbb\db\migration\migration;

class mchat_2_0_1 extends migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\mchat\migrations\mchat_2_0_0',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('mchat_version', '2.0.1')),
			array('config.add', array('mchat_archive_sort', settings::ARCHIVE_SORT_BOTTOM_TOP)),
		);
	}
}

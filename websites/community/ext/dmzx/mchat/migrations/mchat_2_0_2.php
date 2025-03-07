<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2017 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_0_2 extends migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\mchat\migrations\mchat_2_0_1',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('mchat_version', '2.0.2')),

			array('config.add', array('mchat_posts_auth_check', 0)),

			// Move rules and static message from config to config_text table
			array('config_text.add', array('mchat_rules', $this->config['mchat_rules'])),
			array('config_text.add', array('mchat_static_message', $this->config['mchat_static_message'])),

			array('config.remove', array('mchat_rules')),
			array('config.remove', array('mchat_static_message')),
		);
	}
}

<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_0_0_rc6 extends migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\mchat\migrations\mchat_2_0_0_rc5',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('mchat_version', '2.0.0-RC6')),

			array('config.add', array('mchat_navbar_link_count', 1)),
			array('config.add', array('mchat_posts_login', 0)),

			array('config.add', array('mchat_prune_gc', strtotime('1 day', 0))),
			array('config.add', array('mchat_prune_last_gc', 0, true)), // true => value is dynamic, do not cache

			array('config.remove', array('mchat_whois')),

			array('permission.add', array('u_mchat_moderator_edit', true)),
			array('permission.add', array('u_mchat_moderator_delete', true)),
		);
	}

	public function update_schema()
	{
		return array(
			'add_tables'	=> array(
				$this->table_prefix . 'mchat_log' => array(
					'COLUMNS'		=> array(
						'log_id'				=> array('UINT', null, 'auto_increment'),
						'log_type'				=> array('TINT:4', 0),
						'user_id'				=> array('UINT', 0),
						'message_id'			=> array('UINT', 0),
						'log_ip'				=> array('VCHAR:40', ''),
						'log_time'				=> array('INT:11', 0),
					),
					'PRIMARY_KEY'	=> 'log_id',
				),
			),
			'drop_tables'	=> array(
				$this->table_prefix . 'mchat_deleted_messages',
			),

			'drop_columns'	=> array(
				$this->table_prefix . 'mchat' => array(
					'edit_time',
				),
			),
		);
	}

	public function revert_schema()
	{
		return array(
			'drop_tables'	=> array(
				$this->table_prefix . 'mchat_log',
			),
		);
	}
}

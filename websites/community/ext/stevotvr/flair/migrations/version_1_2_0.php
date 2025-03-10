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
 * Profile Flair migration for version 1.2.0.
 */
class version_1_2_0 extends migration
{
	/**
	 * @inheritDoc
	 */
	static public function depends_on()
	{
		return array('\stevotvr\flair\migrations\version_1_1_1');
	}

	/**
	 * @inheritDoc
	 */
	public function update_schema()
	{
		return array(
			'add_tables'    => array(
				$this->table_prefix . 'flair_notif' => array(
					'COLUMNS' => array(
						'notification_id'	=> array('UINT', null, 'auto_increment'),
						'user_id'			=> array('UINT', 0),
						'flair_id'			=> array('UINT', 0),
						'flair_name'		=> array('VCHAR_UNI', ''),
						'old_count'			=> array('UINT', 0),
						'new_count'			=> array('UINT', 0),
						'updated'			=> array('UINT:11', 0),
					),
					'PRIMARY_KEY' => 'notification_id',
					'KEYS' => array(
						'u_f'	=> array('UNIQUE', array('user_id', 'flair_id')),
					),
				),
				$this->table_prefix . 'flair_favs' => array(
					'COLUMNS' => array(
						'user_id'		=> array('UINT', 0),
						'flair_id'		=> array('UINT', 0),
					),
					'PRIMARY_KEY' => array('flair_id', 'user_id'),
				),
			),
			'add_columns'	=> array(
				$this->table_prefix . 'flair' => array(
					'flair_groups_auto'	=> array('BOOL', 1),
				),
				$this->table_prefix . 'flair_cats' => array(
					'cat_display_limit'	=> array('UINT', 0),
				),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function revert_schema()
	{
		return array(
			'drop_tables'   => array(
				$this->table_prefix . 'flair_notif',
				$this->table_prefix . 'flair_favs',
			),
			'drop_columns'	=> array(
				$this->table_prefix . 'flair' => array(
					'flair_groups_auto',
				),
				$this->table_prefix . 'flair_cats' => array(
					'cat_display_limit',
				),
			),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function update_data()
	{
		return array(
			array('config.add', array('stevotvr_flair_display_limit', 0)),
			array('config.add', array('stevotvr_flair_notify_users', 1)),
			array('config.add', array('stevotvr_flair_cron_last_run', 0)),

			array('permission.add', array('m_userflair', true, 'a_manage_flair')),
			array('permission.add', array('u_flair', true, 'u_sig')),
			array('permission.remove', array('a_manage_flair')),

			array('module.remove', array(
				'acp',
				'ACP_CAT_USERS',
				array(
					'module_basename'	=> '\stevotvr\flair\acp\user_module',
					'modes'				=> array('main'),
				),
			)),
			array('module.add', array(
				'mcp',
				0,
				'MCP_FLAIR',
			)),
			array('module.add', array(
				'mcp',
				'MCP_FLAIR',
				array(
					'module_basename'	=> '\stevotvr\flair\mcp\user_module',
					'modes'				=> array('front', 'user_flair'),
				),
			)),
			array('module.add', array(
				'ucp',
				'UCP_PROFILE',
				array(
					'module_basename'	=> '\stevotvr\flair\ucp\flair_module',
					'modes'				=> array('main'),
				),
			)),
		);
	}
}

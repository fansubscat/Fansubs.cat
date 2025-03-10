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
 * Profile Flair migration for version 1.1.0.
 */
class version_1_1_0 extends migration
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
	public function update_schema()
	{
		return array(
			'add_columns'	=> array(
				$this->table_prefix . 'flair' => array(
					'flair_type'	=> array('USINT', 0),
					'flair_img'		=> array('VCHAR_UNI', ''),
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
			array('module.add', array(
				'acp',
				'ACP_FLAIR_TITLE',
				array(
					'module_basename'	=> '\stevotvr\flair\acp\main_module',
					'modes'				=> array('images'),
				),
			)),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function revert_schema()
	{
		return array(
			'drop_columns'	=> array(
				$this->table_prefix . 'flair' => array(
					'flair_type',
					'flair_img',
				),
			),
		);
	}
}

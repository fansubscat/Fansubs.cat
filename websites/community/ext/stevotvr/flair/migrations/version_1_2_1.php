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
 * Profile Flair migration for version 1.2.1.
 */
class version_1_2_1 extends migration
{
	/**
	 * @inheritDoc
	 */
	static public function depends_on()
	{
		return array('\stevotvr\flair\migrations\version_1_2_0');
	}

	/**
	 * @inheritDoc
	 */
	public function update_data()
	{
		return array(
			array('module.remove', array(
				'mcp',
				'MCP_FLAIR',
				array(
					'module_basename'	=> '\stevotvr\flair\mcp\user_module',
					'modes'				=> array('front', 'user_flair'),
				),
			)),
			array('module.add', array(
				'mcp',
				'MCP_FLAIR',
				array(
					'module_basename'	=> '\stevotvr\flair\mcp\user_module',
					'modes'				=> array('front', 'user_flair'),
				),
			)),
		);
	}
}

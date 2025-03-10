<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\mcp;

/**
 * Profile Flair user MCP module info.
 */
class user_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\mcp\user_module',
			'title'		=> 'MCP_FLAIR',
			'modes'		=> array(
				'front'			=> array(
					'title'	=> 'MCP_FLAIR_FRONT',
					'auth'	=> 'ext_stevotvr/flair && (acl_m_userflair || acl_a_board)',
					'cat'	=> array('MCP_FLAIR'),
				),
				'user_flair'	=> array(
					'title'	=> 'MCP_FLAIR_USER',
					'auth'	=> 'ext_stevotvr/flair && (acl_m_userflair || acl_a_board)',
					'cat'	=> array('MCP_FLAIR'),
				),
			),
		);
	}
}

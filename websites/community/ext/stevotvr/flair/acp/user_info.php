<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\acp;

/**
 * Profile Flair user ACP module info.
 */
class user_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\acp\user_module',
			'title'		=> 'ACP_FLAIR_MANAGE_USERS',
			'modes'		=> array(
				'main'	=> array(
					'title'	=> 'ACP_FLAIR_MANAGE_USERS',
					'auth'	=> 'ext_stevotvr/flair && acl_a_manage_flair',
					'cat'	=> array('ACP_FLAIR_MANAGE_USERS'),
				),
			),
		);
	}
}

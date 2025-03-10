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
 * Profile Flair main ACP module info.
 */
class main_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\acp\main_module',
			'title'		=> 'ACP_FLAIR_TITLE',
			'modes'		=> array(
				'settings'	=> array(
					'title'	=> 'ACP_FLAIR_SETTINGS',
					'auth'	=> 'ext_stevotvr/flair && acl_a_board',
					'cat'	=> array('ACP_FLAIR_TITLE'),
				),
				'manage'	=> array(
					'title'	=> 'ACP_FLAIR_MANAGE',
					'auth'	=> 'ext_stevotvr/flair && acl_a_board',
					'cat'	=> array('ACP_FLAIR_TITLE'),
				),
				'images'	=> array(
					'title'	=> 'ACP_FLAIR_IMAGES',
					'auth'	=> 'ext_stevotvr/flair && acl_a_board',
					'cat'	=> array('ACP_FLAIR_TITLE'),
				),
			),
		);
	}
}

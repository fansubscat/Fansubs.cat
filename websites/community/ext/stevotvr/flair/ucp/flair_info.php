<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\ucp;

/**
 * Profile Flair UCP module info.
 */
class flair_info
{
	public function module()
	{
		return array(
			'filename'	=> '\stevotvr\flair\ucp\flair_module',
			'title'		=> 'UCP_FLAIR',
			'modes'		=> array(
				'main'	=> array(
					'title'	=> 'UCP_FLAIR',
					'auth'	=> 'ext_stevotvr/flair && acl_u_flair',
					'cat'	=> array('UCP_FLAIR'),
				),
			),
		);
	}
}

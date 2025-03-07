<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\acp;

class autogroups_info
{
	public function module()
	{
		return array(
			'filename'	=> '\phpbb\autogroups\acp\autogroups_module',
			'title'		=> 'ACP_AUTOGROUPS_MANAGE',
			'modes'		=> array(
				'manage'	=> array(
					'title'	=> 'ACP_AUTOGROUPS_MANAGE',
					'auth'	=> 'ext_phpbb/autogroups && acl_a_group',
					'cat'	=> array('ACP_GROUPS')),
			),
		);
	}
}

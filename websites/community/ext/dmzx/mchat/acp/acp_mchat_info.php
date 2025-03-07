<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\acp;

class acp_mchat_info
{
	function module()
	{
		return [
			'filename'	=> '\dmzx\mchat\acp\acp_mchat_module',
			'title'		=> 'ACP_CAT_MCHAT',
			'modes'		=> [
				'globalsettings'		=> [
					'title'	=> 'ACP_MCHAT_GLOBALSETTINGS',
					'auth'	=> 'ext_dmzx/mchat && acl_a_mchat',
					'cat'	=> ['ACP_CAT_MCHAT'],
				],
				'globalusersettings'	=> [
					'title'	=> 'ACP_MCHAT_GLOBALUSERSETTINGS',
					'auth'	=> 'ext_dmzx/mchat && acl_a_mchat',
					'cat'	=> ['ACP_CAT_MCHAT'],
				],
			],
		];
	}
}

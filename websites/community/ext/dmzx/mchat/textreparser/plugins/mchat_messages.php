<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2018 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\textreparser\plugins;

use phpbb\textreparser\row_based_plugin;

class mchat_messages extends row_based_plugin
{
	public function get_columns()
	{
		return [
			'id'			=> 'message_id',
			'text'			=> 'message',
			'bbcode_uid'	=> 'bbcode_uid',
			'options'		=> 'bbcode_options',
		];
	}
}

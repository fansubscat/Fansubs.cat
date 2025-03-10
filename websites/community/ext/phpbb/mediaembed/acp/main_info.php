<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\acp;

/**
 * phpBB Media Embed Plugin ACP module info.
 */
class main_info
{
	public function module()
	{
		return [
			'filename'	=> '\phpbb\mediaembed\acp\main_module',
			'title'		=> 'ACP_PHPBB_MEDIA_EMBED',
			'modes'		=> [
				'settings'	=> ['title' => 'ACP_PHPBB_MEDIA_EMBED_SETTINGS', 'auth' => 'ext_phpbb/mediaembed && acl_a_bbcode', 'cat' => ['ACP_PHPBB_MEDIA_EMBED']],
				'manage'	=> ['title' => 'ACP_PHPBB_MEDIA_EMBED_MANAGE', 'auth' => 'ext_phpbb/mediaembed && acl_a_bbcode', 'cat' => ['ACP_PHPBB_MEDIA_EMBED']],
			],
		];
	}
}

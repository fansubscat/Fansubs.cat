<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration 3: Add a config var for parsing plain urls
 */
class m3_plain_urls_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->config->offsetExists('media_embed_parse_urls');
	}

	public static function depends_on()
	{
		return ['\phpbb\mediaembed\migrations\m1_install_data'];
	}

	public function update_data()
	{
		return [
			['config.add', ['media_embed_parse_urls', 1]],
		];
	}
}

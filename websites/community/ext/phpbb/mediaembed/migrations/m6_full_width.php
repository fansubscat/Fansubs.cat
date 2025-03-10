<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration to install MediaEmbed config for full width responsive embeds
 */
class m6_full_width extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\phpbb\mediaembed\migrations\m5_cache'];
	}

	public function effectively_installed()
	{
		return $this->config->offsetExists('media_embed_full_width');
	}

	public function update_data()
	{
		return [
			['config.add', ['media_embed_full_width', 0]],
			['config_text.add', ['media_embed_max_width', '']],
		];
	}
}

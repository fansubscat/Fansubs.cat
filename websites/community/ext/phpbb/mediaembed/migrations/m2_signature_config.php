<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration 2: Add a config var for the signature setting
 */
class m2_signature_config extends \phpbb\db\migration\migration
{
	public function effectively_installed()
	{
		return $this->config->offsetExists('media_embed_allow_sig');
	}

	public static function depends_on()
	{
		return ['\phpbb\mediaembed\migrations\m1_install_data'];
	}

	public function update_data()
	{
		return [
			['config.add', ['media_embed_allow_sig', 0]],
		];
	}
}

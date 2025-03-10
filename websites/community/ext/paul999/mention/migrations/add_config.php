<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\migrations;

class add_config extends \phpbb\db\migration\migration
{
	static public function depends_on()
	{
		return array('\paul999\mention\migrations\version_100rc2');
	}

	public function update_data()
	{
		return [
			['config.add', ['simple_mention_minlength', '1', false]],
			['config.add', ['simple_mention_color', 'ff0000', false]],
		];
	}
}

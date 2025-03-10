<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\simplespoiler\migrations\v13x;

use phpbb\db\migration\migration;

class m1_spoiler_data extends migration
{
	/**
	 * Migration dependencies.
	 *
	 * @return array
	 */
	static public function depends_on()
	{
		return ['\alfredoramos\simplespoiler\migrations\v10x\m1_spoiler_data'];
	}

	/**
	 * Check if it's already installed.
	 *
	 * @return bool
	 */
	public function effectively_installed()
	{
		return isset($this->config['max_spoiler_depth']);
	}

	/**
	 * Add Spoiler configuration.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			[
				'config.add',
				['max_spoiler_depth', 3]
			]
		];
	}
}

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

use phpbb\db\migration\container_aware_migration;

class add_bbcode extends container_aware_migration
{
	static public function depends_on()
	{
		return array('\phpbb\db\migration\data\v31x\v314');
	}

	public function update_data()
	{
		return array(
			array('custom', array(array($this, 'install_bbcodes'))),
		);
	}
	public function install_bbcodes()
	{
		// Removed on purpose. Starting with version 2.0 the old BBCode is no longer used.
		// The old BBCode is (On purpose) never removed, to keep it working.
		// For new installations: There is no mention BBCode yet, and adding it is no longer needed.
		// However, we can't remove it in a later migration as that will break old installs.
		// Migration on purpose not removed, but just made empty to keep backwards compatibility.
	}
}

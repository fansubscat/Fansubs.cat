<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\migrations;

use phpbb\db\migration\migration;

class mchat_2_0_0_rc7 extends migration
{
	static public function depends_on()
	{
		return array(
			'\dmzx\mchat\migrations\mchat_2_0_0_rc6',
		);
	}

	public function update_data()
	{
		return array(
			array('config.update', array('mchat_version', '2.0.0-RC7')),

			array('config.add', array('mchat_prune_mode', 0)),
			array('custom', array(array($this, 'fix_pruning_time_span'))),
		);
	}

	/**
	 * In 2.0.0-RC6 it was possible to specify a time span like '45 minutes' or '4 days' to
	 * keep the messages from that time span when pruning messages. Since 2.0.0-RC7 it is
	 * only possible to specify the number of hours, days or weeks. If such a time span is
	 * defined in the mchat_prune_num config value it is converted here to the number of
	 * hours, rounded up to the next full hour.
	 */
	public function fix_pruning_time_span()
	{
		$prune_num = $this->config['mchat_prune_num'];

		if (false === filter_var($prune_num, FILTER_VALIDATE_INT))
		{
			$time_span = strtotime($prune_num, 0);

			if ($time_span !== false)
			{
				// A time span is specified. Convert it to number of hours.
				$hours = ceil($time_span / 60 / 60);

				$sql = 'UPDATE ' . CONFIG_TABLE . '
					SET config_value = ' . (int) $hours . "
					WHERE config_name = 'mchat_prune_num'";
				$this->sql_query($sql);

				// Set to 'hours' mode
				$sql = 'UPDATE ' . CONFIG_TABLE . "
					SET config_value = 1
					WHERE config_name = 'mchat_prune_mode'";
				$this->sql_query($sql);
			}
		}
	}
}

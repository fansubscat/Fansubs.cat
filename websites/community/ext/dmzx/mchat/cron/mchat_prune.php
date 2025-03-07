<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\cron;

use dmzx\mchat\core\functions;
use dmzx\mchat\core\settings;
use phpbb\cron\task\base;

class mchat_prune extends base
{
	/** @var functions */
	protected $mchat_functions;

	/** @var settings */
	protected $mchat_settings;

	/**
	 * Constructor
	 *
	 * @param functions	$mchat_functions
	 * @param settings	$mchat_settings
	 */
	public function __construct(
		functions $mchat_functions,
		settings $mchat_settings
	)
	{
		$this->mchat_functions	= $mchat_functions;
		$this->mchat_settings	= $mchat_settings;
	}

	/**
	 * Runs this cron task.
	 */
	public function run()
	{
		$this->mchat_functions->mchat_prune();
		$this->mchat_settings->set_cfg('mchat_prune_last_gc', time());
	}

	/**
	 * Returns whether this cron task can run, given current board configuration.
	 *
	 * If warnings are set to never expire, this cron task will not run.
	 *
	 * @return bool
	 */
	public function is_runnable()
	{
		return $this->mchat_settings->cfg('mchat_prune');
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run (24 hours).
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->mchat_settings->cfg('mchat_prune_last_gc') < time() - $this->mchat_settings->cfg('mchat_prune_gc');
	}
}

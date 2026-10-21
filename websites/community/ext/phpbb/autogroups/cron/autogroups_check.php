<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\cron;

/**
 * Auto groups cron task.
 */
class autogroups_check extends \phpbb\cron\task\base
{
	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\autogroups\conditions\manager */
	protected $manager;

	/**
	 * Constructor
	 *
	 * @param \phpbb\config\config                 $config  Config object
	 * @param \phpbb\autogroups\conditions\manager $manager Auto groups condition manager object
	 * @access public
	 */
	public function __construct(\phpbb\config\config $config, \phpbb\autogroups\conditions\manager $manager)
	{
		$this->config = $config;
		$this->manager = $manager;
	}

	/**
	 * Runs this cron task.
	 *
	 * @return void
	 */
	public function run()
	{
		$this->manager->check_conditions();
		$this->config->set('autogroups_last_run', time(), false);
	}

	/**
	 * Returns whether this cron task should run now, because enough time
	 * has passed since it was last run (24 hours).
	 *
	 * @return bool
	 */
	public function should_run()
	{
		return $this->config['autogroups_last_run'] < strtotime('24 hours ago');
	}
}

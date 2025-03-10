<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\cron;

use phpbb\config\config;
use phpbb\mediaembed\cache\cache;

/**
 * Media Embed cron task.
 */
class purge_cache extends \phpbb\cron\task\base
{
	/** @var config $config */
	protected $config;

	/** @var cache $cache */
	protected $cache;

	/**
	 * Constructor
	 *
	 * @param config $config Config object
	 * @param cache  $cache  MediaEmbed cache object
	 */
	public function __construct(config $config, cache $cache)
	{
		$this->config = $config;
		$this->cache = $cache;
	}

	/**
	 * {@inheritDoc}
	 */
	public function run()
	{
		$this->cache->purge_mediaembed_cache();
		$this->config->set('mediaembed_last_gc', time(), false);
	}

	/**
	 * {@inheritDoc}
	 */
	public function is_runnable()
	{
		return (bool) $this->config['media_embed_enable_cache'];
	}

	/**
	 * {@inheritDoc}
	 */
	public function should_run()
	{
		return $this->config['mediaembed_last_gc'] < strtotime('24 hours ago');
	}
}

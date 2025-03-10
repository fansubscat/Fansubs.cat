<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\cache;

use phpbb\cache\driver\driver_interface as cache_driver;
use Symfony\Component\Finder\Finder;

/**
 * Media Embed cache handling class.
 */
class cache
{
	/** @var cache_driver */
	protected $cache;

	/** @var string Cache key used for the parser */
	protected $cache_key_parser;

	/** @var string Cache key used for the renderer */
	protected $cache_key_renderer;

	/**
	 * Constructor
	 *
	 * @param cache_driver $cache              Cache driver object
	 * @param string       $cache_key_parser   Cache key used for the parser
	 * @param string       $cache_key_renderer Cache key used for the renderer
	 */
	public function __construct(cache_driver $cache, $cache_key_parser, $cache_key_renderer)
	{
		$this->cache = $cache;
		$this->cache_key_parser = $cache_key_parser;
		$this->cache_key_renderer = $cache_key_renderer;
	}

	/**
	 * Purge cached MediaEmbed files
	 *
	 * @return void
	 */
	public function purge_mediaembed_cache()
	{
		$finder = new Finder();
		$finder
			->name('http.*')
			->in($this->cache->cache_dir)
			->files();

		foreach ($finder as $file)
		{
			$this->cache->remove_file($file->getRealPath());
		}
	}

	/**
	 * Purge cached TextFormatter files
	 *
	 * @return void
	 */
	public function purge_textformatter_cache()
	{
		$this->cache->destroy($this->cache_key_parser);
		$this->cache->destroy($this->cache_key_renderer);
	}
}

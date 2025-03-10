<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\collection;

use phpbb\extension\manager;
use phpbb\mediaembed\ext;

class customsitescollection
{
	/** @var manager */
	protected $extension_manager;

	/**
	 * Constructor
	 *
	 * @param manager $extension_manager
	 */
	public function __construct(manager $extension_manager)
	{
		$this->extension_manager = $extension_manager;
	}

	/**
	 * Get a collection of custom YAML site definition files
	 *
	 * @return array Collection of YAML site definition files
	 */
	public function get_collection()
	{
		$finder = $this->extension_manager->get_finder();

		return $finder
			->set_extensions(['phpbb/mediaembed'])
			->extension_suffix(ext::YML)
			->extension_directory('collection/sites')
			->get_files();
	}
}

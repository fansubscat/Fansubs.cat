<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2026 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\collection;

use phpbb\config\config;
use phpbb\mediaembed\ext;
use s9e\TextFormatter\Configurator;
use Symfony\Component\Yaml\Yaml;

/**
 * Apply extension site definitions to a MediaEmbed configurator.
 */
class sitescollection
{
	/** @var config */
	protected $config;

	/** @var customsitescollection */
	protected $custom_sites;

	/** @var upstreamsitescollection */
	protected $upstream_sites;

	/**
	 * Constructor.
	 *
	 * @param config                  $config         Configuration service
	 * @param customsitescollection   $custom_sites   Custom site definitions
	 * @param upstreamsitescollection $upstream_sites Upstream site definitions
	 */
	public function __construct(config $config, customsitescollection $custom_sites, upstreamsitescollection $upstream_sites)
	{
		$this->config = $config;
		$this->custom_sites = $custom_sites;
		$this->upstream_sites = $upstream_sites;
	}

	/**
	 * Apply upstream and custom site definitions.
	 *
	 * @param Configurator $configurator TextFormatter configurator
	 * @return void
	 */
	public function configure(Configurator $configurator)
	{
		if (!$this->is_phpbb4())
		{
			foreach ($this->upstream_sites->get_removed_sites() as $site_id)
			{
				unset($configurator->MediaEmbed->defaultSites[$site_id]);
			}

			foreach ($this->upstream_sites->get_collection() as $site_id => $site_config)
			{
				$configurator->MediaEmbed->defaultSites->add($site_id, $site_config);
			}
		}

		foreach ($this->custom_sites->get_collection() as $site)
		{
			$name = basename($site, ext::YML);
			$configurator->MediaEmbed->defaultSites->add($name, Yaml::parseFile($site));
		}
	}

	/**
	 * Check whether current phpBB version is 4.0 or newer.
	 *
	 * @return bool
	 */
	public function is_phpbb4()
	{
		return phpbb_version_compare($this->config['version'], '4.0.0-a1', '>=');
	}
}

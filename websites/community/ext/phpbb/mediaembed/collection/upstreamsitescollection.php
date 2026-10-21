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

use phpbb\mediaembed\collection\generated\upstream_sites;

class upstreamsitescollection
{
	/**
	 * Get changed upstream definitions with hand-maintained compatibility patches.
	 *
	 * @return array
	 */
	public function get_collection()
	{
		$patches = compatibility_sites::PATCHES;
		$sites = upstream_sites::SITES;

		foreach ($patches as $site_id => $patch)
		{
			foreach ($patch['unset'] ?? [] as $path)
			{
				$this->unset_path($sites[$site_id], $path);
			}
			foreach ($patch['replace'] ?? [] as $path => $value)
			{
				$this->set_path($sites[$site_id], $path, $value);
			}
			foreach ($patch['append'] ?? [] as $path => $values)
			{
				$current = $this->get_path($sites[$site_id], $path);
				$this->set_path($sites[$site_id], $path, array_merge((array) $current, $values));
			}
		}

		return $sites;
	}

	private function get_path(array $definition, $path)
	{
		foreach (explode('.', $path) as $key)
		{
			$definition = $definition[$key];
		}

		return $definition;
	}

	private function set_path(array &$definition, $path, $value)
	{
		$keys = explode('.', $path);
		$last_key = array_pop($keys);
		foreach ($keys as $key)
		{
			$definition =& $definition[$key];
		}
		$definition[$last_key] = $value;
	}

	private function unset_path(array &$definition, $path)
	{
		$keys = explode('.', $path);
		$last_key = array_pop($keys);
		foreach ($keys as $key)
		{
			$definition =& $definition[$key];
		}
		unset($definition[$last_key]);
	}

	/**
	 * Get site IDs removed by target TextFormatter release.
	 *
	 * @return array
	 */
	public function get_removed_sites()
	{
		return upstream_sites::REMOVED_SITES;
	}

	/**
	 * Get generated collection metadata.
	 *
	 * @return array
	 */
	public function get_metadata()
	{
		return [
			'base_version' => upstream_sites::BASE_VERSION,
			'target_version' => upstream_sites::TARGET_VERSION,
		];
	}
}

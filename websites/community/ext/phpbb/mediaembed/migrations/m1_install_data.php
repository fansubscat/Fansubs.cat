<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration 1: Install data to the database
 */
class m1_install_data extends \phpbb\db\migration\container_aware_migration
{
	public function effectively_installed()
	{
		return $this->config->offsetExists('media_embed_bbcode');
	}

	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v320\v320rc1'];
	}

	public function update_data()
	{
		return [
			['config.add', ['media_embed_bbcode', 1]],

			['module.add', ['acp', 'ACP_CAT_POSTING', [
				'module_langname'	=> 'ACP_PHPBB_MEDIA_EMBED',
				'after'				=> 'ACP_MESSAGES',
			]]],

			['module.add', ['acp', 'ACP_PHPBB_MEDIA_EMBED', [
				'module_basename'	=> '\phpbb\mediaembed\acp\main_module',
				'modes'				=> ['settings', 'manage'],
			]]],

			['config_text.add', ['media_embed_sites', call_user_func([$this, 'media_sites'])]],
		];
	}

	/**
	 * Get a JSON encoded string of an array of all available MediaEmbed
	 * sites that can be enabled without conflicting with existing BBCodes.
	 *
	 * @return string
	 */
	public function media_sites()
	{
		/** @var \s9e\TextFormatter\Configurator $configurator */
		$configurator = $this->container->get('text_formatter.s9e.factory')->get_configurator();
		$sites = array_filter(array_keys(iterator_to_array($configurator->MediaEmbed->defaultSites)), function ($siteId) use ($configurator) {
			return !isset($configurator->BBCodes[$siteId]);
		});

		return json_encode(array_values($sites));
	}
}

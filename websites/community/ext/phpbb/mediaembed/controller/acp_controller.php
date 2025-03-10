<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\controller;

use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\language\language;
use phpbb\log\log;
use phpbb\mediaembed\cache\cache as media_cache;
use phpbb\request\request;
use phpbb\template\template;
use phpbb\textformatter\s9e\factory as textformatter;
use phpbb\user;

/**
 * phpBB Media Embed ACP module controller.
 */
class acp_controller implements acp_controller_interface
{
	/** @var config $config */
	protected $config;

	/** @var db_text $config_text */
	protected $config_text;

	/** @var language $language */
	protected $language;

	/** @var log $log */
	protected $log;

	/** @var media_cache $media_cache */
	protected $media_cache;

	/** @var request $request */
	protected $request;

	/** @var template $template */
	protected $template;

	/** @var textformatter $textformatter */
	protected $textformatter;

	/** @var user $user */
	protected $user;

	/** @var array $enabled_sites */
	protected $enabled_sites;

	/** @var string $u_action */
	public $u_action;

	/** @var array An array of errors */
	protected $errors = [];

	/**
	 * Constructor
	 */
	public function __construct(config $config, db_text $config_text, language $language, log $log, media_cache $media_cache, request $request, template $template, textformatter $textformatter, user $user)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->language = $language;
		$this->log = $log;
		$this->media_cache = $media_cache;
		$this->request = $request;
		$this->template = $template;
		$this->textformatter = $textformatter;
		$this->user = $user;

		$this->language->add_lang('acp', 'phpbb/mediaembed');
	}

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 */
	public function set_page_url($u_action)
	{
		$this->u_action = $u_action;
	}

	/**
	 * Add settings template vars to the form
	 */
	public function display_settings()
	{
		$this->template->assign_vars([
			'S_MEDIA_EMBED_BBCODE'       => $this->config['media_embed_bbcode'],
			'S_MEDIA_EMBED_ALLOW_SIG'    => $this->config['media_embed_allow_sig'],
			'S_MEDIA_EMBED_PARSE_URLS'   => $this->config['media_embed_parse_urls'],
			'S_MEDIA_EMBED_ENABLE_CACHE' => $this->config['media_embed_enable_cache'],
			'S_MEDIA_EMBED_FULL_WIDTH'   => $this->config['media_embed_full_width'],
			'S_MEDIA_EMBED_MAX_WIDTHS'   => $this->get_media_embed_max_width(),
			'U_ACTION'                   => $this->u_action,
		]);
	}

	/**
	 * Add manage sites template vars to the form
	 */
	public function display_manage()
	{
		$this->template->assign_vars([
			'MEDIA_SITES' => $this->get_sites(),
			'U_ACTION'    => $this->u_action,
			'ERRORS'      => $this->errors,
		]);
	}

	/**
	 * Save settings data to the database
	 *
	 * @return array Message and code for trigger error
	 */
	public function save_settings()
	{
		$this->config->set('media_embed_bbcode', $this->request->variable('media_embed_bbcode', 0));
		$this->config->set('media_embed_allow_sig', $this->request->variable('media_embed_allow_sig', 0));
		$this->config->set('media_embed_parse_urls', $this->request->variable('media_embed_parse_urls', 0));
		$this->config->set('media_embed_enable_cache', $this->request->variable('media_embed_enable_cache', 0));
		$this->config->set('media_embed_full_width', $this->request->variable('media_embed_full_width', 0));

		$this->set_media_embed_max_width();

		$this->media_cache->purge_textformatter_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_SETTINGS');

		if (count($this->errors))
		{
			return [
				'code' => E_USER_WARNING,
				'message' => $this->language->lang('ACP_MEDIA_ERROR_MSG', implode('<br>', $this->errors))
			];
		}

		return [
			'code' => E_USER_NOTICE,
			'message' => $this->language->lang('CONFIG_UPDATED')
		];
	}

	/**
	 * Save site managed data to the database
	 *
	 * @return array Message and code for trigger error
	 */
	public function save_manage()
	{
		$this->config_text->set('media_embed_sites', json_encode($this->request->variable('mark', [''])));

		$this->media_cache->purge_textformatter_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_MANAGE');

		return [
			'code' => E_USER_NOTICE,
			'message' => $this->language->lang('CONFIG_UPDATED')
		];
	}

	/**
	 * Purge all Media Embed cache files
	 */
	public function purge_mediaembed_cache()
	{
		$this->media_cache->purge_mediaembed_cache();

		$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_PHPBB_MEDIA_EMBED_CACHE_PURGED');

		return [
			'code' => E_USER_NOTICE,
			'message' => $this->language->lang('PURGE_CACHE_SUCCESS')
		];

	}

	/**
	 * Get a list of available sites
	 *
	 * @return array An array of available sites
	 */
	protected function get_sites()
	{
		$sites = [];

		$configurator = $this->textformatter->get_configurator();
		foreach ($configurator->MediaEmbed->defaultSites as $siteId => $siteConfig)
		{
			$disabled = isset($configurator->BBCodes[$siteId]);
			$sites[$siteId] = [
				'id'       => $siteId,
				'name'     => $siteConfig['name'],
				'title'    => $this->language->lang($disabled ? 'ACP_MEDIA_SITE_DISABLED' : 'ACP_MEDIA_SITE_TITLE', $siteId),
				'enabled'  => in_array($siteId, $this->get_enabled_sites()),
				'disabled' => $disabled,
			];
		}

		ksort($sites);

		$this->errors = array_diff($this->get_enabled_sites(), array_keys($sites));

		return $sites;
	}

	/**
	 * Get enabled media sites stored in the database
	 *
	 * @return array An array of enabled sites
	 */
	protected function get_enabled_sites()
	{
		if ($this->enabled_sites === null)
		{
			$sites = json_decode($this->config_text->get('media_embed_sites'), true);
			$this->enabled_sites = is_array($sites) ? $sites : [];
		}

		return $this->enabled_sites;
	}

	/**
	 * Store the media embed max width value to the config text as JSON,
	 * with some basic input validation and array formatting.
	 */
	protected function set_media_embed_max_width()
	{
		$input = $this->request->variable('media_embed_max_width', '');

		if ($input)
		{
			$lines = array_unique(explode("\n", $input));

			foreach ($lines as $key => $line)
			{
				$parts = explode(':', $line);
				if (count($parts) !== 2)
				{
					unset($lines[$key]);
					continue;
				}

				$lines[$key] = array_combine(['site', 'width'], array_map('trim', $parts));
			}

			$input = json_encode(array_filter($lines, [$this, 'validate']));
		}

		$this->config_text->set('media_embed_max_width', strtolower($input));
	}

	/**
	 * Get the stored media embed max width data from config text and convert
	 * from JSON to the formatting used in the ACP textarea field.
	 *
	 * @return string
	 */
	protected function get_media_embed_max_width()
	{
		$config = json_decode($this->config_text->get('media_embed_max_width'), true);

		if ($config)
		{
			foreach ($config as &$item)
			{
				$item = implode(':', $item);
			}

			unset($item);
		}

		return $config ? implode("\n", $config) : '';
	}

	/**
	 * Validate the input for media embed max widths
	 * 'site' key value should be a word
	 * 'width' key value should be a number appended with either px or %
	 *
	 * @param array $input The array to check
	 * @return bool True if array contains valid values, false if not
	 * @throws \Exception
	 */
	protected function validate($input)
	{
		// First, lets get all the available media embed site IDs
		static $default_sites;

		if (null === $default_sites)
		{
			$configurator = $this->textformatter->get_configurator();
			$default_sites = array_keys(iterator_to_array($configurator->MediaEmbed->defaultSites));
		}

		// Next create an array to hold any errors
		$errors = [];

		// Check to see if the site id provided exists in Media Embed
		if (!in_array($input['site'], $default_sites))
		{
			$errors[] = $this->language->lang('ACP_MEDIA_INVALID_SITE', $input['site'], $input['width']);
		}

		// Check to see if the width provided is a valid number followed px or %
		if (!preg_match('/^\d+(?:%|px)$/', $input['width']))
		{
			$errors[] = $this->language->lang('ACP_MEDIA_INVALID_WIDTH', $input['site'], $input['width']);
		}

		// Update the errors object with any new errors
		$this->errors = array_merge($this->errors, $errors);

		return empty($errors);
	}
}

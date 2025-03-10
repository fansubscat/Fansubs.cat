<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed;

class ext extends \phpbb\extension\base
{
	/**
	 * @var string Minimum requirements:
	 * phpBB 3.3.2 because using role_exists in migrations
	 * phpBB 3.3.2 because TextFormatter 2.7.5 supports clyp.it and codepen.com
	 */
	public const PHPBB_MINIMUM = '3.3.2';

	/**
	 * @var string YAML file extension
	 */
	public const YML = '.yml';

	/**
	 * @var array An array of installation error messages
	 */
	protected $errors = [];

	/**
	 * {@inheritDoc}
	 */
	public function is_enableable()
	{
		return $this->check_phpbb_version()
			->check_s9e_mediaembed()
			->result();
	}

	/**
	 * Check the installed phpBB version meets this
	 * extension's requirements.
	 *
	 * @return \phpbb\mediaembed\ext
	 */
	protected function check_phpbb_version()
	{
		if (phpbb_version_compare(PHPBB_VERSION, self::PHPBB_MINIMUM, '<'))
		{
			$this->errors[] = 'PHPBB_VERSION_ERROR';
		}

		return $this;
	}

	/**
	 * Check if s9e MediaEmbed extension for phpBB is installed
	 * (it must NOT be to enable this extension).
	 *
	 * @return \phpbb\mediaembed\ext
	 */
	protected function check_s9e_mediaembed()
	{
		$ext_manager = $this->container->get('ext.manager');

		if ($ext_manager->is_enabled('s9e/mediaembed'))
		{
			$this->errors[] = 'S9E_MEDIAEMBED_ERROR';
		}

		return $this;
	}

	/**
	 * Return the is enableable result. Either true, or the best enable failed
	 * response for the current phpBB environment: array of error messages
	 * in phpBB 3.3 or newer, false otherwise.
	 *
	 * @return array|bool
	 */
	protected function result()
	{
		if (empty($this->errors))
		{
			return true;
		}

		if (phpbb_version_compare(PHPBB_VERSION, '3.3.0-b1', '>='))
		{
			$language = $this->container->get('language');
			$language->add_lang('install', 'phpbb/mediaembed');
			return array_map([$language, 'lang'], $this->errors);
		}

		return false;
	}
}

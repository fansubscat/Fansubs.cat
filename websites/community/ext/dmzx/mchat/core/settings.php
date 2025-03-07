<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\core;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\event\dispatcher_interface;
use phpbb\exception\runtime_exception;
use phpbb\language\language;
use phpbb\user;

class settings
{
	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var config */
	protected $config;

	/** @var db_text */
	protected $config_text;

	/** @var auth */
	protected $auth;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var string */
	protected $mchat_table;

	/** @var string */
	protected $mchat_log_table;

	/** @var string */
	protected $mchat_sessions_table;

	/** @var string */
	protected $board_url;

	/**
	 * Keys for global settings that only the administrator is allowed to modify.
	 * The values are stored in the phpbb_config table.
	 *
	 * @var array
	 */
	protected $global_settings;

	/**
	 * Keys for global text settings that only the administrator is allowed to modify.
	 * The values are stored in the phpbb_config_text table.
	 *
	 * @var array
	 */
	protected $global_text_settings;

	/**
	 * Values for global text settings.
	 *
	 * @var array
	 */
	protected $global_text_values;

	/**
	 * Keys for user-specific settings for which the administrator can set default
	 * values as well as adjust permissions to allow users to customize them.
	 * The values are stored in the phpbb_users table as well as the phpbb_config table.
	 * If a user has permission to customize a setting, the value in the phpbb_users
	 * table is used, otherwise the value in the phpbb_config table is used.
	 *
	 * @var array
	 */
	protected $ucp_settings;

	/**
	 * Prune modes listed in the ACP. For values other than messages the key is the
	 * amount of hours that is later multiplied with the value that is set in the ACP.
	 *
	 * @var array
	 */
	public $prune_modes = [
		0	=> 'messages',
		1	=> 'hours',
		24	=> 'days',
		168	=> 'weeks',
	];

	/**
	 * Possible values of the global setting mchat_archive_sort
	 */
	const ARCHIVE_SORT_TOP_BOTTOM = 0;
	const ARCHIVE_SORT_BOTTOM_TOP = 1;
	const ARCHIVE_SORT_USER = 2;

	/**
	 * Constructor
	 *
	 * @param user					$user
	 * @param language				$lang
	 * @param config				$config
	 * @param db_text				$config_text
	 * @param auth					$auth
	 * @param dispatcher_interface	$dispatcher
	 * @param string				$root_path
	 * @param string				$php_ext
	 * @param string				$mchat_table
	 * @param string				$mchat_log_table
	 * @param string				$mchat_sessions_table
	 */
	public function __construct(
		user $user,
		language $lang,
		config $config,
		db_text $config_text,
		auth $auth,
		dispatcher_interface $dispatcher,
		$root_path,
		$php_ext,
		$mchat_table,
		$mchat_log_table,
		$mchat_sessions_table
	)
	{
		$this->user					= $user;
		$this->lang					= $lang;
		$this->config				= $config;
		$this->config_text			= $config_text;
		$this->auth					= $auth;
		$this->dispatcher			= $dispatcher;
		$this->root_path			= $root_path;
		$this->php_ext				= $php_ext;
		$this->mchat_table			= $mchat_table;
		$this->mchat_log_table		= $mchat_log_table;
		$this->mchat_sessions_table	= $mchat_sessions_table;
	}

	/**
	 * @return array
	 */
	public function initialize_global_settings()
	{
		$global_settings = [
			'mchat_archive_sort'			=> ['default' => self::ARCHIVE_SORT_BOTTOM_TOP],
			'mchat_bbcode_disallowed'		=> ['default' => '',	'validation' => ['string', false, 0, 255]],
			'mchat_custom_height'			=> ['default' => 350,	'validation' => ['num', false, 50, 1000]],
			'mchat_custom_page'				=> ['default' => 1],
			'mchat_edit_delete_limit'		=> ['default' => 0],
			'mchat_flood_time'				=> ['default' => 0,		'validation' => ['num', false, 0, 3600]],
			'mchat_flood_messages'			=> ['default' => 0,		'validation' => ['num', false, 0, 100]],
			'mchat_index_height'			=> ['default' => 250,	'validation' => ['num', false, 50, 1000]],
			'mchat_live_updates'			=> ['default' => 1],
			'mchat_log_enabled'				=> ['default' => 1],
			'mchat_max_input_height'		=> ['default' => 150,	'validation' => ['num', false, 0, 1000]],
			'mchat_max_message_lngth'		=> ['default' => 500],
			'mchat_message_num_archive'		=> ['default' => 25,	'validation' => ['num', false, 10, 100]],
			'mchat_message_num_custom'		=> ['default' => 10],
			'mchat_message_num_index'		=> ['default' => 10],
			'mchat_navbar_link_count'		=> ['default' => 1],
			'mchat_override_min_post_chars' => ['default' => 0],
			'mchat_override_smilie_limit'	=> ['default' => 0],
			'mchat_posts_auth_check'		=> ['default' => 0],
			'mchat_posts_edit'				=> ['default' => 0],
			'mchat_posts_quote'				=> ['default' => 0],
			'mchat_posts_reply'				=> ['default' => 0],
			'mchat_posts_topic'				=> ['default' => 0],
			'mchat_posts_login'				=> ['default' => 0],
			'mchat_prune'					=> ['default' => 0],
			'mchat_prune_gc'				=> ['default' => strtotime('1 day', 0)],
			'mchat_prune_mode'				=> ['default' => 0],
			'mchat_prune_num'				=> ['default' => 0],
			'mchat_refresh'					=> ['default' => 10,	'validation' => ['num', false, 2, 3600]],
			'mchat_timeout'					=> ['default' => 0,		'validation' => ['num', false, 0, (int) $this->cfg('session_length')]],
			'mchat_whois_refresh'			=> ['default' => 60,	'validation' => ['num', false, 10, 300]],
		];

		/**
		 * Event to modify global settings data
		 *
		 * @event dmzx.mchat.global_settings_modify
		 * @var array	global_settings		Array containing global settings data
		 * @since 2.0.0-RC7
		 */
		$vars = [
			'global_settings',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.global_settings_modify', compact($vars)));

		return $global_settings;
	}

	/**
	 * @return array
	 */
	public function initialize_global_text_settings()
	{
		$global_text_settings = [
			'mchat_rules'					=> ['default' => ''],
			'mchat_static_message'			=> ['default' => ''],
		];

		/**
		 * Event to modify global text settings data
		 *
		 * @event dmzx.mchat.global_text_settings_modify
		 * @var array	global_text_settings	Array containing global text settings data
		 * @since 2.0.2
		 */
		$vars = [
			'global_text_settings',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.global_text_settings_modify', compact($vars)));

		return $global_text_settings;
	}

	/**
	 * @return array
	 */
	public function initialize_ucp_settings()
	{
		$ucp_settings = [
			'mchat_avatars'					=> ['default' => 1],
			'mchat_capital_letter'			=> ['default' => 1],
			'mchat_character_count'			=> ['default' => 1],
			'mchat_date'					=> ['default' => 'D M d, Y g:i a', 'validation' => ['string', false, 0, 64]],
			'mchat_index'					=> ['default' => 1],
			'mchat_location'				=> ['default' => 1],
			'mchat_message_top'				=> ['default' => 1],
			'mchat_posts'					=> ['default' => 1],
			'mchat_relative_time'			=> ['default' => 1],
			'mchat_sound'					=> ['default' => 1],
			'mchat_stats_index'				=> ['default' => 0],
			'mchat_whois_index'				=> ['default' => 1],
		];

		/**
		 * Event to modify UCP settings data
		 *
		 * @event dmzx.mchat.ucp_settings_modify
		 * @var	array	ucp_settings		Array containing UCP settings data
		 * @since 2.0.0-RC7
		 */
		$vars = [
			'ucp_settings',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.ucp_settings_modify', compact($vars)));

		return $ucp_settings;
	}

	/**
	 * @return array
	 */
	public function global_settings()
	{
		if (empty($this->global_settings))
		{
			$this->global_settings = $this->initialize_global_settings();
		}

		return $this->global_settings;
	}

	/**
	 * @return array
	 */
	public function global_text_settings()
	{
		if (empty($this->global_text_settings))
		{
			$this->global_text_settings = $this->initialize_global_text_settings();
		}

		return $this->global_text_settings;
	}

	/**
	 * @return array
	 */
	public function global_text_values()
	{
		if (empty($this->global_text_values))
		{
			$global_text_values = $this->config_text->get_array(array_keys($this->global_text_settings()));

			/**
			 * Event to modify global text values
			 *
			 * @event dmzx.mchat.global_text_values_modify
			 * @var array	global_text_values	Array containing global text values
			 * @since 2.0.2
			 */
			$vars = [
				'global_text_values',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.global_text_values_modify', compact($vars)));

			$this->global_text_values = $global_text_values;
		}

		return $this->global_text_values;
	}

	/**
	 * @return array
	 */
	public function ucp_settings()
	{
		if (empty($this->ucp_settings))
		{
			$this->ucp_settings = $this->initialize_ucp_settings();
		}

		return $this->ucp_settings;
	}

	/**
	 * @param string $config
	 * @param bool $force_global
	 * @return string
	 */
	public function cfg($config, $force_global = false)
	{
		return $this->cfg_user($config, $this->user->data, $this->auth, $force_global);
	}

	/**
	 * @param string $config
	 * @param array $user_data
	 * @param auth $auth
	 * @param bool $force_global
	 * @return string
	 */
	public function cfg_user($config, $user_data, $auth, $force_global = false)
	{
		if (!$force_global)
		{
			$ucp_settings = $this->ucp_settings();

			if (isset($ucp_settings[$config]) && $auth->acl_get('u_' . $config))
			{
				return $user_data['user_' . $config];
			}
		}

		if (isset($this->config[$config]))
		{
			return $this->config[$config];
		}

		$global_text_settings = $this->global_text_settings();

		if (isset($global_text_settings[$config]))
		{
			$global_text_values = $this->global_text_values();
			return $global_text_values[$config];
		}

		throw new runtime_exception();
	}

	/**
	 * @param string $config
	 * @param mixed $value
	 * @param bool $volatile
	 */
	public function set_cfg($config, $value, $volatile = false)
	{
		$global_text_settings = $this->global_text_settings();

		if (isset($global_text_settings[$config]))
		{
			$this->global_text_values[$config] = $value;

			if (!$volatile)
			{
				$this->config_text->set($config, $value);
			}

			return;
		}

		if ($volatile)
		{
			$this->config[$config] = $value;
		}
		else
		{
			$this->config->set($config, $value);
		}
	}

	/**
	 * @return string
	 */
	public function get_table_mchat()
	{
		return $this->mchat_table;
	}

	/**
	 * @return string
	 */
	public function get_table_mchat_log()
	{
		return $this->mchat_log_table;
	}

	/**
	 * @return string
	 */
	public function get_table_mchat_sessions()
	{
		return $this->mchat_sessions_table;
	}

	/**
	 * @param string $selected
	 * @return array
	 */
	public function get_date_template_data($selected)
	{
		$dateformat_options = '';
		$dateformats = $this->lang->lang_raw('dateformats');

		foreach (array_keys($dateformats) as $format)
		{
			$dateformat_options .= '<option value="' . $format . '"' . (($format == $selected) ? ' selected="selected"' : '') . '>';
			$dateformat_options .= $this->user->format_date(time(), $format, false) . ((strpos($format, '|') !== false) ? $this->lang->lang('VARIANT_DATE_SEPARATOR') . $this->user->format_date(time(), $format, true) : '');
			$dateformat_options .= '</option>';
		}

		$s_custom = false;

		$dateformat_options .= '<option value="custom"';
		if (!isset($dateformats[$selected]))
		{
			$dateformat_options .= ' selected="selected"';
			$s_custom = true;
		}
		$dateformat_options .= '>' . $this->lang->lang('MCHAT_CUSTOM_DATEFORMAT') . '</option>';

		$ucp_settings = $this->ucp_settings();

		return [
			'S_MCHAT_DATEFORMAT_OPTIONS'	=> $dateformat_options,
			'MCHAT_DEFAULT_DATEFORMAT'		=> $ucp_settings['mchat_date']['default'],
			'S_MCHAT_CUSTOM_DATEFORMAT'		=> $s_custom,
		];
	}

	/**
	 * @return string
	 */
	public function get_enabled_post_notifications_lang()
	{
		$enabled_notifications_lang = [];

		foreach (['topic', 'reply', 'quote', 'edit', 'login'] as $notification)
		{
			if ($this->cfg('mchat_posts_' . $notification))
			{
				$enabled_notifications_lang[] = $this->lang->lang('MCHAT_POSTS_' . strtoupper($notification));
			}
		}

		return implode($this->lang->lang('COMMA_SEPARATOR'), $enabled_notifications_lang);
	}

	/**
	 * @return string
	 */
	public function get_current_page()
	{
		$page = $this->user->page['page_name'];

		// Remove app.php if URL rewriting is enabled in the ACP
		if ($this->config['enable_mod_rewrite'])
		{
			$app_php = 'app.' . $this->php_ext . '/';

			if (($app_position = strpos($page, $app_php)) !== false)
			{
				$page = substr($page, $app_position + strlen($app_php));
			}
		}

		return generate_board_url() . '/' . $page;
	}

	/**
	 * @param string $path
	 * @param bool $absolute_url
	 * @param bool $append_ext
	 * @return string
	 */
	public function url($path, $absolute_url = false, $append_ext = true)
	{
		if ($absolute_url && !$this->board_url)
		{
			$this->board_url = generate_board_url() . '/';
		}

		$url = ($absolute_url ? $this->board_url : $this->root_path) . $path;

		if ($append_ext)
		{
			$url .= '.' . $this->php_ext;
		}

		return $url;
	}

	/**
	 * @param string $file
	 * @param string $function
	 */
	public function include_functions($file, $function)
	{
		if (!function_exists($function))
		{
			include($this->root_path . 'includes/functions_' . $file . '.' . $this->php_ext);
		}
	}
}

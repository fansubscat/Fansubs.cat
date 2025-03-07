<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\controller;

use dmzx\mchat\core\functions;
use dmzx\mchat\core\settings;
use phpbb\cache\driver\driver_interface as cache_interface;
use phpbb\config\db_text as config_text;
use phpbb\db\driver\driver_interface as db_interface;
use phpbb\event\dispatcher_interface;
use phpbb\language\language;
use phpbb\log\log_interface;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;

class acp_controller
{
	/** @var functions */
	protected $mchat_functions;

	/** @var template */
	protected $template;

	/** @var log_interface */
	protected $log;

	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var db_interface */
	protected $db;

	/** @var config_text */
	protected $config_text;

	/** @var cache_interface */
	protected $cache;

	/** @var request_interface */
	protected $request;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var settings */
	protected $settings;

	/**
	 * Constructor
	 *
	 * @param functions				$mchat_functions
	 * @param template				$template
	 * @param log_interface			$log
	 * @param user					$user
	 * @param language				$lang
	 * @param db_interface			$db
	 * @param config_text			$config_text
	 * @param cache_interface		$cache
	 * @param request_interface		$request
	 * @param dispatcher_interface 	$dispatcher
	 * @param settings				$settings
	 */
	public function __construct(
		functions $mchat_functions,
		template $template,
		log_interface $log,
		user $user,
		language $lang,
		db_interface $db,
		config_text $config_text,
		cache_interface $cache,
		request_interface $request,
		dispatcher_interface $dispatcher,
		settings $settings
	)
	{
		$this->mchat_functions	= $mchat_functions;
		$this->template			= $template;
		$this->log				= $log;
		$this->user				= $user;
		$this->lang				= $lang;
		$this->db				= $db;
		$this->config_text		= $config_text;
		$this->cache			= $cache;
		$this->request			= $request;
		$this->dispatcher		= $dispatcher;
		$this->settings			= $settings;
	}

	/**
	 * Display the options the admin can configure for this extension
	 *
	 * @param string $u_action
	 */
	public function globalsettings($u_action)
	{
		add_form_key('acp_mchat');

		$error = [];

		$is_founder = $this->user->data['user_type'] == USER_FOUNDER;

		$settings = array_merge($this->settings->global_settings(), $this->settings->global_text_settings());

		if ($this->request->is_set_post('submit'))
		{
			$mchat_new_config = [];
			$validation = [];
			foreach ($settings as $config_name => $config_data)
			{
				$default = $this->settings->cfg($config_name);
				settype($default, gettype($config_data['default']));
				$mchat_new_config[$config_name] = $this->request->variable($config_name, $default, is_string($default));
				if (isset($config_data['validation']))
				{
					$validation[$config_name] = $config_data['validation'];
				}
			}

			// Enable Emojis and rich text in Rules and Static Message
			$mchat_new_config['mchat_rules'] = utf8_encode_ncr($mchat_new_config['mchat_rules']);
			$mchat_new_config['mchat_static_message'] = utf8_encode_ncr($mchat_new_config['mchat_static_message']);

			// Remove leading & trailing | characters to not break allowed BBCodes
			$mchat_new_config['mchat_bbcode_disallowed'] = trim($mchat_new_config['mchat_bbcode_disallowed'], '|');

			if (!$is_founder)
			{
				// Don't allow changing pruning settings for non founders
				unset($mchat_new_config['mchat_prune']);
				unset($mchat_new_config['mchat_prune_gc']);
				unset($mchat_new_config['mchat_prune_mode']);
				unset($mchat_new_config['mchat_prune_num']);

				// Don't allow changing log settings for non founders
				unset($mchat_new_config['mchat_log_enabled']);
			}

			$this->settings->include_functions('user', 'validate_data');

			$error = array_merge($error, validate_data($mchat_new_config, $validation));

			if (!check_form_key('acp_mchat'))
			{
				$error[] = 'FORM_INVALID';
			}

			/**
			 * Event to modify ACP global settings data before they are updated
			 *
			 * @event dmzx.mchat.acp_globalsettings_update_data
			 * @var array	mchat_new_config		Array containing the ACP settings data that is about to be sent to the database
			 * @var array	error					Array with error lang keys
			 * @since 2.0.0-RC7
			 */
			$vars = [
				'mchat_new_config',
				'error',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.acp_globalsettings_update_data', compact($vars)));

			if (!$error)
			{
				// Set the options the user configured
				foreach ($mchat_new_config as $config_name => $config_value)
				{
					$this->settings->set_cfg($config_name, $config_value);
				}

				// Add an entry into the log table
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MCHAT_CONFIG_UPDATE', false, [$this->user->data['username']]);

				trigger_error($this->lang->lang('MCHAT_CONFIG_SAVED') . adm_back_link($u_action));
			}

			// Replace "error" strings with their real, localised form
			$error = array_map([$this->lang, 'lang'], $error);
		}

		if (!$error)
		{
			if ($is_founder && $this->request->is_set_post('mchat_purge') && $this->request->variable('mchat_purge_confirm', false) && check_form_key('acp_mchat'))
			{
				/**
				 * Event that is triggered right before all mChat messages are
				 * deleted when using the Delete all messages button in the ACP
				 *
				 * @event dmzx.mchat.purge_before
				 * @since 2.1.0-RC1
				 */
				$this->dispatcher->dispatch('dmzx.mchat.purge_before');

				$this->db->sql_query('DELETE FROM ' . $this->settings->get_table_mchat());
				$this->db->sql_query('DELETE FROM ' . $this->settings->get_table_mchat_log());
				$this->cache->destroy('sql', $this->settings->get_table_mchat_log());
				$this->mchat_functions->phpbb_log('LOG_MCHAT_TABLE_PURGED');
				trigger_error($this->lang->lang('MCHAT_PURGED') . adm_back_link($u_action));
			}
			else if ($is_founder && $this->request->is_set_post('mchat_prune_now') && $this->request->variable('mchat_prune_now_confirm', false) && check_form_key('acp_mchat'))
			{
				$num_pruned_messages = count($this->mchat_functions->mchat_prune());
				trigger_error($this->lang->lang('MCHAT_PRUNED', $num_pruned_messages) . adm_back_link($u_action));
			}
		}

		$template_data = [
			'MCHAT_ERROR'							=> implode('<br>', $error),
			'MCHAT_VERSION'							=> $this->settings->cfg('mchat_version'),
			'MCHAT_FOUNDER'							=> $is_founder,
			'S_MCHAT_PRUNE_MODE_OPTIONS'			=> $this->get_prune_mode_options($this->settings->cfg('mchat_prune_mode')),
			'L_MCHAT_BBCODES_DISALLOWED_EXPLAIN'	=> $this->lang->lang('MCHAT_BBCODES_DISALLOWED_EXPLAIN', '<a href="' . append_sid($this->settings->url('adm/index'), ['i' => 'bbcodes']) . '">', '</a>'),
			'L_MCHAT_TIMEOUT_EXPLAIN'				=> $this->lang->lang('MCHAT_TIMEOUT_EXPLAIN','<a href="' . append_sid($this->settings->url('adm/index'), ['i' => 'board', 'mode' => 'load']) . '">', '</a>', $this->settings->cfg('session_length')),
			'S_REPARSER_ACTIVE'						=> $this->is_reparser_active('dmzx.mchat.text_reparser.mchat_messages'),
			'U_ACTION'								=> $u_action,
		];

		foreach (array_keys($settings) as $key)
		{
			$template_data[strtoupper($key)] = $this->settings->cfg($key);
		}

		/**
		 * Event to modify ACP global settings template data
		 *
		 * @event dmzx.mchat.acp_globalsettings_modify_template_data
		 * @var array	template_data	Array containing the template data for the ACP settings
		 * @var array	error			Array with error lang keys
		 * @since 2.0.0-RC7
		 */
		$vars = [
			'template_data',
			'error',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.acp_globalsettings_modify_template_data', compact($vars)));

		$this->template->assign_vars($template_data);
	}

	/**
	 * @param string $u_action
	 */
	public function globalusersettings($u_action)
	{
		add_form_key('acp_mchat');

		$error = [];

		if ($this->request->is_set_post('submit'))
		{
			$mchat_new_config = [];
			$validation = [];
			foreach ($this->settings->ucp_settings() as $config_name => $config_data)
			{
				$default = $this->settings->cfg($config_name, true);
				settype($default, gettype($config_data['default']));
				$mchat_new_config[$config_name] = $this->request->variable('user_' . $config_name, $default, is_string($default));

				if (isset($config_data['validation']))
				{
					$validation[$config_name] = $config_data['validation'];
				}
			}

			$this->settings->include_functions('user', 'validate_data');

			$error = array_merge($error, validate_data($mchat_new_config, $validation));

			if (!check_form_key('acp_mchat'))
			{
				$error[] = 'FORM_INVALID';
			}

			$mchat_new_user_config = [];

			if ($this->request->variable('mchat_overwrite', 0) && $this->request->variable('mchat_overwrite_confirm', 0))
			{
				foreach ($mchat_new_config as $config_name => $config_value)
				{
					$mchat_new_user_config['user_' . $config_name] = $config_value;
				}
			}

			/**
			 * Event to modify ACP global user settings data before they are updated
			 *
			 * @event dmzx.mchat.acp_globalusersettings_update_data
			 * @var array	mchat_new_config		Array containing the ACP global user settings data that is about to be sent to the database
			 * @var array	mchat_new_user_config	Array containing the user settings data when overwriting all user settings
			 * @var array	error					Array with error lang keys
			 * @since 2.0.0-RC7
			 */
			$vars = [
				'mchat_new_config',
				'mchat_new_user_config',
				'error',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.acp_globalusersettings_update_data', compact($vars)));

			if (!$error)
			{
				if ($mchat_new_user_config)
				{
					$sql = 'UPDATE ' . USERS_TABLE . ' SET ' . $this->db->sql_build_array('UPDATE', $mchat_new_user_config);
					$this->db->sql_query($sql);
				}

				// Set the options the user configured
				foreach ($mchat_new_config as $config_name => $config_value)
				{
					$this->settings->set_cfg($config_name, $config_value);
				}

				// Add an entry into the log table
				$this->log->add('admin', $this->user->data['user_id'], $this->user->ip, 'LOG_MCHAT_CONFIG_UPDATE', false, [$this->user->data['username']]);

				trigger_error($this->lang->lang('MCHAT_CONFIG_SAVED') . adm_back_link($u_action));
			}

			// Replace "error" strings with their real, localised form
			$error = array_map([$this->lang, 'lang'], $error);
		}

		// Force global date format for $selected_date value, not user-specific
		$selected_date = $this->settings->cfg('mchat_date', true);
		$template_data = $this->settings->get_date_template_data($selected_date);

		foreach (array_keys($this->settings->ucp_settings()) as $key)
		{
			$template_data[strtoupper($key)] = $this->settings->cfg($key, true);
		}

		$template_data = array_merge($template_data, [
			'MCHAT_POSTS_ENABLED_LANG'	=> $this->settings->get_enabled_post_notifications_lang(),
			'MCHAT_ERROR'				=> implode('<br>', $error),
			'MCHAT_VERSION'				=> $this->settings->cfg('mchat_version'),
			'U_ACTION'					=> $u_action,
		]);

		/**
		 * Event to modify ACP global user settings template data
		 *
		 * @event dmzx.mchat.acp_globalusersettings_modify_template_data
		 * @var array	template_data	Array containing the template data for the ACP user settings
		 * @var array	error			Array with error lang keys
		 * @since 2.0.0-RC7
		 */
		$vars = [
			'template_data',
			'error',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.acp_globalusersettings_modify_template_data', compact($vars)));

		$this->template->assign_vars($template_data);
	}

	/**
	 * @param int $selected
	 * @return string
	 */
	protected function get_prune_mode_options($selected)
	{
		if (empty($this->settings->prune_modes[$selected]))
		{
			$selected = 0;
		}

		$prune_mode_options = '';

		foreach ($this->settings->prune_modes as $i => $prune_mode)
		{
			$prune_mode_options .= '<option value="' . $i . '"' . (($i == $selected) ? ' selected="selected"' : '') . '>';
			$prune_mode_options .= $this->lang->lang('MCHAT_ACP_' . strtoupper($prune_mode));
			$prune_mode_options .= '</option>';
		}

		return $prune_mode_options;
	}

	/**
	 * @param string $reparser_name
	 * @return bool
	 */
	protected function is_reparser_active($reparser_name)
	{
		$reparser_resume = $this->config_text->get('reparser_resume');

		if (empty($reparser_resume))
		{
			return false;
		}

		$reparser_resume = @unserialize($reparser_resume);

		if (!isset($reparser_resume[$reparser_name]['range-min']) || !isset($reparser_resume[$reparser_name]['range-max']))
		{
			return false;
		}

		return $reparser_resume[$reparser_name]['range-max'] >= $reparser_resume[$reparser_name]['range-min'];
	}
}

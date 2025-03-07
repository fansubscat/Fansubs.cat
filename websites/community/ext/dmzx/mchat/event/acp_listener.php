<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\event;

use dmzx\mchat\core\functions;
use dmzx\mchat\core\settings;
use phpbb\auth\auth;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class acp_listener implements EventSubscriberInterface
{
	/** @var template */
	protected $template;

	/** @var request_interface */
	protected $request;

	/** @var language */
	protected $lang;

	/** @var settings */
	protected $mchat_settings;

	/** @var functions */
	protected $mchat_functions;

	/**
	* Constructor
	*
	* @param template			$template
	* @param request_interface	$request
	* @param language			$lang
	* @param settings			$mchat_settings
	* @param functions			$mchat_functions
	*/
	public function __construct(
		template $template,
		request_interface $request,
		language $lang,
		settings $mchat_settings,
		functions $mchat_functions
	)
	{
		$this->template			= $template;
		$this->request			= $request;
		$this->lang				= $lang;
		$this->mchat_settings	= $mchat_settings;
		$this->mchat_functions	= $mchat_functions;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.permissions'							=> 'permissions',
			'core.acp_users_prefs_modify_sql'			=> 'acp_users_prefs_modify_sql',
			'core.acp_users_prefs_modify_template_data'	=> 'acp_users_prefs_modify_template_data',
			'core.acp_users_overview_before'			=> 'acp_users_overview_before',
			'core.delete_user_after'					=> 'delete_user_after',
		];
	}

	/**
	 * @param data $event
	 */
	public function permissions(data $event)
	{
		$ucp_configs = [];

		foreach (array_keys($this->mchat_settings->ucp_settings()) as $config_name)
		{
			$ucp_configs[] = 'u_' . $config_name;
		}

		$permission_categories = [
			'mchat' => [
				'u_mchat_use',
				'u_mchat_view',
				'u_mchat_edit',
				'u_mchat_delete',
				'u_mchat_moderator_edit',
				'u_mchat_moderator_delete',
				'u_mchat_ip',
				'u_mchat_pm',
				'u_mchat_like',
				'u_mchat_quote',
				'u_mchat_flood_ignore',
				'u_mchat_archive',
				'u_mchat_bbcode',
				'u_mchat_smilies',
				'u_mchat_urls',
				'a_mchat',
			],
			'mchat_user_config' => $ucp_configs,
		];

		$mchat_permissions = [];

		foreach ($permission_categories as $cat => $permissions)
		{
			foreach ($permissions as $permission)
			{
				$mchat_permissions[$permission] = [
					'lang'	=> 'ACL_' . strtoupper($permission),
					'cat'	=> $cat,
				];
			}
		}

		$event['permissions'] = array_merge($event['permissions'], $mchat_permissions);

		$event['categories'] = array_merge($event['categories'], [
			'mchat'				=> 'ACP_CAT_MCHAT',
			'mchat_user_config'	=> 'ACP_CAT_MCHAT_USER_CONFIG',
		]);
	}

	/**
	 * @param data $event
	 */
	public function acp_users_prefs_modify_sql(data $event)
	{
		$sql_ary = [];
		$validation = [];

		$user_id = $event['user_row']['user_id'];

		$auth = new auth();
		$userdata = $auth->obtain_user_data($user_id);
		$auth->acl($userdata);

		foreach ($this->mchat_settings->ucp_settings() as $config_name => $config_data)
		{
			if ($auth->acl_get('u_' . $config_name))
			{
				$default = $event['user_row']['user_' . $config_name];
				settype($default, gettype($config_data['default']));
				$sql_ary['user_' . $config_name] = $this->request->variable('user_' . $config_name, $default, is_string($default));

				if (isset($config_data['validation']))
				{
					$validation['user_' . $config_name] = $config_data['validation'];
				}
			}
		}

		$this->mchat_settings->include_functions('user', 'validate_data');

		$event['error'] = array_merge($event['error'], validate_data($sql_ary, $validation));
		$event['sql_ary'] = array_merge($event['sql_ary'], $sql_ary);
	}

	/**
	 * @param data $event
	 */
	public function acp_users_prefs_modify_template_data(data $event)
	{
		$this->lang->add_lang(['mchat_acp', 'mchat_ucp'], 'dmzx/mchat');

		$user_id = (int) $event['user_row']['user_id'];

		$auth = new auth();
		$userdata = $auth->obtain_user_data($user_id);
		$auth->acl($userdata);

		$selected = $this->mchat_settings->cfg_user('mchat_date', $event['user_row'], $auth);
		$date_template_data = $this->mchat_settings->get_date_template_data($selected);
		$this->template->assign_vars($date_template_data);

		$notifications_template_data = $this->mchat_settings->get_enabled_post_notifications_lang();
		$this->template->assign_var('MCHAT_POSTS_ENABLED_LANG', $notifications_template_data);

		foreach (array_keys($this->mchat_settings->ucp_settings()) as $config_name)
		{
			$upper = strtoupper($config_name);
			$this->template->assign_vars([
				$upper				=> $this->mchat_settings->cfg_user($config_name, $event['user_row'], $auth),
				$upper . '_NOAUTH'	=> !$auth->acl_get('u_' . $config_name, $user_id),
			]);
		}
	}

	/**
	 *
	 */
	public function acp_users_overview_before()
	{
		$this->lang->add_lang('mchat_acp', 'dmzx/mchat');

		$this->template->assign_vars([
			'L_RETAIN_POSTS'	=> $this->lang->lang('MCHAT_RETAIN_MESSAGES', $this->lang->lang('RETAIN_POSTS')),
			'L_DELETE_POSTS'	=> $this->lang->lang('MCHAT_DELETE_MESSAGES', $this->lang->lang('DELETE_POSTS')),
		]);
	}

	/**
	 * @param data $event
	 */
	public function delete_user_after(data $event)
	{
		if ($event['mode'] == 'remove')
		{
			$this->mchat_functions->mchat_prune($event['user_ids']);
		}
	}
}

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
use phpbb\collapsiblecategories\operator\operator as cc_operator;
use phpbb\controller\helper;
use phpbb\event\dispatcher_interface;
use phpbb\exception\http_exception;
use phpbb\extension\manager;
use phpbb\language\language;
use phpbb\pagination;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\textformatter\parser_interface;
use phpbb\user;
use rmcgirr83\authorizedforurls\event\listener as authorizedforurls;
use Symfony\Component\HttpFoundation\JsonResponse;

class mchat
{
	/** @var functions */
	protected $mchat_functions;

	/** @var notifications */
	protected $mchat_notifications;

	/** @var settings */
	protected $mchat_settings;

	/** @var log */
	protected $mchat_log;

	/** @var helper */
	protected $helper;

	/** @var template */
	protected $template;

	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var auth */
	protected $auth;

	/** @var pagination */
	protected $pagination;

	/** @var request_interface */
	protected $request;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var manager */
	protected $extension_manager;

	/** @var parser_interface */
	protected $textformatter_parser;

	/** @var cc_operator */
	protected $cc_operator;

	/** @var authorizedforurls */
	protected $authorized_for_urls;

	/** @var boolean */
	protected $remove_disallowed_bbcodes = false;

	/** @var bool */
	protected $custom_bbcodes_generated = false;

	/** @var bool */
	protected $smilies_generated = false;

	/** @var array */
	protected $foes = null;

	/**
	 * Constructor
	 *
	 * @param functions				$mchat_functions
	 * @param notifications			$mchat_notifications
	 * @param settings				$mchat_settings
	 * @param log					$mchat_log
	 * @param helper				$helper
	 * @param template				$template
	 * @param user					$user
	 * @param language				$lang
	 * @param auth					$auth
	 * @param pagination			$pagination
	 * @param request_interface		$request
	 * @param dispatcher_interface	$dispatcher
	 * @param manager				$extension_manager
	 * @param parser_interface		$textformatter_parser
	 * @param cc_operator			$cc_operator
	 * @param authorizedforurls		$authorized_for_urls
	 */
	public function __construct(
		functions $mchat_functions,
		notifications $mchat_notifications,
		settings $mchat_settings,
		log $mchat_log,
		helper $helper,
		template $template,
		user $user,
		language $lang,
		auth $auth,
		pagination $pagination,
		request_interface $request,
		dispatcher_interface $dispatcher,
		manager $extension_manager,
		parser_interface $textformatter_parser,
		cc_operator $cc_operator = null,
		authorizedforurls $authorized_for_urls = null
	)
	{
		$this->mchat_functions		= $mchat_functions;
		$this->mchat_notifications	= $mchat_notifications;
		$this->mchat_settings		= $mchat_settings;
		$this->mchat_log			= $mchat_log;
		$this->helper				= $helper;
		$this->template				= $template;
		$this->user					= $user;
		$this->lang					= $lang;
		$this->auth					= $auth;
		$this->pagination			= $pagination;
		$this->request				= $request;
		$this->dispatcher			= $dispatcher;
		$this->extension_manager	= $extension_manager;
		$this->textformatter_parser	= $textformatter_parser;
		$this->cc_operator			= $cc_operator;
		$this->authorized_for_urls	= $authorized_for_urls;
	}

	/**
	 * Render mChat on the index page
	 */
	public function page_index()
	{
		if (!$this->auth->acl_get('u_mchat_view'))
		{
			return;
		}

		$this->assign_whois();

		if (!$this->mchat_settings->cfg('mchat_index'))
		{
			return;
		}

		$this->lang->add_lang('mchat', 'dmzx/mchat');

		$this->assign_bbcodes_smilies();

		$this->render_page('index');
	}

	/**
	 * Render the mChat custom page
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function page_custom()
	{
		if (!$this->auth->acl_get('u_mchat_view'))
		{
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			throw new http_exception(403, 'NOT_AUTHORISED');
		}

		$this->lang->add_lang('mchat', 'dmzx/mchat');

		if (!$this->mchat_settings->cfg('mchat_custom_page'))
		{
			throw new http_exception(404, 'MCHAT_NO_CUSTOM_PAGE');
		}

		$this->mchat_functions->mchat_add_user_session();

		$this->assign_whois();

		$this->assign_bbcodes_smilies();

		$this->render_page('custom');

		// Add to navlinks
		$this->template->assign_block_vars('navlinks', [
			'FORUM_NAME'	=> $this->lang->lang('MCHAT_TITLE'),
			'U_VIEW_FORUM'	=> $this->helper->route('dmzx_mchat_page_custom_controller'),
		]);

		return $this->helper->render('mchat_body.html', $this->lang->lang('MCHAT_TITLE'));
	}

	/**
	 * Render the mChat archive
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function page_archive()
	{
		$this->lang->add_lang('mchat', 'dmzx/mchat');

		if (!$this->auth->acl_get('u_mchat_view') || !$this->auth->acl_get('u_mchat_archive'))
		{
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			throw new http_exception(403, 'MCHAT_NOACCESS_ARCHIVE');
		}

		$this->render_page('archive');

		// Add to navlinks
		$this->template->assign_block_vars_array('navlinks', [
			[
				'FORUM_NAME'	=> $this->lang->lang('MCHAT_TITLE'),
				'U_VIEW_FORUM'	=> $this->helper->route('dmzx_mchat_page_custom_controller'),
			],
			[
				'FORUM_NAME'	=> $this->lang->lang('MCHAT_ARCHIVE'),
				'U_VIEW_FORUM'	=> $this->helper->route('dmzx_mchat_page_archive_controller'),
			],
		]);

		return $this->helper->render('mchat_body.html', $this->lang->lang('MCHAT_ARCHIVE_PAGE'));
	}

	/**
	 * Controller for mChat IP WHOIS
	 *
	 * @param string $ip
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function page_whois($ip)
	{
		if (!$this->auth->acl_get('u_mchat_ip'))
		{
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			throw new http_exception(403, 'NOT_AUTHORISED');
		}

		$this->lang->add_lang('mchat', 'dmzx/mchat');

		$this->mchat_settings->include_functions('user', 'user_ipwhois');

		$this->template->assign_var('WHOIS', user_ipwhois($ip));

		return $this->helper->render('viewonline_whois.html', $this->lang->lang('WHO_IS_ONLINE'));
	}

	/**
	 * Controller for mChat Rules page
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 */
	public function page_rules()
	{
		if (!$this->auth->acl_get('u_mchat_view'))
		{
			if (!$this->user->data['is_registered'])
			{
				login_box();
			}

			throw new http_exception(403, 'NOT_AUTHORISED');
		}

		$this->lang->add_lang('mchat', 'dmzx/mchat');

		// If the rules are not empty in the language file, use them, else use the entry in the database
		$mchat_rules = $this->lang->lang('MCHAT_RULES_MESSAGE') ?: $this->mchat_settings->cfg('mchat_rules');

		if (!$mchat_rules)
		{
			throw new http_exception(404, 'MCHAT_NO_RULES');
		}

		$mchat_rules = htmlspecialchars_decode($mchat_rules);
		$mchat_rules = str_replace("\n", '<br>', $mchat_rules);

		$this->template->assign_var('MCHAT_RULES', $mchat_rules);

		return $this->helper->render('mchat_rules.html', $this->lang->lang('MCHAT_RULES'));
	}

	/**
	 * Initialize AJAX action
	 *
	 * @param string $permission Permission that is required to perform the current action
	 * @param bool $check_form_key
	 */
	protected function init_action($permission, $check_form_key = true)
	{
		if (!$this->request->is_ajax() || !$this->auth->acl_get($permission) || ($check_form_key && !check_form_key('mchat', -1)))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		$this->lang->add_lang('mchat', 'dmzx/mchat');
	}

	/**
	 * User submits a message
	 *
	 * @param bool $return_raw
	 * @return array|JsonResponse data sent to client as JSON
	 */
	public function action_add($return_raw = false)
	{
		$this->init_action('u_mchat_use');

		if ($this->mchat_functions->mchat_is_user_flooding())
		{
			throw new http_exception(400, 'MCHAT_FLOOD');
		}

		$message = $this->request->variable('message', '', true);

		if (!$this->mchat_settings->cfg('mchat_max_input_height'))
		{
			$message = preg_replace('/\s+/', ' ', $message);
		}

		if ($this->mchat_settings->cfg('mchat_capital_letter'))
		{
			$message = utf8_ucfirst($message);
		}

		$message_data = $this->process_message($message);

		$message_data = array_merge($message_data, [
			'user_id'		=> $this->user->data['user_id'],
			'user_ip'		=> $this->user->ip,
			'message_time'	=> time(),
		]);

		/**
		 * Event to modify a new message before it is inserted in the database
		 *
		 * @event dmzx.mchat.action_add_before
		 * @var	string	message			The message that is about to be processed and added to the database
		 * @var array	message_data	Array containing additional information that is added to the database
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'message',
			'message_data',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_add_before', compact($vars)));

		$is_new_session = $this->mchat_functions->mchat_action('add', $message_data);

		$response = $this->action_refresh(true);

		if ($is_new_session)
		{
			$response = array_merge($response, $this->action_whois(true));
		}

		/**
		 * Event to modify message data of a user's new message before it is sent back to the user
		 *
		 * @event dmzx.mchat.action_add_after
		 * @var	string	message			The message that was added to the database
		 * @var array	message_data	Array containing additional information that was added to the database
		 * @var bool	is_new_session	Indicating whether the message triggered a new mChat session to be created for the user
		 * @var array	response		The data that is sent back to the user
		 * @var boolean	return_raw		Whether to return a raw array or a JsonResponse object
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'message',
			'message_data',
			'is_new_session',
			'response',
			'return_raw',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_add_after', compact($vars)));

		return $return_raw ? $response : new JsonResponse($response);
	}

	/**
	 * User edits a message
	 *
	 * @param bool $return_raw
	 * @return array|JsonResponse data sent to client as JSON
	 */
	public function action_edit($return_raw = false)
	{
		$this->init_action('u_mchat_use');

		$message_id = $this->request->variable('message_id', 0);

		if (!$message_id)
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		$author = $this->mchat_functions->mchat_author_for_message($message_id);

		if (!$author)
		{
			throw new http_exception(410, 'MCHAT_MESSAGE_DELETED');
		}

		// Notifications can't be edited
		if ($this->mchat_notifications->is_notification($author) || !$this->auth_message('edit', $author['user_id'], $author['message_time']))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		$this->template->assign_var('MCHAT_PAGE', $this->request->variable('page', ''));

		$message = $this->request->variable('message', '', true);
		$sql_ary = $this->process_message($message);
		$this->mchat_functions->mchat_action('edit', $sql_ary, $message_id);

		$rows = $this->mchat_functions->mchat_get_messages($message_id);

		$this->assign_global_template_data();
		$this->assign_messages($rows);

		$response = ['edit' => $this->render_template('mchat_messages.html')];

		/**
		 * Event to modify the data of an edited message
		 *
		 * @event dmzx.mchat.action_edit_after
		 * @var int		message_id	The ID of the edited message
		 * @var	string	message		The content of the edited message that was added to the database
		 * @var array	author		Information about the message author
		 * @var array	response	The data that is sent back to the user
		 * @var boolean	return_raw	Whether to return a raw array or a JsonResponse object
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'message_id',
			'message',
			'author',
			'response',
			'return_raw',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_edit_after', compact($vars)));

		return $return_raw ? $response : new JsonResponse($response);
	}

	/**
	 * User deletes a message
	 *
	 * @param bool $return_raw
	 * @return array|JsonResponse data sent to client as JSON
	 */
	public function action_del($return_raw = false)
	{
		$this->init_action('u_mchat_use');

		$message_id = $this->request->variable('message_id', 0);

		if (!$message_id)
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		$author = $this->mchat_functions->mchat_author_for_message($message_id);

		if (!$author)
		{
			throw new http_exception(410, 'MCHAT_MESSAGE_DELETED');
		}

		if (!$this->auth_message('delete', $author['user_id'], $author['message_time']))
		{
			throw new http_exception(403, 'NO_AUTH_OPERATION');
		}

		$this->mchat_functions->mchat_action('del', null, $message_id);

		$response = ['del' => $message_id];

		/**
		 * Event that is triggered after an mChat message was deleted
		 *
		 * @event dmzx.mchat.action_delete_after
		 * @var int		message_id	The ID of the deleted message
		 * @var array	author		Information about the message author
		 * @var array	response	The data that is sent back to the user
		 * @var boolean	return_raw	Whether to return a raw array or a JsonResponse object
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'message_id',
			'author',
			'response',
			'return_raw',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_delete_after', compact($vars)));

		return $return_raw ? $response : new JsonResponse($response);
	}

	/**
	 * User checks for new messages
	 *
	 * @param bool $return_raw
	 * @return array|JsonResponse sent to client as JSON
	 */
	public function action_refresh($return_raw = false)
	{
		$this->init_action('u_mchat_view', false);

		// Keep the session alive forever if there is no session timeout
		$keep_session_alive = !$this->mchat_settings->cfg('mchat_timeout');

		// Whether to check the log table for new entries
		$need_log_update = $this->mchat_settings->cfg('mchat_live_updates');

		/**
		 * Event that is triggered before new mChat messages are checked
		 *
		 * @event dmzx.mchat.action_refresh_before
		 * @var bool	keep_session_alive	Whether to the user's phpBB session
		 * @var bool	need_log_update		Whether to check the log table for new entries
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'keep_session_alive',
			'need_log_update',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_refresh_before', compact($vars)));

		if ($keep_session_alive)
		{
			$this->user->update_session_infos();
		}

		$response = ['refresh' => true];

		if ($need_log_update)
		{
			$log_id = $this->request->variable('log', 0);
			$logs = $this->mchat_log->get_logs($log_id);

			$response['log'] = $logs['latest'];
			unset($logs['latest']);

			$log_edit_del_ids = $logs;
			unset($logs);
		}
		else
		{
			$log_edit_del_ids = array_fill_keys($this->mchat_log->get_types(), []);
		}

		$last_id = $this->request->variable('last', 0);
		$total = 0;
		$offset = 0;

		/**
		 * Event that allows modifying data before new mChat messages are fetched
		 *
		 * @event dmzx.mchat.action_refresh_get_messages_before
		 * @var array	response			The data that is sent back to the user (still incomplete at this point)
		 * @var array	log_edit_del_ids	An array containing IDs of messages that have been edited or deleted since the user's last refresh
		 * @var int		last_id				The latest message that the user has
		 * @var int		total				Limit the number of messages to fetch
		 * @var int		offset				The number of messages to skip
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'response',
			'log_edit_del_ids',
			'last_id',
			'total',
			'offset',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_refresh_get_messages_before', compact($vars)));

		$rows = $this->mchat_functions->mchat_get_messages($log_edit_del_ids['edit'], $last_id, $total, $offset);
		$rows_refresh = [];
		$rows_edit = [];

		foreach ($rows as $row)
		{
			if ($row['message_id'] > $last_id)
			{
				$rows_refresh[] = $row;
			}
			else if (in_array($row['message_id'], $log_edit_del_ids['edit']))
			{
				$rows_edit[] = $row;
			}
		}

		if ($rows_refresh || $rows_edit)
		{
			$this->assign_global_template_data();
		}

		// Assign new messages
		if ($rows_refresh)
		{
			$this->assign_messages($rows_refresh);
			$response['add'] = $this->render_template('mchat_messages.html');
		}

		// Assign edited messages
		if ($rows_edit)
		{
			$this->assign_messages($rows_edit);
			$response['edit'] = $this->render_template('mchat_messages.html');
		}

		// Assign deleted messages
		if ($log_edit_del_ids['del'])
		{
			$response['del'] = $log_edit_del_ids['del'];
		}

		/**
		 * Event to modify the data that is sent to the user after checking for new mChat message
		 *
		 * @event dmzx.mchat.action_refresh_after
		 * @var array	rows		The rows that where fetched from the database
		 * @var array	response	The data that is sent back to the user
		 * @var boolean	return_raw	Whether to return a raw array or a JsonResponse object
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'rows',
			'response',
			'return_raw',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_refresh_after', compact($vars)));

		return $return_raw ? $response : new JsonResponse($response);
	}

	/**
	 * User requests who is chatting
	 *
	 * @param bool $return_raw
	 * @return array|JsonResponse data sent to client as JSON
	 */
	public function action_whois($return_raw = false)
	{
		$this->init_action('u_mchat_view', false);

		$this->assign_whois();

		$response = ['whois' => true];

		if ($this->mchat_settings->cfg('mchat_whois_index'))
		{
			$response['container'] = $this->render_template('mchat_whois.html');
		}

		if ($this->mchat_settings->cfg('mchat_navbar_link_count'))
		{
			$active_users = $this->mchat_functions->mchat_active_users();
			$response['navlink'] = $active_users['users_count_title'];
			$response['navlink_title'] = strip_tags($active_users['users_total']);
		}

		/**
		 * Event to modify the result of the Who Is Online update
		 *
		 * @event dmzx.mchat.action_whois_after
		 * @var array	response	The data that is sent back to the user
		 * @var boolean	return_raw	Whether to return a raw array or a JsonResponse object
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'response',
			'return_raw',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_whois_after', compact($vars)));

		return $return_raw ? $response : new JsonResponse($response);
	}

	/**
	 * Adds the template variables for the header link
	 */
	public function render_page_header_link()
	{
		if (!$this->auth->acl_get('u_mchat_view'))
		{
			return;
		}

		$custom_page = $this->mchat_settings->cfg('mchat_custom_page');
		$archive = $this->auth->acl_get('u_mchat_archive');
		$rules = $this->lang->lang('MCHAT_RULES_MESSAGE') ?: $this->mchat_settings->cfg('mchat_rules');

		$template_data = [
			'MCHAT_TITLE'			=> $this->lang->lang('MCHAT_TITLE'),
			'MCHAT_TITLE_HINT'		=> $this->lang->lang('MCHAT_TITLE'),
			'U_MCHAT_CUSTOM_PAGE'	=> $custom_page ? $this->helper->route('dmzx_mchat_page_custom_controller') : false,
			'U_MCHAT_ARCHIVE'		=> $archive ? $this->helper->route('dmzx_mchat_page_archive_controller') : false,
			'U_MCHAT_RULES'			=> $rules ? $this->helper->route('dmzx_mchat_page_rules_controller') : false,
		];

		if ($this->mchat_settings->cfg('mchat_navbar_link_count'))
		{
			$active_users = $this->mchat_functions->mchat_active_users();
			$template_data['MCHAT_TITLE'] = $active_users['users_count_title'];
			$template_data['MCHAT_TITLE_HINT'] = strip_tags($active_users['users_total']);
		}
		else
		{
			$active_users = [];
		}

		/**
		 * Event that is triggered before data for the navigation bar is assigned to the template
		 *
		 * @event dmzx.mchat.header_link_template_data
		 * @var array	template_data	The data that is abbout to be assigned to the template
		 * @var array	active_users	Array containing information about active users. Available array keys:
		 *           					online_userlist, users_count_title, users_total, refresh_message
		 * 								Note:	This array is empty if the number of active chat sessions is not
		 * 										displayed in the navbar.
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'template_data',
			'active_users',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.header_link_template_data', compact($vars)));

		$this->template->assign_vars($template_data);
	}

	/**
	 * Renders data for a page
	 *
	 * @param string $page The page we are rendering for, one of index|custom|archive
	 */
	protected function render_page($page)
	{
		/**
		 * Event that is triggered before mChat is rendered
		 *
		 * @event dmzx.mchat.render_page_before
		 * @var string	page	The page that is rendered, one of index|custom|archive
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'page',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.render_page_before', compact($vars)));

		// Add lang file
		$this->lang->add_lang('posting');

		$is_archive = $page == 'archive';
		$jump_to_id = $is_archive ? $this->request->variable('jumpto', 0) : 0;

		// If the static message is not empty in the language file, use it, else use the static message in the database
		$static_message = $this->lang->lang('MCHAT_STATIC_MESSAGE') ?: $this->mchat_settings->cfg('mchat_static_message');
		$whois_refresh = $this->mchat_settings->cfg('mchat_whois_index') || $this->mchat_settings->cfg('mchat_navbar_link_count');

		$total_messages = $this->mchat_functions->mchat_total_message_count();

		$template_data = [
			'MCHAT_PAGE'					=> $page,
			'MCHAT_CURRENT_URL'				=> $this->mchat_settings->get_current_page(),
			'MCHAT_ALLOW_SMILES'			=> $this->mchat_settings->cfg('allow_smilies') && $this->auth->acl_get('u_mchat_smilies'),
			'MCHAT_MESSAGE_TOP'				=> $this->mchat_settings->cfg('mchat_message_top'),
			'MCHAT_INDEX_HEIGHT'			=> $this->mchat_settings->cfg('mchat_index_height'),
			'MCHAT_CUSTOM_HEIGHT'			=> $this->mchat_settings->cfg('mchat_custom_height'),
			'MCHAT_LOCATION'				=> $this->mchat_settings->cfg('mchat_location'),
			'MCHAT_CHARACTER_COUNT'			=> $this->mchat_settings->cfg('mchat_character_count'),
			'MCHAT_SOUND'					=> $this->mchat_settings->cfg('mchat_sound'),
			'MCHAT_SOUND_ENABLED'			=> $this->mchat_settings->cfg('mchat_sound') || $this->mchat_settings->cfg('mchat_sound', true),
			'MCHAT_INDEX'					=> $this->mchat_settings->cfg('mchat_index'),
			'MCHAT_WHOIS_INDEX'				=> $this->mchat_settings->cfg('mchat_whois_index'),
			'MCHAT_WHOIS_REFRESH'			=> $whois_refresh ? $this->mchat_settings->cfg('mchat_whois_refresh') * 1000 : 0,
			'MCHAT_REFRESH_JS'				=> $this->mchat_settings->cfg('mchat_refresh') * 1000,
			'MCHAT_ARCHIVE'					=> $this->auth->acl_get('u_mchat_archive'),
			'MCHAT_RULES'					=> $this->lang->lang('MCHAT_RULES_MESSAGE') ?: $this->mchat_settings->cfg('mchat_rules'),
			'MCHAT_LOG_ID'					=> $this->mchat_log->get_latest_id(),
			'MCHAT_STATIC_MESS'				=> htmlspecialchars_decode($static_message),
			'MCHAT_MAX_INPUT_HEIGHT'		=> $this->mchat_settings->cfg('mchat_max_input_height'),
			'MCHAT_MAX_MESSAGE_LENGTH'		=> $this->mchat_settings->cfg('mchat_max_message_lngth'),
			'MCHAT_TOTAL_MESSAGES'			=> $total_messages,
			'MCHAT_JUMP_TO'					=> $jump_to_id,
			'COOKIE_NAME'					=> $this->mchat_settings->cfg('cookie_name', true) . '_',
		];

		// The template needs some language variables if we display relative time for messages
		if ($this->mchat_settings->cfg('mchat_relative_time'))
		{
			$template_data['MCHAT_MINUTES_AGO_LIMIT'] = $this->get_relative_minutes_limit();
		}

		// Get actions which the user is allowed to perform on the current page
		$actions = array_keys(array_filter([
			'edit'		=> $this->auth_message('edit', true, time()),
			'del'		=> $this->auth_message('delete', true, time()),
			'refresh'	=> !$is_archive && $this->auth->acl_get('u_mchat_view'),
			'add'		=> !$is_archive && $this->auth->acl_get('u_mchat_use'),
			'whois'		=> !$is_archive && $whois_refresh,
		]));

		foreach ($actions as $action)
		{
			$this->template->assign_block_vars('mchaturl', [
				'ACTION'	=> $action,
				'URL'		=> $this->helper->route('dmzx_mchat_action_' . $action . '_controller', [], false),
			]);
		}

		$limit = $this->mchat_settings->cfg('mchat_message_num_' . $page);

		if ($is_archive)
		{
			if ($jump_to_id)
			{
				$sql_where_jump_to_id = 'm.message_id > ' . (int) $jump_to_id;
				$sql_order_by = 'm.message_id ASC';
				$num_subsequent_messages = $this->mchat_functions->mchat_total_message_count($sql_where_jump_to_id, $sql_order_by);
				$start = (int) floor($num_subsequent_messages / $limit) * $limit;
			}
			else
			{
				$start = $this->request->variable('start', 0);
			}
		}
		else
		{
			$start = 0;
		}

		$message_ids = [];
		$last_id = 0;

		/**
		 * Event to modify arguments before fetching messages from the database
		 *
		 * @event dmzx.mchat.render_page_get_messages_before
		 * @var string	page			The page that is rendered, one of index|custom|archive
		 * @var array	message_ids		IDs of specific messages to fetch, should be an empty array
		 * @var int		last_id			The ID of the latest message that the user has, should be 0
		 * @var int		limit			Number of messages to display per page
		 * @var int		start			The message which should be considered currently active, used to determine the page we're on
		 * @var int		jump_to_id		The ID of the message that is being jumped to in the archive, usually when a user clicked on a quote reference
		 * @var array	actions			Array containing URLs to actions the user is allowed to perform (read only)
		 * @var array	template_data	The data that is about to be assigned to the template
		 * @var int		total_messages	Total number of messages
		 * @since 2.1.1
		 * @changed 2.1.4-RC1 added total_messages
		 */
		$vars = [
			'page',
			'message_ids',
			'last_id',
			'limit',
			'start',
			'jump_to_id',
			'actions',
			'template_data',
			'total_messages',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.render_page_get_messages_before', compact($vars)));

		$this->assign_global_template_data();

		// Always fetch at least one message so that we can extract the latest_message_id
		$soft_limit = max(1, $limit);

		$rows = $this->mchat_functions->mchat_get_messages($message_ids, $last_id, $soft_limit, $start);

		if ($limit)
		{
			$this->assign_messages($rows, $page);
		}

		// Pass the latest_message_id to the template so that we know later where to start looking for new messages
		$latest_message_id = 0;

		if ($rows)
		{
			$latest_message = reset($rows);
			$latest_message_id = $latest_message['message_id'];
		}

		$template_data['MCHAT_LATEST_MESSAGE_ID'] = $latest_message_id;

		// Render pagination
		if ($is_archive)
		{
			$archive_url = $this->helper->route('dmzx_mchat_page_archive_controller');

			/**
			 * Event to modify mChat pagination on the archive page
			 *
			 * @event dmzx.mchat.render_page_pagination_before
			 * @var string	archive_url		Pagination base URL
			 * @var int		total_messages	Total number of messages in the mChat table
			 * @var int		limit			Number of messages to display per page
			 * @var int		start			The message which should be considered currently active, used to determine the page we're on
			 * @var int		jump_to_id		The ID of the message that is being jumped to in the archive, usually when a user clicked on a quote reference
			 * @var array	template_data	The data that is about to be assigned to the template
			 * @since 2.0.0-RC6
			 * @changed 2.1.1 added jump_to_id, template_data
			 */
			$vars = [
				'archive_url',
				'total_messages',
				'limit',
				'start',
				'jump_to_id',
				'template_data',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.render_page_pagination_before', compact($vars)));

			$this->pagination->generate_template_pagination($archive_url, 'pagination', 'start', $total_messages, $limit, $start);
			$template_data['MCHAT_TOTAL_MESSAGES'] = $this->lang->lang('MCHAT_TOTALMESSAGES', $total_messages);
		}

		// Render legend
		if ($page !== 'index')
		{
			$legend = $this->mchat_functions->mchat_legend();
			$template_data['LEGEND'] = implode($this->lang->lang('COMMA_SEPARATOR'), $legend);
		}

		// Make mChat collapsible
		if ($page === 'index' && $this->cc_operator !== null)
		{
			$cc_fid = 'mchat';
			$template_data = array_merge($template_data, [
				'MCHAT_IS_COLLAPSIBLE'	=> true,
				'S_MCHAT_HIDDEN'		=> $this->cc_operator->is_collapsed($cc_fid),
				'U_MCHAT_COLLAPSE_URL'	=> $this->cc_operator->get_collapsible_link($cc_fid),
			]);
		}

		$this->assign_authors();

		if ($this->auth->acl_get('u_mchat_use'))
		{
			add_form_key('mchat', '_DMZX_MCHAT');
		}

		/**
		 * Event that is triggered after mChat was rendered
		 *
		 * @event dmzx.mchat.render_page_after
		 * @var string	page			The page that was rendered, one of index|custom|archive
		 * @var array	actions			Array containing URLs to actions the user is allowed to perform (read only)
		 * @var array	template_data	The data that is about to be assigned to the template
		 * @since 2.0.0-RC6
		 * @changed 2.1.1 Added template_data
		 */
		$vars = [
			'page',
			'actions',
			'template_data',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.render_page_after', compact($vars)));

		$this->template->assign_vars($template_data);
	}

	/**
	 * Assigns author names and homepages for copyright
	 */
	protected function assign_authors()
	{
		$md_manager = $this->extension_manager->create_extension_metadata_manager('dmzx/mchat');
		$meta = $md_manager->get_metadata();

		$author_homepages = [];

		foreach (array_slice($meta['authors'], 0, 1) as $author)
		{
			$author_homepages[] = sprintf('<a href="%1$s" title="%2$s">%2$s</a>', $author['homepage'], $author['name']);
		}

		$this->template->assign_vars([
			'MCHAT_DISPLAY_NAME'		=> $meta['extra']['display-name'],
			'MCHAT_AUTHOR_HOMEPAGES'	=> implode(' &amp; ', $author_homepages),
		]);
	}

	/**
	 * Assigns common template data that is required for displaying messages
	 */
	public function assign_global_template_data()
	{
		$template_data = [
			'S_BBCODE_ALLOWED'			=> $this->auth->acl_get('u_mchat_bbcode') && $this->mchat_settings->cfg('allow_bbcode'),
			'MCHAT_ALLOW_USE'			=> $this->auth->acl_get('u_mchat_use'),
			'MCHAT_ALLOW_IP'			=> $this->auth->acl_get('u_mchat_ip'),
			'MCHAT_ALLOW_PM'			=> $this->auth->acl_get('u_mchat_pm'),
			'MCHAT_ALLOW_LIKE'			=> $this->auth->acl_get('u_mchat_like'),
			'MCHAT_ALLOW_QUOTE'			=> $this->auth->acl_get('u_mchat_quote'),
			'MCHAT_ALLOW_PERMISSIONS'	=> $this->auth->acl_get('a_authusers'),
			'MCHAT_EDIT_DELETE_LIMIT'	=> 1000 * $this->mchat_settings->cfg('mchat_edit_delete_limit'),
			'MCHAT_EDIT_DELETE_IGNORE'	=> $this->mchat_settings->cfg('mchat_edit_delete_limit') && ($this->auth->acl_get('u_mchat_moderator_edit') || $this->auth->acl_get('u_mchat_moderator_delete')),
			'MCHAT_RELATIVE_TIME'		=> $this->mchat_settings->cfg('mchat_relative_time'),
			'MCHAT_TIMEOUT'				=> 1000 * $this->mchat_settings->cfg('mchat_timeout'),
			'S_MCHAT_AVATARS'			=> $this->display_avatars(),
			'EXT_URL'					=> $this->mchat_settings->url('ext/dmzx/mchat/', true, false),
			'STYLE_PATH'				=> $this->mchat_settings->url('styles/' . rawurlencode($this->user->style['style_path']), true, false),
		];

		/**
		 * Event that allows adding global template data for mChat
		 *
		 * @event dmzx.mchat.global_modify_template_data
		 * @var array	template_data		The data that is about to be assigned to the template
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'template_data',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.global_modify_template_data', compact($vars)));

		$this->template->assign_vars($template_data);
	}

	/**
	 * Returns true if we need do display avatars in the messages, otherwise false
	 *
	 * @return bool
	 */
	protected function display_avatars()
	{
		return $this->mchat_settings->cfg('mchat_avatars') && $this->user->optionget('viewavatars');
	}

	/**
	 * Assigns all message rows to the template
	 *
	 * @param array $rows
	 * @param string $page
	 */
	public function assign_messages($rows, $page = '')
	{
		$rows = array_filter($rows, [$this, 'has_read_auth']);

		if (!$rows)
		{
			return;
		}

		if ($this->messages_need_reversing($page))
		{
			$rows = array_reverse($rows);
		}

		if ($this->foes === null)
		{
			$this->foes = $this->mchat_functions->mchat_foes();
		}

		// Remove template data from previous render
		$this->template->destroy_block_vars('mchatrow');

		$user_avatars = [];

		// Cache avatars
		$display_avatar = $this->display_avatars();
		foreach ($rows as $row)
		{
			if (!isset($user_avatars[$row['user_id']]))
			{
				$user_avatars[$row['user_id']] = !$display_avatar || !$row['user_avatar'] ? '' : phpbb_get_user_avatar([
					'avatar'		=> $row['user_avatar'],
					'avatar_type'	=> $row['user_avatar_type'],
					'avatar_width'	=> $row['user_avatar_width'] >= $row['user_avatar_height'] ? 36 : 0,
					'avatar_height'	=> $row['user_avatar_width'] >= $row['user_avatar_height'] ? 0 : 36,
				]);
			}
		}

		$rows = $this->mchat_notifications->process($rows);

		foreach ($rows as $row)
		{
			$username_full = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $this->lang->lang('GUEST'));

			if (in_array($row['user_id'], $this->foes))
			{
				$row['message'] = $this->lang->lang('MCHAT_FOE', $username_full);
			}

			$message_age = time() - $row['message_time'];
			$minutes_ago = $this->get_minutes_ago($message_age);
			$absolute_datetime = $this->user->format_date($row['message_time'], $this->mchat_settings->cfg('mchat_date'), true);
			// If relative time is selected, also display "today" / "yesterday", else display absolute time.
			$datetime = $this->user->format_date($row['message_time'], $this->mchat_settings->cfg('mchat_date'), !$this->mchat_settings->cfg('mchat_relative_time'));

			$is_poster = $row['user_id'] != ANONYMOUS && $this->user->data['user_id'] == $row['user_id'];

			$message_for_edit = generate_text_for_edit($row['message'], $row['bbcode_uid'], $row['bbcode_options']);

			$template_data = [
				'MCHAT_USER_ID'				=> $row['user_id'],
				'MCHAT_ALLOW_EDIT'			=> $this->auth_message('edit', $row['user_id'], $row['message_time']),
				'MCHAT_ALLOW_DEL'			=> $this->auth_message('delete', $row['user_id'], $row['message_time']),
				'MCHAT_USER_AVATAR'			=> $user_avatars[$row['user_id']],
				'U_VIEWPROFILE'				=> $row['user_id'] != ANONYMOUS ? append_sid($this->mchat_settings->url('memberlist', true), ['mode' => 'viewprofile', 'u' => $row['user_id']]) : '',
				'MCHAT_IS_POSTER'			=> $is_poster,
				'MCHAT_IS_NOTIFICATION'		=> $this->mchat_notifications->is_notification($row),
				'MCHAT_PM'					=> !$is_poster && $this->mchat_settings->cfg('allow_privmsg') && $this->auth->acl_get('u_sendpm') && ($row['user_allow_pm'] || $this->auth->acl_gets('a_', 'm_') || $this->auth->acl_getf_global('m_')) ? append_sid($this->mchat_settings->url('ucp', true), ['i' => 'pm', 'mode' => 'compose', 'mchat_pm_quote_message' => $row['message_id'], 'u' => $row['user_id']]) : '',
				'MCHAT_MESSAGE_EDIT'		=> $message_for_edit['text'],
				'MCHAT_MESSAGE_ID'			=> $row['message_id'],
				'MCHAT_USERNAME_FULL'		=> $username_full,
				'MCHAT_USERNAME'			=> get_username_string('username', $row['user_id'], $row['username'], $row['user_colour'], $this->lang->lang('GUEST')),
				'MCHAT_USERNAME_COLOR'		=> get_username_string('colour', $row['user_id'], $row['username'], $row['user_colour'], $this->lang->lang('GUEST')),
				'MCHAT_WHOIS_USER'			=> $this->lang->lang('MCHAT_WHOIS_USER', $row['user_ip']),
				'MCHAT_U_IP'				=> $this->auth->acl_get('u_mchat_ip') ? $this->helper->route('dmzx_mchat_page_whois_controller', ['ip' => $row['user_ip']]) : false,
				'MCHAT_U_PERMISSIONS'		=> append_sid($this->mchat_settings->url('adm/index', true), ['i' => 'permissions', 'mode' => 'setting_user_global', rawurlencode('user_id[0]') => $row['user_id']], true, $this->user->session_id),
				'MCHAT_MESSAGE'				=> generate_text_for_display($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options']),
				'MCHAT_TIME'				=> $minutes_ago === -1 ? $datetime : $this->lang->lang('MCHAT_MINUTES_AGO', $minutes_ago),
				'MCHAT_DATETIME'			=> $absolute_datetime,
				'MCHAT_MINUTES_AGO'			=> $minutes_ago,
				'MCHAT_RELATIVE_UPDATE'		=> 60 - $message_age % 60,
				'MCHAT_MESSAGE_TIME'		=> $row['message_time'],
			];

			/**
			 * Event to modify the template data of an mChat message before it is sent to the template
			 *
			 * @event dmzx.mchat.message_modify_template_data
			 * @var array	template_data		The data that is about to be assigned to the template
			 * @var string	username_full		The link to the user profile, e.g. <a href="...">Username</a>
			 * @var array	row					The raw message data as fetched from the database
			 * @var int		message_age			The number of seconds that have passed since the message was posted
			 * @var int		minutes_ago			The number of minutes that have passed since the message was posted, or -1
			 * @var string	datetime			The full date in the user-specific date format
			 * @var bool	is_poster			Whether or not the current user posted this message
			 * @var array	message_for_edit	The data for editing the message
			 * @since 2.0.0-RC6
			 */
			$vars = [
				'template_data',
				'username_full',
				'row',
				'message_age',
				'minutes_ago',
				'datetime',
				'is_poster',
				'message_for_edit',
			];
			extract($this->dispatcher->trigger_event('dmzx.mchat.message_modify_template_data', compact($vars)));

			$this->template->assign_block_vars('mchatrow', $template_data);
		}
	}

	/**
	 * By default, rows are fetched by message ID descending. This method returns true if
	 * the user wants them to be displayed ascending, otherwise false.
	 *
	 * @param string $page
	 * @return bool
	 */
	protected function messages_need_reversing($page)
	{
		$mchat_message_top = $this->mchat_settings->cfg('mchat_message_top');

		if ($page === 'archive')
		{
			$mchat_archive_sort = $this->mchat_settings->cfg('mchat_archive_sort');

			if ($mchat_archive_sort == settings::ARCHIVE_SORT_TOP_BOTTOM || $mchat_archive_sort == settings::ARCHIVE_SORT_USER && !$mchat_message_top)
			{
				return true;
			}
		}
		else if (!$mchat_message_top)
		{
			return true;
		}

		return false;
	}

	/**
	 * Returns true if the user is allowed to read the given message row
	 *
	 * @param array $row
	 * @return bool
	 */
	protected function has_read_auth($row)
	{
		if ($row['forum_id'])
		{
			// No permission to read forum
			if (!$this->auth->acl_get('f_read', $row['forum_id']))
			{
				return false;
			}

			// Post is not approved and no approval permission
			if ($row['post_visibility'] != ITEM_APPROVED && !$this->auth->acl_get('m_approve', $row['forum_id']))
			{
				return false;
			}
		}

		return true;
	}

	/**
	 * Calculates the number of minutes that have passed since the message was posted.
	 * If relative time is disabled or the message is older than 59 minutes, -1 is returned.
	 *
	 * @param int $message_age
	 * @return int
	 */
	protected function get_minutes_ago($message_age)
	{
		if ($this->mchat_settings->cfg('mchat_relative_time'))
		{
			$minutes_ago = floor($message_age / 60);
			if ($minutes_ago < $this->get_relative_minutes_limit())
			{
				return $minutes_ago;
			}
		}

		return -1;
	}

	/**
	 * Calculates the amount of time after which messages switch from displaying relative time
	 * to displaying absolute time. Uses mChat's timeout if it's not zero, otherwise phpBB's
	 * global session timeout, but never shorter than 1 minute and never longer than 60 minutes.
	 *
	 * @return int
	 */
	protected function get_relative_minutes_limit()
	{
		$timeout = $this->mchat_settings->cfg('mchat_timeout');

		if (!$timeout)
		{
			$timeout = $this->mchat_settings->cfg('session_length');
		}

		return min(max((int) ceil($timeout / 60), 1), 60);
	}

	/**
	 * Assigns BBCodes and smilies to the template
	 */
	protected function assign_bbcodes_smilies()
	{
		$display_bbcodes = $this->mchat_settings->cfg('allow_bbcode') && $this->auth->acl_get('u_mchat_bbcode');

		$display_smilies = $this->mchat_settings->cfg('allow_smilies') && $this->auth->acl_get('u_mchat_smilies') && !$this->smilies_generated;

		/**
		 * Event to decide whether or to display BBCodes or smilies
		 *
		 * @event dmzx.mchat.assign_bbcodes_smilies_before
		 * @var bool	display_bbcodes		Whether or not to render BBCodes
		 * @var bool	display_smilies		Whether or not to render smilies
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'display_bbcodes',
			'display_smilies',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.assign_bbcodes_smilies_before', compact($vars)));

		// Display BBCodes
		if ($display_bbcodes)
		{
			$bbcode_template_vars = [
				'quote'	=> [
					'allow'			=> true,
					'template_var'	=> 'S_BBCODE_QUOTE',
				],
				'img'	=> [
					'allow'			=> true,
					'template_var'	=> 'S_BBCODE_IMG',
				],
				'url'	=> [
					'allow'			=> $this->mchat_settings->cfg('allow_post_links'),
					'template_var'	=> 'S_LINKS_ALLOWED',
				],
				'flash'	=> [
					'allow'			=> $this->mchat_settings->cfg('allow_post_flash'),
					'template_var'	=> 'S_BBCODE_FLASH',
				],
			];

			foreach ($bbcode_template_vars as $bbcode => $option)
			{
				$is_disallowed = preg_match('#(^|\|)' . $bbcode . '($|\|)#Usi', $this->mchat_settings->cfg('mchat_bbcode_disallowed')) || !$option['allow'];
				$this->template->assign_var($option['template_var'], !$is_disallowed);
			}

			$this->template->assign_var('MCHAT_DISALLOWED_BBCODES', $this->mchat_settings->cfg('mchat_bbcode_disallowed'));

			if (!$this->custom_bbcodes_generated)
			{
				$this->mchat_settings->include_functions('display', 'display_custom_bbcodes');

				$this->remove_disallowed_bbcodes = true;
				display_custom_bbcodes();
			}
		}

		// Display smilies
		if ($display_smilies)
		{
			$this->mchat_settings->include_functions('posting', 'generate_smilies');

			generate_smilies('inline', 0);
		}
	}

	/**
	 * Appends a condition to the WHERE key of the SQL array to not fetch disallowed BBCodes from the database
	 *
	 * @param array $sql_ary
	 * @return array
	 */
	public function remove_disallowed_bbcodes($sql_ary)
	{
		// Add disallowed BBCodes to the template only if we're rendering for mChat
		if ($this->remove_disallowed_bbcodes)
		{
			$sql_ary['WHERE'] = $this->mchat_functions->mchat_sql_append_forbidden_bbcodes($sql_ary['WHERE']);
		}

		return $sql_ary;
	}

	/**
	 * Sets the default values when a user registers a new account as configured in the global user settings
	 *
	 * @param array $sql_ary
	 * @return array
	 */
	public function set_user_default_values($sql_ary)
	{
		foreach (array_keys($this->mchat_settings->ucp_settings()) as $config_name)
		{
			$sql_ary['user_' . $config_name] = $this->mchat_settings->cfg($config_name, true);
		}

		return $sql_ary;
	}

	/**
	 * Fetches the message text of the given ID, quotes it using the current user name and assigns it to the template
	 *
	 * @param int $mchat_message_id
	 */
	public function quote_message_text($mchat_message_id)
	{
		if (!$this->auth->acl_get('u_mchat_view'))
		{
			return;
		}

		$rows = $this->mchat_functions->mchat_get_messages($mchat_message_id);
		$row = reset($rows);

		if (!$row || !$this->has_read_auth($row))
		{
			return;
		}

		if ($row['post_id'])
		{
			$rows = $this->mchat_notifications->process([$row]);
			$row = reset($rows);
		}

		$message_for_edit = generate_text_for_edit($row['message'], $row['bbcode_uid'], $row['bbcode_options']);
		$message = '[quote=&quot;' . $row['username'] . '&quot;]' . $message_for_edit['text'] . "[/quote]\n";

		$this->template->assign_var('MESSAGE', $message);
	}

	/**
	 * Remove expired sessions from the database
	 */
	public function session_gc()
	{
		$this->mchat_functions->mchat_session_gc();
	}

	/**
	 * Assigns whois and stats at the bottom of the index page
	 */
	protected function assign_whois()
	{
		if ($this->mchat_settings->cfg('mchat_whois_index') || $this->mchat_settings->cfg('mchat_stats_index'))
		{
			$active_users = $this->mchat_functions->mchat_active_users();

			$this->template->assign_vars([
				'MCHAT_STATS_INDEX'		=> $this->mchat_settings->cfg('mchat_stats_index'),
				'MCHAT_USERS_TOTAL'		=> $active_users['users_total'],
				'MCHAT_USERS_LIST'		=> $active_users['online_userlist'],
				'MCHAT_ONLINE_EXPLAIN'	=> $active_users['refresh_message'],
			]);
		}
	}

	/**
	 * Checks whether the current user has edit or delete permissions for a message written by $author_id
	 *
	 * @param string $mode One of edit|delete
	 * @param int $author_id The user id of the message
	 * @param int $message_time The message created time
	 * @return bool
	 */
	protected function auth_message($mode, $author_id, $message_time)
	{
		if ($this->auth->acl_get('u_mchat_moderator_' . $mode))
		{
			return true;
		}

		if (!$this->user->data['is_registered'] || $this->user->data['user_id'] != $author_id || !$this->auth->acl_get('u_mchat_' . $mode))
		{
			return false;
		}

		return !$this->mchat_settings->cfg('mchat_edit_delete_limit') || $message_time >= time() - $this->mchat_settings->cfg('mchat_edit_delete_limit');
	}

	/**
	 * Performs bound checks on the message and returns an array containing the message
	 * and BBCode options ready to be sent to the database
	 *
	 * @param string $message
	 * @return array
	 */
	protected function process_message($message)
	{
		// Must have something other than bbcode in the message
		$message_without_bbcode = trim(preg_replace('#\[\/?[^\[\]]+\]#m', '', $message));
		if (!utf8_strlen($message_without_bbcode))
		{
			throw new http_exception(400, 'MCHAT_NOMESSAGEINPUT');
		}

		// Must not exceed character limit
		if ($this->mchat_settings->cfg('mchat_max_message_lngth'))
		{
			$message_without_entities = htmlspecialchars_decode($message, ENT_COMPAT);
			if (utf8_strlen($message_without_entities) > $this->mchat_settings->cfg('mchat_max_message_lngth'))
			{
				throw new http_exception(400, 'MCHAT_MESS_LONG', [$this->mchat_settings->cfg('mchat_max_message_lngth')]);
			}
		}

		// Compatibility with Authorized for URLs by RMcGirr83 - requires at least 1.0.5
		// https://www.phpbb.com/customise/db/extension/authorized_for_urls_2/
		if ($this->authorized_for_urls !== null && is_callable([$this->authorized_for_urls, 'check_text']))
		{
			$authorized_for_urls_lang_args = $this->authorized_for_urls->check_text($message, true);

			if ($authorized_for_urls_lang_args)
			{
				$authorized_for_urls_lang_key = array_shift($authorized_for_urls_lang_args);
				throw new http_exception(400, $authorized_for_urls_lang_key, $authorized_for_urls_lang_args);
			}
		}

		if ($this->mchat_settings->cfg('mchat_override_min_post_chars'))
		{
			$this->mchat_settings->set_cfg('min_post_chars', 0, true);
		}

		if ($this->mchat_settings->cfg('mchat_override_smilie_limit'))
		{
			$this->mchat_settings->set_cfg('max_post_smilies', 0, true);
		}

		$disallowed_bbcodes = array_filter(explode('|', $this->mchat_settings->cfg('mchat_bbcode_disallowed')));

		$mchat_bbcode		= $this->mchat_settings->cfg('allow_bbcode') && $this->auth->acl_get('u_mchat_bbcode');
		$mchat_magic_urls	= $this->mchat_settings->cfg('allow_post_links') && $this->auth->acl_get('u_mchat_urls');
		$mchat_smilies		= $this->mchat_settings->cfg('allow_smilies') && $this->auth->acl_get('u_mchat_smilies');

		$mchat_img = $mchat_flash = $mchat_quote = $mchat_url = $mchat_bbcode;

		// Disallowed bbcodes
		if ($disallowed_bbcodes)
		{
			$mchat_img		&= !in_array('img', $disallowed_bbcodes);
			$mchat_flash	&= !in_array('flash', $disallowed_bbcodes);
			$mchat_quote	&= !in_array('quote', $disallowed_bbcodes);
			$mchat_url		&= !in_array('url', $disallowed_bbcodes);

			foreach ($disallowed_bbcodes as $bbcode)
			{
				$this->textformatter_parser->disable_bbcode($bbcode);
			}
		}

		/**
		 * Event to modify the raw mChat message before it is processed
		 *
		 * @event dmzx.mchat.process_message_before
		 * @var string	message					The raw message as entered by the user
		 * @var string	message_without_bbcode	The message stripped of all BBCode tags
		 * @var array	disallowed_bbcodes		The list of disallowed BBCode tags
		 * @var bool	mchat_img				Whether or not the img BBCode is allowed
		 * @var	bool	mchat_flash				Whether or not the flash BBCode is allowed
		 * @var bool	mchat_quote				Whether or not the quote BBCode is allowed
		 * @var bool	mchat_url				Whether or not the url BBCode is allowed
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'message',
			'message_without_bbcode',
			'disallowed_bbcodes',
			'mchat_img',
			'mchat_flash',
			'mchat_quote',
			'mchat_url',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.process_message_before', compact($vars)));

		$uid = $bitfield = $options = '';
		generate_text_for_storage($message, $uid, $bitfield, $options, $mchat_bbcode, $mchat_magic_urls, $mchat_smilies, $mchat_img, $mchat_flash, $mchat_quote, $mchat_url, 'mchat');

		return [
			'message'			=> str_replace("'", '&#39;', $message),
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'bbcode_options'	=> $options,
		];
	}

	/**
	 * @param bool $custom_bbcodes_generated
	 */
	public function set_custom_bbcodes_generated($custom_bbcodes_generated)
	{
		$this->custom_bbcodes_generated = $custom_bbcodes_generated;
	}

	/**
	 * @param bool $smilies_generated
	 */
	public function set_smilies_generated($smilies_generated)
	{
		$this->smilies_generated = $smilies_generated;
	}

	/**
	 * Renders a template file and returns it
	 *
	 * @param string $template_file
	 * @return string
	 */
	public function render_template($template_file)
	{
		$this->template->set_filenames(['body' => $template_file]);
		$content = $this->template->assign_display('body', '', true);

		return trim($content);
	}
}

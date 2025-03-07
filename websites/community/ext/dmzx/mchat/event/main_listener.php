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

use dmzx\mchat\core\mchat;
use dmzx\mchat\core\notifications;
use phpbb\controller\helper;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\user;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class main_listener implements EventSubscriberInterface
{
	/** @var mchat */
	protected $mchat;

	/** @var notifications */
	protected $mchat_notifications;

	/** @var helper */
	protected $helper;

	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var request_interface */
	protected $request;

	/** @var string */
	protected $php_ext;

	/**
	 * Constructor
	 *
	 * @param mchat				$mchat
	 * @param notifications		$mchat_notifications
	 * @param helper			$helper
	 * @param user				$user
	 * @param language			$lang
	 * @param request_interface	$request
	 * @param string			$php_ext
	 */
	public function __construct(
		mchat $mchat,
		notifications $mchat_notifications,
		helper $helper,
		user $user,
		language $lang,
		request_interface $request,
		$php_ext
	)
	{
		$this->mchat				= $mchat;
		$this->mchat_notifications	= $mchat_notifications;
		$this->helper				= $helper;
		$this->user					= $user;
		$this->lang					= $lang;
		$this->request				= $request;
		$this->php_ext				= $php_ext;
	}

	/**
	 * @return array
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.viewonline_overwrite_location'		=> 'add_page_viewonline',
			'core.user_setup'							=> 'load_language_on_setup',
			'core.page_header'							=> 'add_page_header_link',
			'core.index_modify_page_title'				=> 'display_mchat_on_index',
			'core.submit_post_end'						=> 'insert_posting',
			'core.delete_posts_after'					=> 'delete_posts_after',
			'core.display_custom_bbcodes_modify_sql'	=> [['remove_disallowed_bbcodes'], ['pm_compose_add_quote']],
			'core.generate_smilies_after'				=> 'generate_smilies_after',
			'core.user_add_modify_data'					=> 'user_registration_set_default_values',
			'core.mcp_change_poster_after'				=> 'mcp_change_poster_after',
			'core.login_box_redirect'					=> 'user_login_success',
			'core.session_gc_after'						=> 'session_gc',
		];
	}

	/**
	 * @param data $event
	 */
	public function add_page_viewonline(data $event)
	{
		if (strrpos($event['row']['session_page'], 'app.' . $this->php_ext . '/mchat') === 0)
		{
			$event['location'] = $this->lang->lang('MCHAT_TITLE');
			$event['location_url'] = $this->helper->route('dmzx_mchat_page_custom_controller');
		}
	}

	/**
	 * @param data $event
	 */
	public function load_language_on_setup(data $event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name' => 'dmzx/mchat',
			'lang_set' => 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Create a URL to the mchat controller file for the header linklist
	 */
	public function add_page_header_link()
	{
		$this->mchat->render_page_header_link();
	}

	/**
	 * Check if mchat should be displayed on index.
	 */
	public function display_mchat_on_index()
	{
		$this->mchat->page_index();
	}

	/**
	 * @param data $event
	 */
	public function insert_posting(data $event)
	{
		$this->mchat_notifications->insert_post($event['mode'], $event['data']['forum_id'], $event['data']['post_id']);
	}

	/**
	 * @param data $event
	 */
	public function delete_posts_after(data $event)
	{
		$this->mchat_notifications->delete_post_notifications($event['post_ids']);
	}

	/**
	 * @param data $event
	 */
	public function remove_disallowed_bbcodes(data $event)
	{
		$event['sql_ary'] = $this->mchat->remove_disallowed_bbcodes($event['sql_ary']);

		$this->mchat->set_custom_bbcodes_generated(true);
	}

	/**
	 *
	 */
	public function generate_smilies_after()
	{
		$this->mchat->set_smilies_generated(true);
	}

	/**
	 * @param data $event
	 */
	public function user_registration_set_default_values(data $event)
	{
		$event['sql_ary'] = $this->mchat->set_user_default_values($event['sql_ary']);
	}

	/**
	 * @param data $event
	 */
	public function mcp_change_poster_after(data $event)
	{
		$this->mchat_notifications->update_post_notification_user($event['post_info']['post_id'], $event['userdata']['user_id']);
	}

	/**
	 * @param data $event
	 */
	public function user_login_success(data $event)
	{
		if (!$event['admin'])
		{
			$is_hidden = $this->request->is_set_post('viewonline') || !$this->user->data['user_allow_viewonline'];
			$this->mchat_notifications->insert_login($is_hidden);
		}
	}

	/**
	 *
	 */
	public function pm_compose_add_quote()
	{
		$mchat_message_id = $this->request->variable('mchat_pm_quote_message', 0);

		if ($mchat_message_id)
		{
			$this->mchat->quote_message_text($mchat_message_id);
		}
	}

	/**
	 *
	 */
	public function session_gc()
	{
		$this->mchat->session_gc();
	}
}

<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\acp;

/**
 * Profile Flair main ACP module.
 */
class main_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	/**
	 * @var \Symfony\Component\DependencyInjection\ContainerInterface
	 */
	protected $container;

	/**
	 * @var \phpbb\language\language
	 */
	protected $language;

	/**
	 * @var \phpbb\request\request
	 */
	protected $request;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$this->container = $phpbb_container;
		$this->language = $phpbb_container->get('language');
		$this->request = $phpbb_container->get('request');

		switch ($mode)
		{
			case 'manage':
				$this->manage();
			break;
			case 'images':
				$this->images();
			break;
			default:
				$this->settings();
			break;
		}
	}

	/**
	 * Handle the settings mode of the module.
	 */
	protected function settings()
	{
		$this->tpl_name = 'settings';
		$this->page_title = 'ACP_FLAIR_SETTINGS_TITLE';

		$config = $this->container->get('config');
		$template = $this->container->get('template');

		add_form_key('stevotvr_flair_settings');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('stevotvr_flair_settings'))
			{
				trigger_error('FORM_INVALID');
			}

			$notify_users = $this->request->variable('flair_notify_users', '');
			if (strlen($notify_users))
			{
				if (!$notify_users && $config['stevotvr_flair_notify_users'])
				{
					$db = $this->container->get('dbal.conn');
					$notif_table = $this->container->getParameter('stevotvr.flair.tables.flair_notifications');
					$db->sql_query('DELETE FROM ' . $notif_table);
				}

				$config->set('stevotvr_flair_notify_users', $notify_users ? 1 : 0);
			}

			$show_on_profile = $this->request->variable('flair_show_on_profile', '');
			if (strlen($show_on_profile))
			{
				$config->set('stevotvr_flair_show_on_profile', $show_on_profile ? 1 : 0);
			}

			$show_on_posts = $this->request->variable('flair_show_on_posts', '');
			if (strlen($show_on_posts))
			{
				$config->set('stevotvr_flair_show_on_posts', $show_on_posts ? 1 : 0);
			}

			$display_limit = $this->request->variable('flair_display_limit', '');
			if (strlen($display_limit))
			{
				$config->set('stevotvr_flair_display_limit', (int) $display_limit);
			}

			trigger_error($this->language->lang('ACP_FLAIR_SETTINGS_SAVED') . adm_back_link($this->u_action));
		}

		$template->assign_vars(array(
			'S_SETTINGS_MODE'		=> true,

			'FLAIR_NOTIFY_USERS'	=> $config['stevotvr_flair_notify_users'],
			'FLAIR_SHOW_ON_PROFILE'	=> $config['stevotvr_flair_show_on_profile'],
			'FLAIR_SHOW_ON_POSTS'	=> $config['stevotvr_flair_show_on_posts'],
			'FLAIR_DISPLAY_LIMIT'	=> $config['stevotvr_flair_display_limit'],

			'U_ACTION'	=> $this->u_action,
		));
	}

	/**
	 * Handle the manage mode of the module.
	 */
	protected function manage()
	{
		$this->tpl_name = 'manage';

		$action = $this->request->variable('action', '');

		switch ($action)
		{
			case 'add_cat':
			case 'edit_cat':
			case 'delete_cat':
				$this->manage_cats($action);
				return;
			break;
			case 'move_cat_up':
			case 'move_cat_down':
				$this->manage_cats($action);
			break;
			case 'add':
			case 'edit':
				$this->manage_flair($action);
				return;
			break;
			case 'delete':
			case 'move_up':
			case 'move_down':
				$this->manage_flair($action);
			break;
		}

		$controller = $this->container->get('stevotvr.flair.controller.acp.main');
		$controller->set_page_url($this->u_action);

		$this->page_title = 'ACP_FLAIR_MANAGE';
		$controller->display_flair();
	}

	/**
	 * Handle the category management actions.
	 *
	 * @param string $action The action parameter
	 */
	protected function manage_cats($action)
	{
		$controller = $this->container->get('stevotvr.flair.controller.acp.cats');
		$controller->set_page_url($this->u_action);

		$cat_id = $this->request->variable('cat_id', 0);

		switch ($action)
		{
			case 'add_cat':
				$this->page_title = 'ACP_FLAIR_ADD_CAT';
				$controller->add_cat();
			break;
			case 'edit_cat':
				$this->page_title = 'ACP_FLAIR_EDIT_CAT';
				$controller->edit_cat($cat_id);
			break;
			case 'delete_cat':
				$this->page_title = 'ACP_FLAIR_DELETE_CAT';
				$controller->delete_cat($cat_id);
			break;
			case 'move_cat_up':
				$controller->move_cat($cat_id, -1);
			break;
			case 'move_cat_down':
				$controller->move_cat($cat_id, 1);
			break;
		}
	}

	/**
	 * Handle the flair management actions.
	 *
	 * @param string $action The action parameter
	 */
	protected function manage_flair($action)
	{
		$controller = $this->container->get('stevotvr.flair.controller.acp.flair');
		$controller->set_page_url($this->u_action);

		$flair_id = $this->request->variable('flair_id', 0);

		switch ($action)
		{
			case 'add':
				$this->page_title = 'ACP_FLAIR_ADD';
				$controller->add_flair();
			break;
			case 'edit':
				$this->page_title = 'ACP_FLAIR_EDIT';
				$controller->edit_flair($flair_id);
			break;
			case 'delete':
				$controller->delete_flair($flair_id);
			break;
			case 'move_up':
				$controller->move_flair($flair_id, -1);
			break;
			case 'move_down':
				$controller->move_flair($flair_id, 1);
			break;
		}
	}

	/**
	 * Handle the image management actions.
	 */
	protected function images()
	{
		$this->tpl_name = 'images';

		$controller = $this->container->get('stevotvr.flair.controller.acp.images');
		$controller->set_page_url($this->u_action);

		$action = $this->request->variable('action', '');

		switch ($action)
		{
			case 'add':
				$controller->add_image();
			break;
			case 'delete':
				$controller->delete_image($this->request->variable('image_name', ''));
			break;
		}

		$this->page_title = 'ACP_FLAIR_IMAGES';
		$controller->list_images();
	}
}

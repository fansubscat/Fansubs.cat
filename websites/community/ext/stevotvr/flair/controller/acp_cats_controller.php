<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\config\config;
use phpbb\json_response;
use stevotvr\flair\entity\category_interface as cat_entity;
use stevotvr\flair\exception\base;
use stevotvr\flair\operator\category_interface as cat_operator;

/**
 * Profile Flair category management ACP controller.
 */
class acp_cats_controller extends acp_base_controller implements acp_cats_interface
{
	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var cat_operator
	 */
	protected $cat_operator;

	/**
	 * Set up the controller.
	 *
	 * @param config       $config
	 * @param cat_operator $cat_operator
	 */
	public function setup(config $config, cat_operator $cat_operator)
	{
		$this->config = $config;
		$this->cat_operator = $cat_operator;
	}

	/**
	 * @inheritDoc
	 */
	public function add_cat()
	{
		$entity = $this->container->get('stevotvr.flair.entity.category');

		$show_on_profile = $this->config['stevotvr_flair_show_on_profile'];
		$show_on_posts = $this->config['stevotvr_flair_show_on_posts'];
		$display_limit = $this->config['stevotvr_flair_display_limit'];

		$entity->set_show_on_profile($show_on_profile);
		$entity->set_show_on_posts($show_on_posts);
		$entity->set_display_limit($display_limit);

		$this->add_edit_cat_data($entity);
		$this->template->assign_vars(array(
			'S_ADD_CAT'	=> true,

			'U_ACTION'	=> $this->u_action . '&amp;action=add_cat',
		));
	}

	/**
	 * @inheritDoc
	 */
	public function edit_cat($cat_id)
	{
		try
		{
			$entity = $this->container->get('stevotvr.flair.entity.category')->load($cat_id);
			$this->add_edit_cat_data($entity);
			$this->template->assign_vars(array(
				'S_EDIT_CAT'	=> true,

				'U_ACTION'		=> $this->u_action . '&amp;action=edit_cat&amp;cat_id=' . $cat_id,
			));
		}
		catch (base $e)
		{
			trigger_error($e->get_message($this->language));
		}
	}

	/**
	 * Process data for the add/edit category form.
	 *
	 * @param cat_entity $entity The category being processed
	 */
	protected function add_edit_cat_data(cat_entity $entity)
	{
		$errors = array();

		$submit = $this->request->is_set_post('submit');

		add_form_key('add_edit_cat');

		$show_on_profile = $this->config['stevotvr_flair_show_on_profile'];
		$show_on_posts = $this->config['stevotvr_flair_show_on_posts'];
		$display_limit = $this->config['stevotvr_flair_display_limit'];

		$data = array(
			'name'				=> $this->request->variable('cat_name', '', true),
			'show_on_profile'	=> $this->request->variable('flair_show_on_profile', $show_on_profile),
			'show_on_posts'		=> $this->request->variable('flair_show_on_posts', $show_on_posts),
			'display_limit'		=> $this->request->variable('flair_display_limit', $display_limit),
		);

		if ($submit)
		{
			if (!check_form_key('add_edit_cat'))
			{
				$errors[] = 'FORM_INVALID';
			}

			foreach ($data as $name => $value)
			{
				try
				{
					$entity->{'set_' . $name}($value);
				}
				catch (base $e)
				{
					$errors[] = $e->get_message($this->language);
				}
			}

			if (empty($errors))
			{
				if ($entity->get_id())
				{
					$entity->save();
					$message = 'ACP_FLAIR_CATS_EDIT_SUCCESS';
				}
				else
				{
					$entity = $this->cat_operator->add_category($entity);
					$message = 'ACP_FLAIR_CATS_ADD_SUCCESS';
				}

				trigger_error($this->language->lang($message) . adm_back_link($this->u_action));
			}
		}

		$errors = array_map(array($this->language, 'lang'), $errors);

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($errors),
			'ERROR_MSG'	=> !empty($errors) ? implode('<br />', $errors) : '',

			'CAT_NAME'				=> $entity->get_name(),
			'FLAIR_SHOW_ON_PROFILE'	=> $entity->show_on_profile(),
			'FLAIR_SHOW_ON_POSTS'	=> $entity->show_on_posts(),
			'FLAIR_DISPLAY_LIMIT'	=> $entity->get_display_limit(),

			'U_BACK'	=> $this->u_action . '&amp;cat_id=' . $entity->get_id(),
		));
	}

	/**
	 * @inheritDoc
	 */
	public function delete_cat($cat_id)
	{
		$errors = array();

		add_form_key('delete_cat');

		if ($this->request->is_set_post('submit'))
		{
			if (!check_form_key('delete_cat'))
			{
				$errors[] = $this->language->lang('FORM_INVALID');
			}

			$action_flair = $this->request->variable('action_flair', '');
			$flair_to_cat = $this->request->variable('flair_to_cat', 0);

			try
			{
				if ($action_flair === 'delete')
				{
					$this->cat_operator->delete_flair($cat_id);
				}
				else
				{
					$this->cat_operator->reassign_flair($cat_id, $flair_to_cat);
				}

				$this->cat_operator->delete_category($cat_id);
			}
			catch (base $e)
			{
				trigger_error($this->language->lang('ACP_FLAIR_CATS_DELETE_ERRORED') . adm_back_link($this->u_action), E_USER_WARNING);
			}

			trigger_error($this->language->lang('ACP_FLAIR_CATS_DELETE_SUCCESS') . adm_back_link($this->u_action));
		}

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($errors),
			'ERROR_MSG'	=> !empty($errors) ? implode('<br />', $errors) : '',

			'S_DELETE_CAT'	=> true,
			'S_HAS_FLAIR'	=> true,

			'CAT_ID'	=> $cat_id,

			'U_ACTION'	=> $this->u_action . '&amp;action=delete_cat&amp;cat_id=' . $cat_id,
			'U_BACK'	=> $this->u_action,
		));

		$categories = $this->cat_operator->get_categories();
		foreach ($categories as $category)
		{
			if ($category->get_id() === $cat_id)
			{
				$this->template->assign_var('CAT_NAME', $category->get_name());
				continue;
			}

			$this->template->assign_block_vars('cats', array(
				'CAT_ID'	=> $category->get_id(),
				'CAT_NAME'	=> $category->get_name(),
			));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function move_cat($cat_id, $offset)
	{
		$this->cat_operator->move_category($cat_id, $offset);

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array('success' => true));
		}
	}
}

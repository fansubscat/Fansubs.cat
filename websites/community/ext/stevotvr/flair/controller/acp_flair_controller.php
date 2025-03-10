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

use phpbb\db\driver\driver_interface;
use phpbb\event\dispatcher_interface;
use phpbb\group\helper;
use phpbb\json_response;
use stevotvr\flair\entity\flair_interface as flair_entity;
use stevotvr\flair\exception\base;
use stevotvr\flair\operator\category_interface as cat_operator;
use stevotvr\flair\operator\flair_interface as flair_operator;
use stevotvr\flair\operator\image_interface as image_operator;
use stevotvr\flair\operator\trigger_interface as trigger_operator;

/**
 * Profile Flair flair management ACP controller.
 */
class acp_flair_controller extends acp_base_controller implements acp_flair_interface
{
	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var helper
	 */
	protected $group_helper;

	/**
	 * @var cat_operator
	 */
	protected $cat_operator;

	/**
	 * @var flair_operator
	 */
	protected $flair_operator;

	/**
	 * @var image_operator
	 */
	protected $image_operator;

	/**
	 * @var trigger_operator
	 */
	protected $trigger_operator;

	/**
	 * The array of default trigger names.
	 *
	 * @var array
	 */
	protected $trigger_names;

	/**
	 * Set up the controller.
	 *
	 * @param driver_interface $db
	 * @param helper           $group_helper
	 * @param cat_operator     $cat_operator
	 * @param flair_operator   $flair_operator
	 * @param image_operator   $image_operator
	 * @param trigger_operator $trigger_operator
	 */
	public function setup(driver_interface $db, helper $group_helper, cat_operator $cat_operator, flair_operator $flair_operator, image_operator $image_operator, trigger_operator $trigger_operator)
	{
		$this->db = $db;
		$this->group_helper = $group_helper;
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
		$this->image_operator = $image_operator;
		$this->trigger_operator = $trigger_operator;

		$this->language->add_lang('posting');
	}

	/**
	 * @param dispatcher_interface $dispatcher
	 * @param array                $trigger_names Array of default trigger names
	 */
	public function set_trigger_names(dispatcher_interface $dispatcher, array $trigger_names)
	{
		/**
		 * Load the list of available triggers.
		 *
		 * @event stevotvr.flair.load_triggers
		 * @var array trigger_names The list of trigger names
		 * @since 0.2.0
		 */
		$vars = array('trigger_names');
		extract($dispatcher->trigger_event('stevotvr.flair.load_triggers', compact($vars)));
		$this->trigger_names = $trigger_names;
	}

	/**
	 * @inheritDoc
	 */
	public function add_flair()
	{
		$entity = $this->container->get('stevotvr.flair.entity.flair');
		$entity->set_category($this->request->variable('cat_id', 0));
		$this->add_edit_flair_data($entity);
		$this->template->assign_vars(array(
			'S_ADD_FLAIR'	=> true,

			'U_ACTION'		=> $this->u_action . '&amp;action=add&amp;cat_id=' . $entity->get_category(),
		));
	}

	/**
	 * @inheritDoc
	 */
	public function edit_flair($flair_id)
	{
		try
		{
			$entity = $this->container->get('stevotvr.flair.entity.flair')->load($flair_id);
			$this->add_edit_flair_data($entity);
			$this->template->assign_vars(array(
				'S_EDIT_FLAIR'	=> true,

				'U_ACTION'		=> $this->u_action . '&amp;action=edit&amp;cat_id=' . $entity->get_category() . '&amp;flair_id=' . $flair_id,
			));
		}
		catch (base $e)
		{
			trigger_error($e->get_message($this->language));
		}
	}

	/**
	 * Process data for the add/edit flair form.
	 *
	 * @param flair_entity $entity The flair item being processed
	 */
	protected function add_edit_flair_data(flair_entity $entity)
	{
		$errors = array();

		$submit = $this->request->is_set_post('submit');

		add_form_key('add_edit_flair');

		$data = array(
			'type'			=> $this->request->variable('flair_type', 0),
			'category'		=> $this->request->variable('flair_category', 0),
			'name'			=> $this->request->variable('flair_name', '', true),
			'desc'			=> $this->request->variable('flair_desc', '', true),
			'color'			=> $this->request->variable('flair_color', ''),
			'icon'			=> $this->request->variable('flair_icon', ''),
			'icon_color'	=> $this->request->variable('flair_icon_color', ''),
			'font_color'	=> $this->request->variable('flair_font_color', ''),
			'img'			=> $this->request->variable('flair_img', ''),
			'groups_auto'	=> $this->request->variable('flair_groups_auto', true),
		);

		$this->set_parse_options($entity, $submit);

		if ($submit)
		{
			$this->validate_form($data, $errors);

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
					$message = 'ACP_FLAIR_EDIT_SUCCESS';
				}
				else
				{
					$entity = $this->flair_operator->add_flair($entity);
					$message = 'ACP_FLAIR_ADD_SUCCESS';
				}

				$this->add_edit_triggers($entity->get_id(), $errors);
				$this->flair_operator->assign_groups($entity->get_id(), $this->request->variable('flair_groups', array(0)));

				if (empty($errors))
				{
					trigger_error($this->language->lang($message) . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()));
				}
			}
		}

		$errors = array_map(array($this->language, 'lang'), $errors);

		$this->template->assign_vars(array(
			'S_ERROR'	=> !empty($errors),
			'ERROR_MSG'	=> !empty($errors) ? implode('<br />', $errors) : '',

			'FLAIR_TYPE'		=> $entity->get_type(),
			'FLAIR_CATEGORY'	=> $entity->get_category(),
			'FLAIR_NAME'		=> $entity->get_name(),
			'FLAIR_DESC'		=> $entity->get_desc_for_edit(),
			'FLAIR_COLOR'		=> $entity->get_color(),
			'FLAIR_ICON'		=> $entity->get_icon(),
			'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
			'FLAIR_IMG'			=> $entity->get_img(1),
			'FLAIR_IMG_X2'		=> $entity->get_img(2),
			'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),
			'FLAIR_GROUPS_AUTO'	=> $entity->is_groups_auto(),
			'FLAIR_IMG_PATH'	=> $this->img_path,

			'S_PARSE_BBCODE_CHECKED'	=> $entity->is_bbcode_enabled(),
			'S_PARSE_SMILIES_CHECKED'	=> $entity->is_smilies_enabled(),
			'S_PARSE_MAGIC_URL_CHECKED'	=> $entity->is_magic_url_enabled(),

			'U_BACK'	=> $this->u_action . '&amp;cat_id=' . $entity->get_category(),
		));

		$this->load_img_select_data($entity->get_img());
		$this->load_cat_select_data($entity->get_category());
		$this->load_triggers($entity->get_id());
		$this->load_groups($entity->get_id());
	}

	/**
	 * Process the triggers portion of the add/edit flair form.
	 *
	 * @param int   $flair_id The flair item ID
	 * @param array &$errors  The array to populate with error strings
	 */
	protected function add_edit_triggers($flair_id, array &$errors)
	{
		$triggers = $this->request->variable('flair_triggers', array('' => 0));
		foreach ($triggers as $name => $value)
		{
			if (!in_array($name, $this->trigger_names))
			{
				continue;
			}

			try
			{
				$this->trigger_operator->set_trigger($flair_id, $name, $value);
			}
			catch (base $e)
			{
				$errors[] = $e->get_message($this->language);
			}
		}
	}

	/**
	 * Load the triggers into template block variables.
	 *
	 * @param int $flair_id The flair item ID
	 */
	protected function load_triggers($flair_id)
	{
		$triggers = $this->trigger_operator->get_flair_triggers($flair_id);
		$triggers = array_merge($triggers, $this->request->variable('flair_triggers', array('' => 0)));

		foreach ($this->trigger_names as $name)
		{
			$lang_key = 'ACP_FLAIR_TRIGGER_' . strtoupper($name);
			$explain = $this->language->is_set($lang_key . '_EXPLAIN') ? $this->language->lang($lang_key . '_EXPLAIN') : null;
			$this->template->assign_block_vars('trigger', array(
				'TRIG_KEY'		=> $name,
				'TRIG_NAME'		=> $this->language->lang($lang_key),
				'TRIG_EXPLAIN'	=> $explain,
				'TRIG_VALUE'	=> isset($triggers[$name]) ? $triggers[$name] : '',
			));
		}
	}

	/**
	 * Load the groups into template block variables.
	 *
	 * @param int $flair_id The flair item ID
	 */
	protected function load_groups($flair_id)
	{
		$groups = array();

		$selected = $this->flair_operator->get_assigned_groups($flair_id);
		$selected = array_merge($selected, $this->request->variable('flair_groups', array(0)));

		$sql = 'SELECT group_id, group_name
				FROM ' . GROUPS_TABLE;
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$groups[] = array(
				'GROUP_ID'		=> (int) $row['group_id'],
				'GROUP_NAME'	=> $this->group_helper->get_name($row['group_name']),

				'S_SELECTED'	=> in_array((int) $row['group_id'], $selected),
			);
		}
		$this->db->sql_freeresult();

		$names = array();
		foreach ($groups as $group)
		{
			$names[] = $group['GROUP_NAME'];
		}
		array_multisort($names, SORT_ASC, SORT_NATURAL | SORT_FLAG_CASE, $groups);
		foreach ($groups as $group)
		{
			$this->template->assign_block_vars('group', $group);
		}
	}

	/**
	 * Process parsing options for the flair description field.
	 *
	 * @param flair_entity $entity The flair item being processed
	 * @param boolean      $submit The form has been submitted
	 */
	protected function set_parse_options(flair_entity $entity, $submit)
	{
		$bbcode = $this->request->variable('parse_bbcode', false);
		$magic_url = $this->request->variable('parse_magic_url', false);
		$smilies = $this->request->variable('parse_smilies', false);

		$parse_options = array(
			'bbcode'	=> $submit ? $bbcode : ($entity->get_id() ? $entity->is_bbcode_enabled() : 1),
			'magic_url'	=> $submit ? $magic_url : ($entity->get_id() ? $entity->is_magic_url_enabled() : 1),
			'smilies'	=> $submit ? $smilies : ($entity->get_id() ? $entity->is_smilies_enabled() : 1),
		);

		foreach ($parse_options as $function => $enabled)
		{
			$entity->{'set_' . $function . '_enabled'}($enabled);
		}
	}

	/**
	 * Validate the add/edit flair form.
	 *
	 * @param array &$data   The form data
	 * @param array &$errors The array to populate with error strings
	 */
	protected function validate_form(array &$data, array &$errors)
	{
		if (!check_form_key('add_edit_flair', -1))
		{
			$errors[] = 'FORM_INVALID';
		}

		if ($data['type'] === flair_entity::TYPE_FA)
		{
			if ($data['color'] === '' && $data['icon'] === '')
			{
				$errors[] = 'ACP_ERROR_APPEARANCE_REQUIRED';
			}

			if ($data['icon'] !== '')
			{
				$icon = strtolower(trim($data['icon']));
				if (substr($icon, 0, 3) !== 'fa-')
				{
					$icon = 'fa-' . $icon;
				}
				$data['icon'] = $icon;
			}
		}
		else if ($data['type'] === flair_entity::TYPE_IMG && $data['img'] === '')
		{
			$errors[] = 'ACP_ERROR_IMG_REQUIRED';
		}
	}

	/**
	 * Load the template data for the image select box.
	 *
	 * @param int $selected The selected item
	 */
	protected function load_img_select_data($selected)
	{
		foreach ($this->image_operator->get_images() as $name)
		{
			$this->template->assign_block_vars('imgs', array(
				'IMG_NAME'	=> $name,

				'S_SELECTED'	=> $name === $selected,
			));
		}
	}

	/**
	 * Load the template data for the category select box.
	 *
	 * @param int $selected The ID of the selected item
	 */
	protected function load_cat_select_data($selected)
	{
		$categories = $this->cat_operator->get_categories();
		if (empty($categories))
		{
			return;
		}

		foreach ($categories as $category)
		{
			$this->template->assign_block_vars('cats', array(
				'CAT_ID'	=> $category->get_id(),
				'CAT_NAME'	=> $category->get_name(),

				'S_SELECTED'	=> $category->get_id() === $selected,
			));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function delete_flair($flair_id)
	{
		try
		{
			$entity = $this->container->get('stevotvr.flair.entity.flair')->load($flair_id);

			if (!confirm_box(true))
			{
				$hidden_fields = build_hidden_fields(array(
					'flair_id'	=> $flair_id,
					'cat_id'	=> $entity->get_category(),
					'mode'		=> 'manage',
					'action'	=> 'delete',
				));
				confirm_box(false, $this->language->lang('ACP_FLAIR_DELETE_FLAIR_CONFIRM'), $hidden_fields);
				return;
			}

			try
			{
				$this->flair_operator->delete_flair($flair_id);
			}
			catch (base $e)
			{
				trigger_error($this->language->lang('ACP_FLAIR_DELETE_ERRORED') . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()), E_USER_WARNING);
			}

			if ($this->request->is_ajax())
			{
				$json_response = new json_response();
				$json_response->send(array(
					'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
					'MESSAGE_TEXT'	=> $this->language->lang('ACP_FLAIR_DELETE_SUCCESS'),
					'REFRESH_DATA'	=> array(
						'time'	=> 3
					),
				));
			}

			trigger_error($this->language->lang('ACP_FLAIR_DELETE_SUCCESS') . adm_back_link($this->u_action . '&amp;cat_id=' . $entity->get_category()));
		}
		catch (base $e)
		{
			trigger_error($e->get_message($this->language));
		}
	}

	/**
	 * @inheritDoc
	 */
	public function move_flair($flair_id, $offset)
	{
		$this->flair_operator->move_flair($flair_id, $offset);

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array('success' => true));
		}
	}
}

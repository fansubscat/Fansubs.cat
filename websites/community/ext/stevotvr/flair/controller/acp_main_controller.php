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

use stevotvr\flair\exception\base;
use stevotvr\flair\operator\category_interface as cat_operator;
use stevotvr\flair\operator\flair_interface as flair_operator;

/**
 * Profile Flair main ACP controller.
 */
class acp_main_controller extends acp_base_controller implements acp_main_interface
{
	/**
	 * @var cat_operator
	 */
	protected $cat_operator;

	/**
	 * @var flair_operator
	 */
	protected $flair_operator;

	/**
	 * Set up the controller.
	 *
	 * @param cat_operator   $cat_operator
	 * @param flair_operator $flair_operator
	 */
	public function setup(cat_operator $cat_operator, flair_operator $flair_operator)
	{
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;
	}

	/**
	 * @inheritDoc
	 */
	public function display_flair()
	{
		$cat_id = $this->request->variable('cat_id', 0);

		$entities = $this->flair_operator->get_flair($cat_id);
		foreach ($entities as $entity)
		{
			$this->template->assign_block_vars('flair', array(
				'FLAIR_TYPE'		=> $entity->get_type(),
				'FLAIR_NAME'		=> $entity->get_name(),
				'FLAIR_COLOR'		=> $entity->get_color(),
				'FLAIR_ICON'		=> $entity->get_icon(),
				'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
				'FLAIR_IMG'			=> $this->img_path . $entity->get_img(1),
				'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),

				'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_up&amp;cat_id=' . $cat_id . '&amp;flair_id=' . $entity->get_id(),
				'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_down&amp;cat_id=' . $cat_id . '&amp;flair_id=' . $entity->get_id(),
				'U_EDIT'	=> $this->u_action . '&amp;action=edit&amp;flair_id=' . $entity->get_id(),
				'U_DELETE'	=> $this->u_action . '&amp;action=delete&amp;flair_id=' . $entity->get_id(),
			));
		}

		if (!$cat_id)
		{
			$cat_name = $this->language->lang('FLAIR_UNCATEGORIZED');

			$entities = $this->cat_operator->get_categories();
			foreach ($entities as $entity)
			{
				$display_on = array();
				if ($entity->show_on_profile())
				{
					$display_on[] = 'ACP_FLAIR_PROFILE';
				}
				if ($entity->show_on_posts())
				{
					$display_on[] = 'ACP_FLAIR_POSTS';
				}
				$display_on = array_map(array($this->language, 'lang'), $display_on);

				$this->template->assign_block_vars('cats', array(
					'CAT_NAME'		=> $entity->get_name(),
					'DISPLAY_ON'	=> implode(', ', $display_on),

					'U_FLAIR'		=> $this->u_action . '&amp;cat_id=' . $entity->get_id(),
					'U_MOVE_UP'		=> $this->u_action . '&amp;action=move_cat_up&amp;cat_id=' . $entity->get_id(),
					'U_MOVE_DOWN'	=> $this->u_action . '&amp;action=move_cat_down&amp;cat_id=' . $entity->get_id(),
					'U_EDIT'		=> $this->u_action . '&amp;action=edit_cat&amp;cat_id=' . $entity->get_id(),
					'U_DELETE'		=> $this->u_action . '&amp;action=delete_cat&amp;cat_id=' . $entity->get_id(),
				));
			}
		}
		else
		{
			try
			{
				$cat_name = $this->container->get('stevotvr.flair.entity.category')->load($cat_id)->get_name();
			}
			catch (base $e)
			{
				trigger_error($e->get_message($this->language));
			}
		}

		$this->template->assign_vars(array(
			'S_IN_CAT'	=> (bool) $cat_id,

			'CAT_NAME'	=> $cat_name,

			'U_ACTION'		=> $this->u_action,
			'U_ADD_CAT'		=> $this->u_action . '&amp;action=add_cat',
			'U_ADD_FLAIR'	=> $this->u_action . '&amp;action=add&amp;cat_id=' . $cat_id,
		));
	}
}

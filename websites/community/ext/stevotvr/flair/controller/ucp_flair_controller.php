<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\json_response;
use phpbb\user;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\user_interface;

/**
 * Profile Flair UCP controller.
 */
class ucp_flair_controller extends acp_base_controller implements ucp_flair_interface
{
	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var user_interface
	 */
	protected $user_operator;

	/**
	 * Set up the controller.
	 *
	 * @param user            $user
	 * @param flair_interface $flair_operator
	 * @param user_interface  $user_operator
	 */
	public function setup(user $user, flair_interface $flair_operator, user_interface $user_operator)
	{
		$this->user = $user;
		$this->flair_operator = $flair_operator;
		$this->user_operator = $user_operator;
	}

	/**
	 * @inheritDoc
	 */
	public function edit_flair()
	{
		$user_id = (int) $this->user->data['user_id'];

		$user_flair = $this->user_operator->get_user_flair((array) $user_id);
		$user_flair = isset($user_flair[$user_id]) ? $user_flair[$user_id] : array();
		$user_flair_ids = array();
		foreach ($user_flair as $flair)
		{
			$user_flair_ids = array_merge($user_flair_ids, array_keys($flair['items']));
		}

		$group_memberships = group_memberships(false, $user_id);
		foreach ($group_memberships as $k => $group_membership)
		{
			$group_memberships[$k] = (int) $group_membership['group_id'];
		}
		$available_flair = $this->flair_operator->get_group_flair($group_memberships);
		$available_flair_ids = array();
		foreach ($available_flair as $cat_id => $category)
		{
			$available_flair_ids = array_merge($available_flair_ids, array_keys($category['items']));

			foreach ($category['items'] as $item_id => $item)
			{
				if (in_array($item_id, $user_flair_ids))
				{
					unset($available_flair[$cat_id]['items'][$item_id]);
				}
			}

			if (empty($available_flair[$cat_id]['items']))
			{
				unset($available_flair[$cat_id]);
			}
		}

		if ($this->request->is_set_post('add_flair'))
		{
			$this->change_flair('add', $available_flair_ids);
		}
		else if ($this->request->is_set_post('remove_flair'))
		{
			$this->change_flair('remove', $available_flair_ids);
		}
		else if ($this->request->is_set_post('fav_flair'))
		{
			$this->change_flair('fav', $user_flair_ids);
		}
		else if ($this->request->is_set_post('unfav_flair'))
		{
			$this->change_flair('unfav', $user_flair_ids);
		}

		foreach ($user_flair as $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('user_flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('user_flair.items', array(
					'S_IS_FREE'	=> in_array($entity->get_id(), $available_flair_ids),

					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_NAME_SHORT'	=> truncate_string($entity->get_name(), 30, 255, false, '…'),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),
					'FLAIR_FAV'			=> (bool) $item['priority'],
				));
			}
		}

		foreach ($available_flair as $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('available_flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('available_flair.items', array(
					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_NAME_SHORT'	=> truncate_string($entity->get_name(), 30, 255, false, '…'),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),
				));
			}
		}
	}

	/**
	 * Make a change to the flair assigned to the user.
	 *
	 * @param string $change          The type of change to make (add|remove|fav|unfav)
	 * @param array  $available_flair The array of available flair IDs
	 */
	protected function change_flair($change, array $available_flair)
	{
		$action = $this->request->variable($change . '_flair', array('' => ''));
		if (is_array($action))
		{
			foreach ($action as $id => $value)
			{
			}
		}

		if (in_array($id, $available_flair))
		{
			$user_id = (int) $this->user->data['user_id'];

			if ($change === 'add')
			{
				$this->user_operator->set_flair_count($user_id, $id, 1, false);
			}
			else if ($change === 'remove')
			{
				if (!confirm_box(true))
				{
					$hidden_fields = build_hidden_fields(array(
						'remove_flair[' . $id . ']'	=> true,
					));
					confirm_box(false, $this->language->lang('UCP_FLAIR_REMOVE_CONFIRM'), $hidden_fields);
					return;
				}

				$this->user_operator->set_flair_count($user_id, $id, 0, false);
				$this->user_operator->set_flair_favorite($user_id, $id, false);
			}
			else if ($change === 'fav')
			{
				$this->user_operator->set_flair_favorite($user_id, $id, true);
			}
			else if ($change === 'unfav')
			{
				$this->user_operator->set_flair_favorite($user_id, $id, false);
			}

			if ($this->request->is_ajax())
			{
				$json_response = new json_response();
				$json_response->send(array(
					'REFRESH_DATA'	=> array(
						'url'	=> html_entity_decode($this->u_action),
					),
				));
			}
		}

		redirect($this->u_action);
	}
}

<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\event;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\db\driver\driver_interface;
use phpbb\event\data;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use stevotvr\flair\operator\flair_interface;
use stevotvr\flair\operator\trigger_interface;
use stevotvr\flair\operator\user_interface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Profile Flair event listener.
 */
class main_listener implements EventSubscriberInterface
{
	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * @var flair_interface
	 */
	protected $flair_operator;

	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var request_interface
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var trigger_interface
	 */
	protected $trigger_operator;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var user_interface
	 */
	protected $user_operator;

	/**
	 * The path to the custom images.
	 *
	 * @var string
	 */
	protected $img_path;

	/**
	 * @param config            $config
	 * @param driver_interface  $db
	 * @param helper            $helper
	 * @param language          $language
	 * @param request_interface $request
	 * @param template          $template
	 * @param user              $user
	 * @param flair_interface   $flair_operator
	 * @param trigger_interface $trigger_operator
	 * @param user_interface    $user_operator
	 * @param string            $img_path The path to the custom images
	 */
	public function __construct(config $config, driver_interface $db, helper $helper, language $language, request_interface $request, template $template, user $user, flair_interface $flair_operator, trigger_interface $trigger_operator, user_interface $user_operator, $img_path)
	{
		$this->config = $config;
		$this->db = $db;
		$this->helper = $helper;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
		$this->user = $user;
		$this->flair_operator = $flair_operator;
		$this->trigger_operator = $trigger_operator;
		$this->user_operator = $user_operator;
		$this->img_path = $img_path;
	}

	/**
	 * @inheritDoc
	 */
	static public function getSubscribedEvents()
	{
		return array(
			'core.permissions'					=> 'permissions',
			'core.user_setup'					=> 'user_setup',
			'core.memberlist_view_profile'		=> 'memberlist_view_profile',
			'core.submit_post_end'				=> 'submit_post_end',
			'core.viewtopic_modify_post_data'	=> 'viewtopic_modify_post_data',
			'core.viewtopic_post_row_after'		=> 'viewtopic_post_row_after',
			'core.delete_group_after'			=> 'delete_group_after',
			'core.delete_user_after'			=> 'delete_user_after',
		);
	}

	/**
	 * Adds the custom extension permissions.
	 *
	 * @param data $event The event data
	 */
	public function permissions(data $event)
	{
		$permissions = $event['permissions'];
		$permissions['m_userflair'] = array('lang' => 'ACL_M_MANAGE_FLAIR', 'cat' => 'misc');
		$permissions['u_flair'] = array('lang' => 'ACL_U_FLAIR', 'cat' => 'profile');
		$event['permissions'] = $permissions;
	}

	/**
	 * Adds the extension language set on user setup.
	 *
	 * @param data $event The event data
	 */
	public function user_setup(data $event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name'	=> 'stevotvr/flair',
			'lang_set'	=> 'common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Adds the user profile flair template variables to the view profile page.
	 *
	 * @param data $event The event data
	 */
	public function memberlist_view_profile(data $event)
	{
		if (!$this->config['stevotvr_flair_show_on_profile'])
		{
			return;
		}

		$user_id = $event['member']['user_id'];
		$username = $event['member']['username'];
		$user_flair = $this->user_operator->get_user_flair((array) $user_id, 'profile');

		if (!isset($user_flair[$user_id]))
		{
			return;
		}

		$this->template->assign_vars(array(
			'FLAIR_TITLE'		=> $this->language->lang('FLAIR_PROFILE_TITLE', $username),
		));

		foreach ($user_flair[$user_id] as $category)
		{
			$this->template->assign_block_vars('flair', array(
				'CAT_NAME'	=> $category['category']->get_name(),
			));

			foreach ($category['items'] as $item)
			{
				$entity = $item['flair'];
				$this->template->assign_block_vars('flair.item', array(
					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 2,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_DESC'		=> $entity->get_desc_for_display(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_ICON_WIDTH'	=> $entity->get_icon_width(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(2),
					'FLAIR_IMG_BIG'			=> $this->img_path . $entity->get_img(3),
					'FLAIR_FONT_COLOR'	=> $entity->get_font_color(),
					'FLAIR_COUNT'		=> $item['count'],
				));
			}
		}
	}

	/**
	 * Dispatch default triggers when a user makes a post.
	 *
	 * @param data $event The event data
	 */
	public function submit_post_end(data $event)
	{
		$user_id = $event['data']['poster_id'];
		$sql = 'SELECT user_regdate, user_posts
				FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $user_id . '
					AND user_type <> ' . USER_IGNORE;
		$this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow();
		$this->db->sql_freeresult();

		if (!$row)
		{
			return;
		}

		$post_count = (int) $row['user_posts'];
		$this->trigger_operator->dispatch($user_id, 'post_count', $post_count);

		$membership_days = (time() - (int) $row['user_regdate']) / 86400;
		$this->trigger_operator->dispatch($user_id, 'membership_days', $membership_days);
	}

	/**
	 * Loads all user profile flair data into the user cache for a topic.
	 *
	 * @param data $event The event data
	 */
	public function viewtopic_modify_post_data(data $event)
	{
		if (!$this->config['stevotvr_flair_show_on_posts'])
		{
			return;
		}

		$user_cache = $event['user_cache'];
		$user_flair = $this->user_operator->get_user_flair(array_keys($user_cache), 'posts');

		if (empty($user_flair))
		{
			return;
		}

		foreach ($user_flair as $user_id => $user)
		{
			foreach ($user as $category_id => $category)
			{
				$user_cache[$user_id]['flair'][$category_id]['category'] = $category['category']->get_name();

				foreach ($category['items'] as $item)
				{
					$entity = $item['flair'];
					$user_cache[$user_id]['flair'][$category_id]['items'][$entity->get_id()] = array(
						'type'			=> $entity->get_type(),
						'name'			=> $entity->get_name(),
						'desc'		=> $entity->get_desc_for_display(),
						'color'			=> $entity->get_color(),
						'icon'			=> $entity->get_icon(),
						'icon_color'	=> $entity->get_icon_color(),
						'icon_width'	=> $entity->get_icon_width(),
						'img'			=> $entity->get_img(1),
						'img_big'			=> $entity->get_img(3),
						'font_color'	=> $entity->get_font_color(),
						'count'			=> $item['count'],
					);
				}
			}
		}

		$event['user_cache'] = $user_cache;
	}

	/**
	 * Assigns user profile flair template block variables for a topic post.
	 *
	 * @param data $event The event data
	 */
	public function viewtopic_post_row_after(data $event)
	{
		if (!$this->config['stevotvr_flair_show_on_posts'])
		{
			return;
		}

		if (!isset($event['user_poster_data']['flair']))
		{
			return;
		}

		foreach ($event['user_poster_data']['flair'] as $category)
		{
			$this->template->assign_block_vars('postrow.flair', array(
				'CAT_NAME'	=> $category['category'],
			));

			foreach ($category['items'] as $item_id => $item)
			{
				$this->template->assign_block_vars('postrow.flair.item', array(
					'FLAIR_TYPE'		=> $item['type'],
					'FLAIR_ID'			=> $item_id,
					'FLAIR_NAME'		=> $item['name'],
					'FLAIR_DESC'		=> $item['desc'],
					'FLAIR_COLOR'		=> $item['color'],
					'FLAIR_ICON'		=> $item['icon'],
					'FLAIR_ICON_COLOR'	=> $item['icon_color'],
					'FLAIR_ICON_WIDTH'	=> $item['icon_width'],
					'FLAIR_IMG'			=> $this->img_path . $item['img'],
					'FLAIR_IMG_BIG'			=> $this->img_path . $item['img_big'],
					'FLAIR_FONT_COLOR'	=> $item['font_color'],
					'FLAIR_COUNT'		=> $item['count'],
				));
			}
		}
	}

	/**
	 * Remove references to a group after it is deleted.
	 *
	 * @param data $event The event data
	 */
	public function delete_group_after(data $event)
	{
		$this->flair_operator->delete_group($event['group_id']);
	}

	/**
	 * Remove references to users after they are deleted.
	 *
	 * @param data $event The event data
	 */
	public function delete_user_after(data $event)
	{
		$this->user_operator->delete_users($event['user_ids']);
	}
}

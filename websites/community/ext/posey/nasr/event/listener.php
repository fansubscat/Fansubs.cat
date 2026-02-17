<?php

/**
 *
 * posey: Normal and Special Ranks (nasr)
 * phpBB3.2 Extension Package
 * @copyright (c) 2015 posey [ www.godfathertalks.com ]
 * @copyright (c) 2016 kasimi [ https://kasimi.net ]
 * @license GNU General Public License v2 [ http://opensource.org/licenses/gpl-2.0.php ]
 *
 */

namespace posey\nasr\event;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\event\data;
use phpbb\path_helper;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Listener
 */
class listener implements EventSubscriberInterface
{
	/** @var template */
	protected $template;

	/** @var config */
	protected $config;

	/** @var driver_interface */
	protected $db;

	/** @var path_helper */
	protected $path_helper;

	/** @var array */
	private $users_extra_rank_template_data;

	/**
	 * Constructor
	 *
	 * @param template			$template
	 * @param config			$config
	 * @param driver_interface	$db
	 * @param path_helper		$path_helper
	 * @access public
	 */
	public function __construct(
		template $template,
		config $config,
		driver_interface $db,
		path_helper $path_helper
	)
	{
		$this->template		= $template;
		$this->config		= $config;
		$this->db			= $db;
		$this->path_helper	= $path_helper;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.memberlist_view_profile'		=> 'viewprofile',
			'core.viewtopic_modify_post_data'	=> 'viewtopic_fetch',
			'core.viewtopic_modify_post_row'	=> 'viewtopic_assign',
			'core.ucp_pm_view_messsage'			=> 'viewpm',
		];
	}

	/**
	 * @param data $event
	 */
	public function viewprofile($event)
	{
		$user_id = $event['member']['user_id'];
		$user_posts = $event['member']['user_posts'];
		$extra_rank_template_data = $this->get_extra_rank_template_data($user_id, $user_posts);
		$this->template->assign_vars($extra_rank_template_data);
	}

	/**
	 * @param data $event
	 */
	public function viewtopic_fetch($event)
	{
		$user_posts = [];
		$user_cache = $event['user_cache'];

		foreach ($event['rowset'] as $post_row)
		{
			$user_id = $post_row['user_id'];
			$user_posts[$user_id] = $user_cache[$user_id]['posts'];
		}

		$this->users_extra_rank_template_data = $this->get_extra_ranks_template_data($user_posts);
	}

	/**
	 * @param data $event
	 */
	public function viewtopic_assign($event)
	{
		$poster_id = $event['poster_id'];
		$extra_rank_template_data = $this->users_extra_rank_template_data[$poster_id];
		$event['post_row'] = array_merge($event['post_row'], $extra_rank_template_data);
	}

	/**
	 * @param data $event
	 */
	public function viewpm($event)
	{
		$user_id = $event['user_info']['user_id'];
		$user_posts = $event['user_info']['user_posts'];
		$extra_rank_template_data = $this->get_extra_rank_template_data($user_id, $user_posts);
		$this->template->assign_vars($extra_rank_template_data);
	}

	/**
	 * Helper method to return the rank template data for a single user
	 *
	 * @param int $user_id The ID of the user to fetch the rank template data
	 * @param int $user_posts The user's number of posts
	 * @return array
	 */
	protected function get_extra_rank_template_data($user_id, $user_posts)
	{
		$template_data = $this->get_extra_ranks_template_data([
			$user_id => $user_posts,
		]);

		return $template_data[$user_id];
	}

	/**
	 * Generates the rank template data for mutiple users
	 *
	 * @param array $user_posts, mapping from user_id to user_posts
	 * @return array mapping from user_id to the array of rank template data
	 */
	protected function get_extra_ranks_template_data($user_posts)
	{
		if (!function_exists('phpbb_get_user_rank'))
		{
			include($this->path_helper->get_phpbb_root_path() . 'includes/functions_display.' . $this->path_helper->get_php_ext());
		}

		$template_data = [];

		$user_special_ranks = $this->get_users_special_ranks(array_keys($user_posts));

		foreach ($user_special_ranks as $user_id => $has_special_rank)
		{
			$user_rank_data = phpbb_get_user_rank([], $has_special_rank && $user_posts[$user_id] ? $user_posts[$user_id] : false);

			$template_data[$user_id] = [
				'EXTRA_RANK_TITLE'	 => $user_rank_data['title'],
				'EXTRA_RANK_IMG'	 => $user_rank_data['img'],
				'EXTRA_RANK_IMG_SRC' => $user_rank_data['img_src'],
			];
		}

		return $template_data;
	}

	/**
	 * Grabs the rank_special flag for all passed user IDs
	 *
	 * @param array $user_ids
	 * @return array mapping from user_id to rank_special
	 */
	protected function get_users_special_ranks($user_ids)
	{
		$sql_array = [
			'SELECT'	=> 'u.user_id, r.rank_special',
			'FROM'		=> [USERS_TABLE => 'u'],
			'LEFT_JOIN' => [
				[
					'FROM'  => [RANKS_TABLE => 'r'],
					'ON'    => 'r.rank_id = u.user_rank',
				],
			],
			'WHERE'		=> $this->db->sql_in_set('u.user_id', $user_ids),
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$user_special_ranks = [];

		foreach ($rows as $row)
		{
			$user_special_ranks[(int) $row['user_id']] = (bool) $row['rank_special'];
		}

		return $user_special_ranks;
	}
}

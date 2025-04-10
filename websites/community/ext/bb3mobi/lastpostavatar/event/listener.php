<?php

/**
*
* @package Last Post Avatar
* @copyright bb3.mobi 2014 (c) Anvar [http://apwa.ru]
* @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
*
*/

namespace bb3mobi\lastpostavatar\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\path_helper */
	protected $path_helper;

	const MAX_SIZE = 30; // Max size img

	/**
	* Constructor of event listener
	* @param \phpbb\user							$user			User object
	* @param \phpbb\path_helper						$path_helper	phpBB path helper
	* @param \phpbb\db\driver\driver_interface		$db				Database object
	*/

	public function __construct(\phpbb\user $user, \phpbb\path_helper $path_helper)
	{
		$this->user = $user;
		$this->path_helper = $path_helper;
	}

	static public function getSubscribedEvents()
	{
		return array(
			/* Forumlist last avatar */
			'core.display_forums_modify_sql'			=> 'add_user_sql',
			'core.display_forums_modify_forum_rows'		=> 'forums_modify_forum_rows',
			'core.display_forums_modify_template_vars'	=> 'forums_last_post_avatar',
			/* Viewforum last avatar */
			'core.viewforum_get_topic_data'				=> 'add_user_viewforum_sql',
			'core.viewforum_modify_topicrow'			=> 'viewforum_last_post_avatar',
			/* Search last avatar */
			'core.search_get_topic_data'				=> 'add_user_search_sql',
			'core.search_modify_tpl_ary'				=> 'search_last_post_avatar',
			/* Connect Recent topics By PayBas */
			'paybas.recenttopics.sql_pull_topics_data'	=> 'add_user_rct_sql',
			'paybas.recenttopics.modify_tpl_ary'		=> 'rct_last_post_avatar',
		);
	}

	/** Data request forum */
	public function add_user_sql($event)
	{
		$sql_ary = $event['sql_ary'];
		$sql_ary['LEFT_JOIN'][] = array(
			'FROM' => array(USERS_TABLE => 'u'),
			'ON' => "u.user_id = f.forum_last_poster_id AND forum_type != " . FORUM_CAT
		);
		$sql_ary['SELECT'] .= ', u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height';
		$event['sql_ary'] = $sql_ary;
	}

	public function forums_modify_forum_rows($event)
	{
		$forum_rows = $event['forum_rows'];
		$parent_id = $event['parent_id'];
		$row = $event['row'];

		if (isset($forum_rows[$parent_id]['user_last_post_time']) && $forum_rows[$parent_id]['user_last_post_time'] > $row['forum_last_post_time'])
		{
			return;
		}
		$forum_rows[$parent_id]['user_last_post_time'] = $row['forum_last_post_time'];
		$forum_rows[$parent_id]['user_avatar'] = $row['user_avatar'];
		$forum_rows[$parent_id]['user_avatar_type'] = $row['user_avatar_type'];
		$forum_rows[$parent_id]['user_avatar_width'] = $row['user_avatar_width'];
		$forum_rows[$parent_id]['user_avatar_height'] = $row['user_avatar_height'];
		$event['forum_rows'] = $forum_rows;
	}

	/* User avatar Last post in forum */
	public function forums_last_post_avatar($event)
	{
		$row = $event['row'];
		$forum_row['AVATAR_IMG'] = $this->avatar_img_resize($row);
		$event['forum_row'] += $forum_row;
	}

	/** Data request viewforum */
	public function add_user_viewforum_sql($event)
	{
		$sql_array = $event['sql_array'];
		$sql_array['LEFT_JOIN'][] = array(
			'FROM' => array(USERS_TABLE => 'u'),
			'ON' => "u.user_id = t.topic_last_poster_id"
		);
		$sql_array['SELECT'] .= ', u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height';
		$event['sql_array'] = $sql_array;
	}

	/* User avatar Last post in viewforum */
	public function viewforum_last_post_avatar($event)
	{
		$row = $event['row'];
		$topic_row = $event['topic_row'];
		$topic_row['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar">' . $this->avatar_img_resize($row) . '</span>';
		$event['topic_row'] = $topic_row;
	}

	/** Data request reccent topics */
	public function add_user_search_sql($event)
	{
		$event['sql_from'] .= ' LEFT JOIN ' . USERS_TABLE . ' us ON (us.user_id = t.topic_last_poster_id)';
		$event['sql_select'] .= ', us.user_avatar, us.user_avatar_type, us.user_avatar_width, us.user_avatar_height';
	}

	/* User avatar Last post in recent topics */
	public function search_last_post_avatar($event)
	{
		$row = $event['row'];
		$tpl_ary = $event['tpl_ary'];
		if (isset($tpl_ary['LAST_POST_AUTHOR_FULL']))
		{
			$tpl_ary['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar">' . $this->avatar_img_resize($row) . '</span>';
			$event['tpl_ary'] = $tpl_ary;
		}
	}

	/** Data request reccent topics */
	public function add_user_rct_sql($event)
	{
		$sql_array = $event['sql_array'];
		$sql_array['LEFT_JOIN'][] = array(
			'FROM' => array(USERS_TABLE => 'u'),
			'ON' => "u.user_id = t.topic_last_poster_id"
		);
		$sql_array['SELECT'] .= ', u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height';
		$event['sql_array'] = $sql_array;
	}

	/* User avatar Last post in recent topics */
	public function rct_last_post_avatar($event)
	{
		$row = $event['row'];
		$tpl_ary = $event['tpl_ary'];
		$tpl_ary['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar">' . $this->avatar_img_resize($row) . '</span>';
		$event['tpl_ary'] = $tpl_ary;
	}

	/* Generate and resize avatar */
	private function avatar_img_resize($avatar)
	{
		if (!empty($avatar['user_avatar']))
		{
			if ($avatar['user_avatar_width'] >= $avatar['user_avatar_height'])
			{
				$avatar_width = ($avatar['user_avatar_width'] > self::MAX_SIZE) ? self::MAX_SIZE : $avatar['user_avatar_width'];
				if ($avatar_width == self::MAX_SIZE)
				{
					$avatar['user_avatar_height'] = round(self::MAX_SIZE/$avatar['user_avatar_width']*$avatar['user_avatar_height']);
				}
				$avatar['user_avatar_width'] = $avatar_width;
			}
			else
			{
				$avatar_height = ($avatar['user_avatar_height'] > self::MAX_SIZE) ? self::MAX_SIZE : $avatar['user_avatar_height'];
				if ($avatar_height == self::MAX_SIZE)
				{
					$avatar['user_avatar_width'] = round(self::MAX_SIZE/$avatar['user_avatar_height']*$avatar['user_avatar_width']);
				}
				$avatar['user_avatar_height'] = $avatar_height;
			}
			return phpbb_get_user_avatar($avatar);
		}

		// Determine board url - we may need it later
		$board_url = generate_board_url() . '/';
		$corrected_path = $this->path_helper->get_web_root_path();
		$web_path = (defined('PHPBB_USE_BOARD_URL_PATH') && PHPBB_USE_BOARD_URL_PATH) ? $board_url : $corrected_path;
		$theme = "{$web_path}styles/" . rawurlencode($this->user->style['style_path']) . '/theme';
		return '<img class="avatar" src="' . $theme . '/images/no_avatar.gif" width="' . self::MAX_SIZE . '" height="' . self::MAX_SIZE . '" alt="" />';
	}
}
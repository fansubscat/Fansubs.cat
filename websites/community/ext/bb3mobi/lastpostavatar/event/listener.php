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
		$sql_array['LEFT_JOIN'][] = array(
			'FROM' => array(USERS_TABLE => 'u2'),
			'ON' => "u2.user_id = t.topic_poster"
		);
		$sql_array['SELECT'] .= ', u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u2.user_avatar first_poster_user_avatar, u2.user_avatar_type first_poster_user_avatar_type, u2.user_avatar_width first_poster_user_avatar_width, u2.user_avatar_height first_poster_user_avatar_height';
		$event['sql_array'] = $sql_array;
	}

	/* User avatar Last post in viewforum */
	public function viewforum_last_post_avatar($event)
	{
		$row = $event['row'];
		$topic_row = $event['topic_row'];
		$topic_row['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar responsive-hide">' . $this->avatar_img_resize($row) . '</span>';
		
		$row['user_avatar'] = $row['first_poster_user_avatar'];
		$row['user_avatar_type'] = $row['first_poster_user_avatar_type'];
		$row['user_avatar_width'] = $row['first_poster_user_avatar_width'];
		$row['user_avatar_height'] = $row['first_poster_user_avatar_height'];
		
		$topic_row['POST_AUTHOR_AVATAR'] = '<span class="firstpostavatar responsive-hide">' . $this->avatar_img_resize($row) . '</span>';
		$event['topic_row'] = $topic_row;
	}

	/** Data request reccent topics */
	public function add_user_search_sql($event)
	{
		$event['sql_from'] .= ' LEFT JOIN ' . USERS_TABLE . ' us ON (us.user_id = t.topic_last_poster_id) LEFT JOIN ' . USERS_TABLE . ' u2 ON u2.user_id=t.topic_poster';
		$event['sql_select'] .= ', us.user_avatar, us.user_avatar_type, us.user_avatar_width, us.user_avatar_height, u2.user_avatar first_poster_user_avatar, u2.user_avatar_type first_poster_user_avatar_type, u2.user_avatar_width first_poster_user_avatar_width, u2.user_avatar_height first_poster_user_avatar_height';
	}

	/* User avatar Last post in recent topics */
	public function search_last_post_avatar($event)
	{
		$row = $event['row'];
		$tpl_ary = $event['tpl_ary'];
		if (isset($tpl_ary['LAST_POST_AUTHOR_FULL']))
		{
			$tpl_ary['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar responsive-hide">' . $this->avatar_img_resize($row) . '</span>';
		}
		
		if (isset($row['first_poster_user_avatar'])) {
			$row['user_avatar'] = $row['first_poster_user_avatar'];
			$row['user_avatar_type'] = $row['first_poster_user_avatar_type'];
			$row['user_avatar_width'] = $row['first_poster_user_avatar_width'];
			$row['user_avatar_height'] = $row['first_poster_user_avatar_height'];
			
			$tpl_ary['POST_AUTHOR_AVATAR'] = '<span class="firstpostavatar responsive-hide">' . $this->avatar_img_resize($row) . '</span>';
		}
		
		$event['tpl_ary'] = $tpl_ary;
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
		$tpl_ary['LAST_POST_AUTHOR_FULL'] .= '<span class="lastpostavatar responsive-hide">' . $this->avatar_img_resize($row) . '</span>';
		$event['tpl_ary'] = $tpl_ary;
	}

	/* Generate and resize avatar */
	private function avatar_img_resize($avatar)
	{
		return phpbb_get_user_avatar($avatar);
	}
}

<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\core;

use phpbb\auth\auth;
use phpbb\cache\driver\driver_interface as cache_interface;
use phpbb\db\driver\driver_interface as db_interface;
use phpbb\event\dispatcher_interface;
use phpbb\group\helper;
use phpbb\language\language;
use phpbb\log\log_interface;
use phpbb\user;

class functions
{
	/** @var settings */
	protected $mchat_settings;

	/** @var notifications */
	protected $mchat_notifications;

	/** @var log */
	protected $mchat_log;

	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var auth */
	protected $auth;

	/** @var log_interface */
	protected $log;

	/** @var db_interface */
	protected $db;

	/** @var cache_interface */
	protected $cache;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var helper */
	protected $group_helper;

	/** @var array */
	protected $active_users;

	/**
	 * Constructor
	 *
	 * @param settings				$mchat_settings
	 * @param notifications			$mchat_notifications
	 * @param log					$mchat_log
	 * @param user					$user
	 * @param language				$lang
	 * @param auth					$auth
	 * @param log_interface			$log
	 * @param db_interface			$db
	 * @param cache_interface		$cache
	 * @param dispatcher_interface	$dispatcher
	 * @param helper				$group_helper

	 */
	function __construct(
		settings $mchat_settings,
		notifications $mchat_notifications,
		log $mchat_log,
		user $user,
		language $lang,
		auth $auth,
		log_interface $log,
		db_interface $db,
		cache_interface $cache,
		dispatcher_interface $dispatcher,
		helper $group_helper
	)
	{
		$this->mchat_settings		= $mchat_settings;
		$this->mchat_notifications	= $mchat_notifications;
		$this->mchat_log			= $mchat_log;
		$this->user					= $user;
		$this->lang					= $lang;
		$this->auth					= $auth;
		$this->log					= $log;
		$this->db					= $db;
		$this->cache				= $cache;
		$this->dispatcher			= $dispatcher;
		$this->group_helper			= $group_helper;
	}

	/**
	 * Converts a number of seconds to a string in the format 'x hours y minutes z seconds'
	 *
	 * @param int $time
	 * @return string
	 */
	protected function mchat_format_seconds($time)
	{
		$times = [];

		$hours = floor($time / 3600);
		if ($hours)
		{
			$time -= $hours * 3600;
			$times[] = $this->lang->lang('MCHAT_HOURS', $hours);
		}

		$minutes = floor($time / 60);
		if ($minutes)
		{
			$time -= $minutes * 60;
			$times[] = $this->lang->lang('MCHAT_MINUTES', $minutes);
		}

		$seconds = ceil($time);
		if ($seconds)
		{
			$times[] = $this->lang->lang('MCHAT_SECONDS', $seconds);
		}

		return $this->lang->lang('MCHAT_ONLINE_EXPLAIN', implode('&nbsp;', $times));
	}

	/**
	 * Returns the total session time in seconds
	 *
	 * @return int
	 */
	protected function mchat_session_time()
	{
		$mchat_timeout = $this->mchat_settings->cfg('mchat_timeout');
		if ($mchat_timeout)
		{
			return $mchat_timeout;
		}

		$load_online_time = $this->mchat_settings->cfg('load_online_time');
		if ($load_online_time)
		{
			return $load_online_time * 60;
		}

		return $this->mchat_settings->cfg('session_length');
	}

	/**
	 * Returns data about users who are currently chatting
	 *
	 * @param bool $cached Whether to return possibly cached data
	 * @return array
	 */
	public function mchat_active_users($cached = true)
	{
		if ($cached && $this->active_users)
		{
			return $this->active_users;
		}

		$check_time = time() - $this->mchat_session_time();

		$sql_array = [
			'SELECT'	=> 'u.user_id, u.username, u.user_colour, s.session_viewonline',
			'FROM'		=> [$this->mchat_settings->get_table_mchat_sessions() => 'ms'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [SESSIONS_TABLE => 's'],
					'ON'	=> 'ms.user_id = s.session_user_id',
				],
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 'ms.user_id = u.user_id',
				],
			],
			'WHERE'		=> 'u.user_id <> ' . ANONYMOUS . ' AND s.session_viewonline IS NOT NULL AND ms.user_lastupdate > ' . (int) $check_time,
			'ORDER_BY'	=> 'u.username ASC',
		];

		/**
		 * Event to modify the SQL query that fetches active mChat users
		 *
		 * @event dmzx.mchat.active_users_sql_before
		 * @var array	sql_array	Array with SQL query data to fetch the current active sessions
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.active_users_sql_before', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$mchat_users = [];
		$can_view_hidden = $this->auth->acl_get('u_viewonline');

		foreach ($rows as $row)
		{
			if (!$row['session_viewonline'])
			{
				if (!$can_view_hidden && $row['user_id'] != $this->user->data['user_id'])
				{
					continue;
				}

				$row['username'] = '<em>' . $row['username'] . '</em>';
			}

			$mchat_users[$row['user_id']] = get_username_string('full', $row['user_id'], $row['username'], $row['user_colour'], $this->lang->lang('GUEST'));
		}

		$active_users = [
			'online_userlist'	=> implode($this->lang->lang('COMMA_SEPARATOR'), $mchat_users),
			'users_count_title'	=> $this->lang->lang('MCHAT_TITLE_COUNT', count($mchat_users)),
			'users_total'		=> $this->lang->lang('MCHAT_ONLINE_USERS_TOTAL', count($mchat_users)),
			'refresh_message'	=> $this->mchat_format_seconds($this->mchat_session_time()),
		];

		/**
		 * Event to modify collected data about active mChat users
		 *
		 * @event dmzx.mchat.active_users_after
		 * @var array	mchat_users		Array containing all currently active mChat sessions, mapping from user ID to full username
		 * @var array	active_users	Array containing info about currently active mChat users
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'mchat_users',
			'active_users',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.active_users_after', compact($vars)));

		$this->active_users = $active_users;

		return $active_users;
	}

	/**
	 * Inserts the current user into the mchat_sessions table
	 *
	 * @return bool Returns true if a new session was created, otherwise false
	 */
	public function mchat_add_user_session()
	{
		if (!$this->user->data['is_registered'] || $this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot'])
		{
			return false;
		}

		$sql = 'UPDATE ' . $this->mchat_settings->get_table_mchat_sessions() . '
			SET user_lastupdate = ' . time() . '
			WHERE user_id = ' . (int) $this->user->data['user_id'];
		$this->db->sql_query($sql);

		$is_new_session = $this->db->sql_affectedrows() < 1;

		if ($is_new_session)
		{
			$sql = 'INSERT INTO ' . $this->mchat_settings->get_table_mchat_sessions() . ' ' . $this->db->sql_build_array('INSERT', [
				'user_id'			=> (int) $this->user->data['user_id'],
				'user_ip'			=> $this->user->ip,
				'user_lastupdate'	=> time(),
			]);
			$this->db->sql_query($sql);
		}

		return $is_new_session;
	}

	/**
	 * Remove expired sessions from the database
	 */
	public function mchat_session_gc()
	{
		$check_time = time() - $this->mchat_session_time();

		$sql = 'DELETE FROM ' . $this->mchat_settings->get_table_mchat_sessions() . '
			WHERE user_lastupdate <= ' . (int) $check_time;
		$this->db->sql_query($sql);
	}

	/**
	 * Prune messages
	 *
	 * @param int|array $user_ids
	 * @return array
	 */
	public function mchat_prune($user_ids = [])
	{
		$prune_num = (int) $this->mchat_settings->cfg('mchat_prune_num');
		$prune_mode = (int) $this->mchat_settings->cfg('mchat_prune_mode');

		if (empty($this->mchat_settings->prune_modes[$prune_mode]))
		{
			return [];
		}

		$sql_array = [
			'SELECT'	=> 'message_id',
			'FROM'		=> [$this->mchat_settings->get_table_mchat() => 'm'],
		];

		if ($user_ids)
		{
			if (!is_array($user_ids))
			{
				$user_ids = [$user_ids];
			}

			$sql_array['WHERE'] = $this->db->sql_in_set('m.user_id', $user_ids);
			$offset = 0;
		}
		else if ($this->mchat_settings->prune_modes[$prune_mode] == 'messages')
		{
			// Skip fixed number of messages, delete all others
			$sql_array['ORDER_BY'] = 'm.message_id DESC';
			$offset = $prune_num;
		}
		else
		{
			// Delete messages older than time period
			$sql_array['WHERE'] = 'm.message_time < ' . (int) strtotime($prune_num * $prune_mode . ' hours ago');
			$offset = 0;
		}

		/**
		 * Allow modifying SQL query before message ids to be pruned are retrieved.
		 *
		 * @event dmzx.mchat.prune_sql_before
		 * @var array	user_ids	Array of user IDs that are being pruned, empty when pruning via cron
		 * @var array	sql_array	SQL query data
		 * @since 2.0.2
		 */
		$vars = [
			'user_ids',
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.prune_sql_before', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 0, $offset);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$prune_ids = [];

		foreach ($rows as $row)
		{
			$prune_ids[] = (int) $row['message_id'];
		}

		/**
		 * Event to modify messages that are about to be pruned
		 *
		 * @event dmzx.mchat.prune_before
		 * @var array	prune_ids	Array of message IDs that are about to be pruned
		 * @var array	user_ids	Array of user IDs that are being pruned, empty when pruning via cron
		 * @since 2.0.0-RC6
		 * @changed 2.0.1 Added user_ids
		 */
		$vars = [
			'prune_ids',
			'user_ids',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.prune_before', compact($vars)));

		if ($prune_ids)
		{
			$this->db->sql_query('DELETE FROM ' . $this->mchat_settings->get_table_mchat() . ' WHERE ' . $this->db->sql_in_set('message_id', $prune_ids));
			$this->db->sql_query('DELETE FROM ' . $this->mchat_settings->get_table_mchat_log() . ' WHERE ' . $this->db->sql_in_set('message_id', $prune_ids));
			$this->cache->destroy('sql', $this->mchat_settings->get_table_mchat_log());

			// Only add a log entry if message pruning was not triggered by user pruning
			if (!$user_ids)
			{
				$this->phpbb_log('LOG_MCHAT_TABLE_PRUNED', [count($prune_ids)]);
			}
		}

		return $prune_ids;
	}

	/**
	 * Returns the total number of messages
	 *
	 * @param string $sql_where
	 * @param string $sql_order_by
	 * @return int
	 */
	public function mchat_total_message_count($sql_where = '', $sql_order_by = '')
	{
		$sql_where_array = array_filter([$sql_where, $this->mchat_notifications->get_sql_where()]);

		$sql_array = [
			'SELECT'	=> 'COUNT(*) AS rows_total',
			'FROM'		=> [$this->mchat_settings->get_table_mchat() => 'm'],
			'WHERE'		=> $sql_where_array ? ('(' . implode(') AND (', $sql_where_array) . ')') : '',
			'ORDER_BY'	=> $sql_order_by,
		];

		/**
		 * Event to modifying the SQL query that fetches the total number of mChat messages
		 *
		 * @event dmzx.mchat.total_message_count_modify_sql
		 * @var array	sql_array		Array with SQL query data to fetch the total message count
		 * @var string	sql_where		Additional SQL where condition passed to this method
		 * @var string	sql_order_by	Additional SQL order by statement passed to this method
		 * @since 2.0.0-RC6
		 * @changed 2.1.1 Added sql_where, sql_order_by
		 */
		$vars = [
			'sql_array',
			'sql_where',
			'sql_order_by',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.total_message_count_modify_sql', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows_total = $this->db->sql_fetchfield('rows_total');
		$this->db->sql_freeresult($result);

		return (int) $rows_total;
	}

	/**
	 * Fetch messages from the database
	 *
	 * @param int|array $message_ids IDs of specific messages to fetch, e.g. for fetching edited messages
	 * @param int $last_id The ID of the latest message that the user has, for fetching new messages
	 * @param int $total
	 * @param int $offset
	 * @return array
	 */
	public function mchat_get_messages($message_ids, $last_id = 0, $total = 0, $offset = 0)
	{
		$sql_where_message_id = [];

		// Fetch new messages
		if ($last_id)
		{
			$sql_where_message_id[] = 'm.message_id > ' . (int) $last_id;
		}

		// Fetch edited messages
		if ($message_ids)
		{
			if (!is_array($message_ids))
			{
				$message_ids = [$message_ids];
			}

			$sql_where_message_id[] = $this->db->sql_in_set('m.message_id', $message_ids);
		}

		$sql_where_ary = array_filter([
			implode(' OR ', $sql_where_message_id),
			$this->mchat_notifications->get_sql_where(),
		]);

		$sql_array = [
			'SELECT'	=> 'm.*, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height, u.user_allow_pm, p.post_visibility',
			'FROM'		=> [$this->mchat_settings->get_table_mchat() => 'm'],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [USERS_TABLE => 'u'],
					'ON'	=> 'm.user_id = u.user_id',
				],
				[
					'FROM'	=> [POSTS_TABLE => 'p'],
					'ON'	=> 'm.post_id = p.post_id AND m.forum_id <> 0',
				],
			],
			'WHERE'		=> $sql_where_ary ? $this->db->sql_escape('(' . implode(') AND (', $sql_where_ary) . ')') : '',
			'ORDER_BY'	=> 'm.message_id DESC',
		];

		/**
		 * Event to modify the SQL query that fetches mChat messages
		 *
		 * @event dmzx.mchat.get_messages_modify_sql
		 * @var array	message_ids	IDs of specific messages to fetch, e.g. for fetching edited messages
		 * @var int		last_id		The ID of the latest message that the user has, for fetching new messages
		 * @var int		total		SQL limit
		 * @var int		offset		SQL offset
		 * @var	array	sql_array	Array containing the SQL query data
		 * @since 2.0.0-RC6
		 * @deprecated 2.1.4-RC1, to be removed in 2.1.0.
		 */
		$vars = [
			'message_ids',
			'last_id',
			'total',
			'offset',
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.get_messages_modify_sql', compact($vars)));

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, $total, $offset);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		// Set deleted users to ANONYMOUS
		foreach ($rows as $i => $row)
		{
			if (!isset($row['username']))
			{
				$rows[$i]['user_id'] = ANONYMOUS;
			}
		}

		/**
		 * Event to modify message rows before being processed and displayed
		 *
		 * @event dmzx.mchat.get_messages_modify_rowset
		 * @var array	message_ids	IDs of specific messages to fetch, e.g. for fetching edited messages
		 * @var int		last_id		The ID of the latest message that the user has, for fetching new messages
		 * @var int		total		SQL limit
		 * @var int		offset		SQL offset
		 * @var	array	rows		Array containing message data
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'message_ids',
			'last_id',
			'total',
			'offset',
			'rows',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.get_messages_modify_rowset', compact($vars)));

		return $rows;
	}

	/**
	 * Generates the user legend markup
	 *
	 * @return array Array of HTML markup for each group
	 */
	public function mchat_legend()
	{
		// Grab group details for legend display for who is online on the custom page
		$order_legend = $this->mchat_settings->cfg('legend_sort_groupname') ? 'group_name' : 'group_legend';

		$sql_array = [
			'SELECT'	=> 'g.group_id, g.group_name, g.group_colour',
			'FROM'		=> [GROUPS_TABLE => 'g'],
			'WHERE'		=> 'g.group_legend <> 0',
			'ORDER_BY'	=> 'g.' . $order_legend . ' ASC',
		];

		if ($this->auth->acl_gets('a_group', 'a_groupadd', 'a_groupdel'))
		{
			$sql_array['LEFT_JOIN'] = [
				[
					'FROM'	=> [USER_GROUP_TABLE => 'ug'],
					'ON'	=> 'g.group_id = ug.group_id AND ug.user_id = ' . (int) $this->user->data['user_id'] . ' AND ug.user_pending = 0',
				],
			];

			$sql_array['WHERE'] .= ' AND (g.group_type <> ' . GROUP_HIDDEN . ' OR ug.user_id = ' . (int) $this->user->data['user_id'] . ')';
		}

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$legend = [];
		foreach ($rows as $row)
		{
			$colour_text = $row['group_colour'] ? ' style="color:#' . $row['group_colour'] . '"' : '';
			$group_name = $this->group_helper->get_name($row['group_name']);
			if ($row['group_name'] == 'BOTS' || $this->user->data['user_id'] != ANONYMOUS && !$this->auth->acl_get('u_viewprofile'))
			{
				$legend[] = '<span' . $colour_text . '>' . $group_name . '</span>';
			}
			else
			{
				$legend[] = '<a' . $colour_text . ' href="' . append_sid($this->mchat_settings->url('memberlist'), ['mode' => 'group', 'g' => $row['group_id']]) . '">' . $group_name . '</a>';
			}
		}

		return $legend;
	}

	/**
	 * Returns a list of all foes of the current user
	 *
	 * @return array Array of user IDs
	 */
	public function mchat_foes()
	{
		$sql_array = [
			'SELECT'	=> 'z.zebra_id',
			'FROM'		=> [ZEBRA_TABLE => 'z'],
			'WHERE'		=> 'z.foe = 1 AND z.user_id = ' . (int) $this->user->data['user_id'],
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$foes = [];

		foreach ($rows as $row)
		{
			$foes[] = $row['zebra_id'];
		}

		return $foes;
	}

	/**
	 * Adds forbidden BBCodes to the passed SQL where statement
	 *
	 * @param string $sql_where
	 * @return string
	 */
	public function mchat_sql_append_forbidden_bbcodes($sql_where)
	{
		$disallowed_bbcodes = explode('|', $this->mchat_settings->cfg('mchat_bbcode_disallowed'));

		if (!empty($disallowed_bbcodes))
		{
			$sql_where .= ' AND ' . $this->db->sql_in_set('b.bbcode_tag', $disallowed_bbcodes, true);
		}

		return $sql_where;
	}

	/**
	 * Checks if the current user is flooding the chat
	 *
	 * @return bool
	 */
	public function mchat_is_user_flooding()
	{
		if ($this->auth->acl_get('u_mchat_flood_ignore'))
		{
			return false;
		}

		$sql_queries = [];

		$sql_array = [
			'SELECT'	=> 'm.user_id',
			'FROM'		=> [$this->mchat_settings->get_table_mchat() => 'm'],
			'ORDER_BY'	=> 'm.message_time DESC, m.message_id DESC',
		];

		if ($this->mchat_settings->cfg('mchat_flood_time'))
		{
			$sql = $this->db->sql_build_query('SELECT', array_merge($sql_array, [
				'WHERE'	=> implode(' AND ', [
					'm.user_id = ' . (int) $this->user->data['user_id'],
					'message_time > ' . time() . ' - ' . (int) $this->mchat_settings->cfg('mchat_flood_time'),
					$this->mchat_notifications->get_sql_where('exclude'),
				]),
			]));

			$sql_queries[$sql] = 1;
		}

		if ($this->mchat_settings->cfg('mchat_flood_messages'))
		{
			$sql = $this->db->sql_build_query('SELECT', array_merge($sql_array, [
				'WHERE'	=> $this->mchat_notifications->get_sql_where('exclude'),
			]));

			$sql_queries[$sql] = $this->mchat_settings->cfg('mchat_flood_messages');
		}

		foreach ($sql_queries as $sql => $limit)
		{
			$result = $this->db->sql_query_limit($sql, $limit);
			$rows = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			if ($rows)
			{
				foreach ($rows as $row)
				{
					if ($row['user_id'] != $this->user->data['user_id'])
					{
						return false;
					}
				}

				return true;
			}
		}

		return false;
	}

	/**
	 * Returns user ID & name of the specified message
	 *
	 * @param int $message_id
	 * @return array
	 */
	public function mchat_author_for_message($message_id)
	{
		$sql_array = [
			'SELECT'	=> 'm.user_id, m.message_time, m.post_id',
			'FROM'		=> [$this->mchat_settings->get_table_mchat() => 'm'],
			'WHERE'		=> 'm.message_id = ' . (int) $message_id,
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row;
	}

	/**
	 * Adds an entry to phpBB's admin log
	 *
	 * @param string $log_lang_key
	 * @param array $additional_data
	 */
	public function phpbb_log($log_lang_key, $additional_data = [])
	{
		$mode = 'admin';
		$log_enabled = $this->mchat_settings->cfg('mchat_log_enabled');
		$additional_data = array_merge([$this->user->data['username']], $additional_data);

		/**
		 * Event to modify the phpBB log data before it is added to the log table
		 *
		 * @event dmzx.mchat.phpbb_log_add_before
		 * @var string	mode					The log mode, one of admin|mod|user|critical
		 * @var	string	log_lang_key			The language key of the log entry
		 * @var bool	log_enabled				Flag indicating whether this log entry should be added or not
		 * @var array	additional_data			Array with additional data for the log message
		 * @since 2.1.0-RC1
		 */
		$vars = [
			'mode',
			'log_lang_key',
			'log_enabled',
			'additional_data',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.phpbb_log_add_before', compact($vars)));

		if ($log_enabled)
		{
			$this->log->add($mode, $this->user->data['user_id'], $this->user->ip, $log_lang_key, false, $additional_data);
		}
	}

	/**
	 * Performs AJAX actions
	 *
	 * @param string $action One of add|edit|del
	 * @param array $sql_ary
	 * @param int $message_id
	 * @return bool
	 */
	public function mchat_action($action, $sql_ary = null, $message_id = 0)
	{
		$update_session_infos = true;

		/**
		 * Event to modify the SQL query that adds, edits or deletes an mChat message
		 *
		 * @event dmzx.mchat.action_before
		 * @var	string	action					The action that is being performed, one of add|edit|del
		 * @var bool	sql_ary					Array containing SQL data, or null if a message is deleted
		 * @var int		message_id				The ID of the message that is being edited or deleted, or 0 if a message is added
		 * @var bool	update_session_infos	Whether or not to update the user session
		 * @since 2.0.0-RC6
		 */
		$vars = [
			'action',
			'sql_ary',
			'message_id',
			'update_session_infos',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.action_before', compact($vars)));

		$is_new_session = false;

		switch ($action)
		{
			// User adds a message
			case 'add':
				if ($update_session_infos)
				{
					$this->user->update_session_infos();
				}
				$is_new_session = $this->mchat_add_user_session();
				$this->db->sql_query('INSERT INTO ' . $this->mchat_settings->get_table_mchat() . ' ' . $this->db->sql_build_array('INSERT', $sql_ary));
				break;

			// User edits a message
			case 'edit':
				if ($update_session_infos)
				{
					$this->user->update_session_infos();
				}
				$is_new_session = $this->mchat_add_user_session();
				$this->db->sql_query('UPDATE ' . $this->mchat_settings->get_table_mchat() . ' SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . ' WHERE message_id = ' . (int) $message_id);
				$this->mchat_log->add_log('edit', $message_id);
				$this->phpbb_log('LOG_EDITED_MCHAT');
				break;

			// User deletes a message
			case 'del':
				if ($update_session_infos)
				{
					$this->user->update_session_infos();
				}
				$is_new_session = $this->mchat_add_user_session();
				$this->db->sql_query('DELETE FROM ' . $this->mchat_settings->get_table_mchat() . ' WHERE message_id = ' . (int) $message_id);
				$this->mchat_log->add_log('del', $message_id);
				$this->phpbb_log('LOG_DELETED_MCHAT');
				break;
		}

		return $is_new_session;
	}
}

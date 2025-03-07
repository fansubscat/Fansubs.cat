<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2018 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat\core;

use phpbb\auth\auth;
use phpbb\event\dispatcher_interface;
use phpbb\language\language;
use phpbb\textformatter\parser_interface;
use phpbb\user;
use phpbb\db\driver\driver_interface as db_interface;

class notifications
{
	/**
	 * Value of the phpbb_mchat.post_id field for login notification
	 * messages if the user session is visible at the time of login
	 */
	const LOGIN_VISIBLE = 1;

	/**
	 * Value of the phpbb_mchat.post_id field for login notification
	 * messages if the user session is hidden at the time of login
	 */
	const LOGIN_HIDDEN = 2;

	/**
	 * A notification of a new topic, quote, edit or reply
	 */
	const POST = 3;

	/** @var settings */
	protected $mchat_settings;

	/** @var user */
	protected $user;

	/** @var language */
	protected $lang;

	/** @var auth */
	protected $auth;

	/** @var db_interface */
	protected $db;

	/** @var dispatcher_interface */
	protected $dispatcher;

	/** @var parser_interface */
	protected $textformatter_parser;

	/**
	 * Constructor
	 *
	 * @param settings				$mchat_settings
	 * @param user					$user
	 * @param language				$lang
	 * @param auth					$auth
	 * @param db_interface			$db
	 * @param dispatcher_interface	$dispatcher
	 * @param parser_interface		$textformatter_parser
	 */
	public function __construct(
		settings $mchat_settings,
		user $user,
		language $lang,
		auth $auth,
		db_interface $db,
		dispatcher_interface $dispatcher,
		parser_interface $textformatter_parser
	)
	{
		$this->mchat_settings		= $mchat_settings;
		$this->user					= $user;
		$this->lang					= $lang;
		$this->auth					= $auth;
		$this->db					= $db;
		$this->dispatcher			= $dispatcher;
		$this->textformatter_parser	= $textformatter_parser;
	}

	/**
	 * Checks whether or not the given message row is a notification
	 *
	 * @param array $row The message row
	 * @return int the notification type, or 0 if the $row is not a notification
	 */
	public function is_notification($row)
	{
		// If post_id is 0 it's not a notification
		if (isset($row['post_id']) && $row['post_id'])
		{
			// If forum_id is 0 it's a login notification
			if (isset($row['forum_id']) && !$row['forum_id'])
			{
				// post_id is either LOGIN_VISIBLE or LOGIN_HIDDEN
				return $row['post_id'];
			}

			return self::POST;
		}

		return 0;
	}

	/**
	 * Checks the post rows for notifications and converts their language keys
	 *
	 * @param array $rows The rows to modify
	 * @return array
	 */
	public function process($rows)
	{
		// All language keys of valid notifications. We need to check for them here because
		// notifications in < 2.0.0-RC6 are plain text and don't need to be processed.
		$notification_lang = [
			'MCHAT_NEW_POST',
			'MCHAT_NEW_QUOTE',
			'MCHAT_NEW_EDIT',
			'MCHAT_NEW_REPLY',
			'MCHAT_NEW_LOGIN',
		];

		/**
		 * Event that allows to modify rows and language keys before checking for notifications
		 *
		 * @event dmzx.mchat.process_notifications_before
		 * @var array	rows				Message rows about to be checked for notifications
		 * @var array	notification_lang	Unprocessed language keys of valid/known notifications
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'rows',
			'notification_lang',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.process_notifications_before', compact($vars)));

		$notification_langs = array_merge(
			// Raw notification messages in phpBB < 3.2
			array_combine($notification_lang, $notification_lang),
			// XML notification messages in phpBB >= 3.2
			array_combine(array_map([$this->textformatter_parser, 'parse'], $notification_lang), $notification_lang)
		);

		$notifications = [];
		$post_ids = [];

		foreach ($rows as $i => $row)
		{
			$type = $this->is_notification($row);

			if ($type && isset($notification_langs[$row['message']]))
			{
				$notifications[$i] = $type;

				if ($type == self::POST)
				{
					$post_ids[$i] = $row['post_id'];
				}
			}
		}

		$notification_post_data = $this->mchat_get_post_data($post_ids);

		foreach ($notifications as $i => $type)
		{
			$lang_key = $notification_langs[$rows[$i]['message']];
			$post_data = $type == self::POST ? $notification_post_data[$post_ids[$i]] : null;
			$rows[$i] = $this->process_notification($rows[$i], $type, $lang_key, $post_data);
		}

		/**
		 * Event that allows to modify rows after processing their notifications
		 *
		 * @event dmzx.mchat.process_notifications_after
		 * @var array	rows					Message rows about to be checked for notifications
		 * @var array	notification_lang		Unprocessed language keys of valid/known notifications
		 * @var array	notification_langs		Processed language keys of valid/known notifications
		 * @var array	notification_post_data	Post data of notifications found in the rows array
		 * @since 2.1.4-RC1
		 */
		$vars = [
			'rows',
			'notification_lang',
			'notification_langs',
			'notification_post_data',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.process_notifications_after', compact($vars)));

		return $rows;
	}

	/**
	 * Fetches post subjects and their forum names. If a post_id can't be found the value for the post_id is set to null.
	 *
	 * @param array $post_ids
	 * @return array
	 */
	protected function mchat_get_post_data($post_ids)
	{
		if (!$post_ids)
		{
			return [];
		}

		$sql_array = [
			'SELECT'	=> 'p.post_id, p.post_subject, f.forum_id, f.forum_name',
			'FROM'		=> [POSTS_TABLE => 'p', FORUMS_TABLE => 'f'],
			'WHERE'		=> 'p.forum_id = f.forum_id AND ' . $this->db->sql_in_set('p.post_id', $post_ids),
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);
		$rows = $this->db->sql_fetchrowset($result);
		$this->db->sql_freeresult($result);

		$existing_post_ids = array_column($rows, 'post_id');
		$existing_posts = array_combine($existing_post_ids, $rows);

		// Map IDs of missing posts to null
		$missing_posts = array_fill_keys(array_diff($post_ids, $existing_post_ids), null);

		return $existing_posts + $missing_posts;
	}

	/**
	 * Converts the message field of the post row so that it can be passed to generate_text_for_display()
	 *
	 * @param array $row
	 * @param int $type
	 * @param string $lang_key
	 * @param array $post_data
	 * @return array
	 */
	protected function process_notification($row, $type, $lang_key, $post_data = null)
	{
		$lang_args = [];
		$replacements = [];

		$post_subject_placeholder = '%POST_SUBJECT%';
		$forum_name_placeholder = '%FORUM_NAME%';

		if ($type == self::POST)
		{
			if ($post_data)
			{
				$viewtopic_url = append_sid($this->mchat_settings->url('viewtopic', true), [
					'p' => $row['post_id'],
					'#' => 'p' . $row['post_id'],
				]);

				// We prefer $post_data because it was fetched from the forums table just now.
				// $row might contain outdated data if a post was moved to a new forum.
				$forum_id = isset($post_data['forum_id']) ? $post_data['forum_id'] : $row['forum_id'];

				$viewforum_url = append_sid($this->mchat_settings->url('viewforum', true), [
					'f' => $forum_id,
				]);

				$lang_args[] = '[url=' . $viewtopic_url . ']' . $post_subject_placeholder . '[/url]';
				$lang_args[] = '[url=' . $viewforum_url . ']' . $forum_name_placeholder . '[/url]';

				$replacements = [
					$post_subject_placeholder	=> $post_data['post_subject'],
					$forum_name_placeholder		=> $post_data['forum_name'],
				];
			}
			else
			{
				$lang_key .= '_DELETED';
			}
		}
		else if ($type == self::LOGIN_HIDDEN)
		{
			$row['username'] = '<em>' . $row['username'] . '</em>';
		}

		$row['message'] = $this->lang->lang_array($lang_key, $lang_args);

		// Quick'n'dirty check if BBCodes are in the message
		if (strpos($row['message'], '[') !== false)
		{
			generate_text_for_storage($row['message'], $row['bbcode_uid'], $row['bbcode_bitfield'], $row['bbcode_options'], true, true, true, true, true, true, true, 'mchat');
		}

		$row['message'] = strtr($row['message'], $replacements);

		return $row;
	}

	/**
	 * Inserts a message with posting information into the database
	 *
	 * @param string $mode One of post|quote|edit|reply
	 * @param int $forum_id
	 * @param int $post_id
	 */
	public function insert_post($mode, $forum_id, $post_id)
	{
		$this->insert($mode, $forum_id, $post_id);
	}

	/**
	 * Inserts a message with login information into the database
	 *
	 * @param bool $is_hidden
	 */
	public function insert_login($is_hidden)
	{
		$this->insert('login', 0, $is_hidden ? self::LOGIN_HIDDEN : self::LOGIN_VISIBLE);
	}

	/**
	 * Inserts a message with posting or login information into the database
	 *
	 * @param string $mode One of post|quote|edit|reply|login
	 * @param int $forum_id
	 * @param int $post_id Can be 0 if mode is login.
	 */
	protected function insert($mode, $forum_id, $post_id)
	{
		$mode_config = [
			'post'	=> 'mchat_posts_topic',
			'quote'	=> 'mchat_posts_quote',
			'edit'	=> 'mchat_posts_edit',
			'reply'	=> 'mchat_posts_reply',
			'login' => 'mchat_posts_login',
		];

		$is_mode_enabled = !empty($mode_config[$mode]) && $this->mchat_settings->cfg($mode_config[$mode]) && (!$this->mchat_settings->cfg('mchat_posts_auth_check') || $this->can_use_mchat());

		$sql_array = [
			'forum_id'		=> (int) $forum_id,
			'post_id'		=> (int) $post_id,
			'user_id'		=> (int) $this->user->data['user_id'],
			'user_ip'		=> $this->user->ip,
			'message'		=> $this->textformatter_parser->parse('MCHAT_NEW_' . strtoupper($mode)),
			'message_time'	=> time(),
		];

		/**
		 * Event that allows to modify data of a posting or login notification before it is inserted in the database
		 *
		 * @event dmzx.mchat.insert_posting_before
		 * @var string	mode			The posting mode, one of post|quote|edit|reply|login
		 * @var int		forum_id		The ID of the forum where the post was made, or 0 if mode is login.
		 * @var int		post_id			The ID of the post that was made. If mode is login this value is
		 * 								one of the constants LOGIN_HIDDEN|LOGIN_VISIBLE
		 * @var array	is_mode_enabled	Whether or not the posting should be added to the database.
		 * @var array	sql_array		An array containing the data that is about to be inserted into the messages table.
		 * @since 2.0.0-RC6
		 * @changed 2.1.0-RC1 Removed is_hidden_login
		 */
		$vars = [
			'mode',
			'forum_id',
			'post_id',
			'is_mode_enabled',
			'sql_array',
		];
		extract($this->dispatcher->trigger_event('dmzx.mchat.insert_posting_before', compact($vars)));

		if ($is_mode_enabled)
		{
			$sql = 'INSERT INTO ' .	$this->mchat_settings->get_table_mchat() . ' ' . $this->db->sql_build_array('INSERT', $sql_array);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * The user might have just logged in successfully in which case the permissions haven't been updated yet.
	 * Let's do that here so that notifications are recorded correctly.
	 *
	 * @return bool
	 */
	protected function can_use_mchat()
	{
		if ($this->auth->acl_get('u_mchat_use'))
		{
			return true;
		}

		$auth = new auth();
		$auth->acl($this->user->data);
		return $auth->acl_get('u_mchat_use');
	}

	/**
	 * Generates an SQL WHERE condition to include or exlude notifacation
	 * messages based on the current user's settings and permissions
	 *
	 * @param string $mode One of user|exclude. user mode uses the current user's settings to decide which notifications
	 *                     to exclude. exclude mode always excludes all notifications.
	 * @return string
	 */
	public function get_sql_where($mode = 'user')
	{
		// Exclude all post notifications
		if ($mode == 'exclude' || !$this->mchat_settings->cfg('mchat_posts'))
		{
			return 'm.post_id = 0';
		}

		// If the current user doesn't have permission to see hidden users, exclude their login posts
		if (!$this->auth->acl_get('u_viewonline'))
		{
			return implode(' OR ', [
				'm.post_id <> ' . self::LOGIN_HIDDEN,					// Exclude all notifications that were created by hidden users ...
				'm.user_id = ' . (int) $this->user->data['user_id'],	// ... but include all login notifications of the current user
				'm.forum_id <> 0',										// ... and include all post notifications
			]);
		}

		return '';
	}

	/**
	 * Delete post notification messages, for example when disapproving posts
	 *
	 * @param array $post_ids
	 */
	public function delete_post_notifications($post_ids)
	{
		if ($post_ids)
		{
			$sql = 'DELETE FROM ' . $this->mchat_settings->get_table_mchat() . '
		 		WHERE forum_id <> 0 AND ' . $this->db->sql_in_set('post_id', $post_ids);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Change the user to which a post notification belongs
	 *
	 * @param int $post_id
	 * @param int $user_id
	 */
	public function update_post_notification_user($post_id, $user_id)
	{
		$sql = 'UPDATE ' . $this->mchat_settings->get_table_mchat() . '
			SET user_id = ' . (int) $user_id . '
			WHERE forum_id <> 0 AND post_id = ' . (int) $post_id;
		$this->db->sql_query($sql);
	}
}

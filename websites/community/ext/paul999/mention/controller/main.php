<?php
/**
 *
 * phpBB mentions. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016, paul999, https://www.phpbbextensions.io
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace paul999\mention\controller;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\exception\http_exception;
use phpbb\request\request_interface;
use phpbb\user;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * phpBB mentions main controller.
 */
class main
{
	/**
	 * @var user
	 */
	protected $user;

	/**
	 * @var driver_interface
	 */
	private $db;

	/**
	 * @var auth
	 */
	private $auth;

	/**
	 * @var request_interface
	 */
	private $request;

	/**
	 * @var config
	 */
	private $config;

	/**
	 * Constructor
	 *
	 * @param user $user
	 * @param driver_interface $db
	 * @param auth $auth
	 * @param request_interface $request
	 * @param config $config
	 */
	public function __construct(user $user, driver_interface $db, auth $auth, request_interface $request, config $config)
	{
		$this->user = $user;
		$this->db = $db;
		$this->auth = $auth;
		$this->request = $request;
		$this->config = $config;
	}

	/**
	 * get a list of users matching on a username (Minimal 3 chars)
	 *
	 *
	 * @return JsonResponse A Symfony Response object
	 */
	public function handle() : JsonResponse
	{
		if ($this->user->data['user_id'] == ANONYMOUS || $this->user->data['is_bot'] || !$this->auth->acl_get('u_can_mention'))
		{
			throw new http_exception(401);
		}
		$name = utf8_clean_string($this->request->variable('q', '', true));

		if (strlen($name) < $this->config['simple_mention_minlength'])
		{
			return new JsonResponse([]);
		}

		$sql = 'SELECT user_id, username 
					FROM ' . USERS_TABLE . ' 
					WHERE user_id <> ' . ANONYMOUS . ' 
					AND ' . $this->db->sql_in_set('user_type', [USER_NORMAL, USER_FOUNDER]) .  '
					AND username_clean ' . $this->db->sql_like_expression($this->db->get_any_char() . $name . $this->db->get_any_char());
		$result = $this->db->sql_query_limit($sql, max(5, (int) $this->config['simple_mention_maxresults']), 0);
		$return = [];

		while ($row = $this->db->sql_fetchrow($result))
		{
			$return[] = [
				'key'       => $row['username'],
				'value'     => $row['username'],
				'user_id'	=> $row['user_id'],
				'type'		=> 'user',
			];
		}
		$this->db->sql_freeresult($result);

		if ($this->auth->acl_get('u_can_mention_groups'))
		{
			// We can mention groups. So also add that to the list.

			$sql = 'SELECT COUNT(ug.user_id) as cnt, g.group_name, g.group_id, g.group_type
				FROM '. USER_GROUP_TABLE . ' ug, ' . GROUPS_TABLE . ' g
				WHERE 
						g.group_id = ug.group_id
						AND g.group_type <> ' . GROUP_HIDDEN . '
						AND ' . $this->db->sql_in_set('g.group_name', ['GUESTS', 'BOTS'], true) . '
						AND (
								LOWER(g.group_name) ' . $this->db->sql_like_expression($this->db->get_any_char() . strtolower($name) . $this->db->get_any_char()) . '
								OR g.group_type = ' . GROUP_SPECIAL . ' 
							) 
				GROUP BY ug.group_id';

			$result = $this->db->sql_query_limit($sql, max(5, (int) $this->config['simple_mention_maxresults']), 0);

			while ($row = $this->db->sql_fetchrow($result))
			{
				if ($row['cnt'] > $this->config['simple_mention_large_groups'] && !$this->auth->acl_get('u_can_mention_large_groups'))
				{
					// User can't send mentions to large groups.
					continue;
				}
				$return[] = [
					'key'       => $row['group_type'] == GROUP_SPECIAL ? $this->user->lang('G_' . $row['group_name']) : $row['group_name'],
					'value'     => $row['group_type'] == GROUP_SPECIAL ? $this->user->lang('G_' . $row['group_name']) : $row['group_name'],
					'group_id'	=> $row['group_id'],
					'cnt'		=> $row['cnt'],
					'type'		=> 'group',
				];
			}
			$this->db->sql_freeresult($result);
		}

		return new JsonResponse($return);
	}
}

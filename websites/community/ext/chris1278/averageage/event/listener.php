<?php
/**
*
* Display of the average age of the users
*
* @copyright (c) 2022, Chris1278
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace chris1278\averageage\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
	protected $auth;
	protected $language;
	protected $db;
	protected $template;

	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\language\language $language,
		\phpbb\db\driver\driver_interface $db,
		\phpbb\template\template $template
	)
	{
		$this->auth 			= $auth;
		$this->language			= $language;
		$this->db				= $db;
		$this->template			= $template;
	}

	public static function getSubscribedEvents()
	{
		return [
			'core.user_setup'			=> 'load_language_on_setup',
			'core.permissions'			=> 'permissions',
			'core.page_header_after'	=> 'show_average_age_of_users_in_stats',
		];
	}

	public function load_language_on_setup($event)
	{
		$lang_set_ext	= $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name'	=> 'chris1278/averageage',
			'lang_set'	=> 'common',
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	public function permissions($event)
	{
		$permissions = $event['permissions'];
		$permissions += [
			'u_averageage'	=> [
				'lang'		=> 'ACL_U_AVERAGEAGE',
				'cat'		=> 'profile'
			],

		];

		$event['permissions'] = $permissions;
	}

	public function show_average_age_of_users_in_stats()
	{
		if ($this->auth->acl_get('u_averageage'))
		{
			$sql = 'SELECT user_birthday, group_id
				FROM ' . USERS_TABLE  . '
				WHERE ' . $this->db->sql_in_set('user_type', [USER_NORMAL, USER_FOUNDER]);
			$result	= $this->db->sql_query($sql);
			$birthdays = $this->db->sql_fetchrowset($result);
			$this->db->sql_freeresult($result);

			$users = $averageage = 0;

			$year = date('Y');

			foreach ($birthdays as $row)
			{
				if ($row['user_birthday'] != '')
				{
					$birthday = explode('-', $row['user_birthday']);

					if ($birthday[2] != '0')
					{
						$averageage += $year - $birthday[2];
						$users++;
					}
				}
			}

			$this->template->assign_var('DISPLAY_AVERAGE_AGE',  ($users > 0 ? $this->language->lang('DISPLAY_AVERAGE_AGE', round(($averageage / $users))) : ''));
		}
	}

}

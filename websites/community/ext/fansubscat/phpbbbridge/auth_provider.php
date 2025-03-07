<?php
/**
*
* This file is part of the phpBB Forum Software package.
*
* @copyright (c) phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
* For full copyright and license information, please see
* the docs/CREDITS.txt file.
*
*/

namespace fansubscat\phpbbbridge;

use phpbb\config\config;
use phpbb\db\driver\driver_interface;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\user;

/**
* Fansubs.cat authentication provider for phpBB3
*/
class auth_provider extends \phpbb\auth\provider\base
{
	/** @var config phpBB config */
	protected $config;

	/** @var driver_interface Database object */
	protected $db;

	/** @var language Language object */
	protected $language;

	/** @var request_interface Request object */
	protected $request;

	/** @var user User object */
	protected $user;

	/** @var string Relative path to phpBB root */
	protected $phpbb_root_path;

	/** @var string PHP file extension */
	protected $php_ext;

	/**
	 * Fansubs.cat Authentication Constructor
	 *
	 * @param	config 				$config		Config object
	 * @param	driver_interface 	$db		Database object
	 * @param	language			$language Language object
	 * @param	request_interface 	$request		Request object
	 * @param	user 				$user		User object
	 * @param	string 				$phpbb_root_path		Relative path to phpBB root
	 * @param	string 				$php_ext		PHP file extension
	 */
	public function __construct(config $config, driver_interface $db, language $language, request_interface $request, user $user, $phpbb_root_path, $php_ext)
	{
		$this->config = $config;
		$this->db = $db;
		$this->language = $language;
		$this->request = $request;
		$this->user = $user;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function login($username, $password)
	{
		// do not allow empty password
		if (!$password)
		{
			return array(
				'status'	=> LOGIN_ERROR_PASSWORD,
				'error_msg'	=> 'NO_PASSWORD_SUPPLIED',
				'user_row'	=> array('user_id' => ANONYMOUS),
			);
		}

		if (!$username)
		{
			return array(
				'status'	=> LOGIN_ERROR_USERNAME,
				'error_msg'	=> 'LOGIN_ERROR_USERNAME',
				'user_row'	=> array('user_id' => ANONYMOUS),
			);
		}
		$fansubs_user = $this->get_fansubs_data();

		if ($fansubs_user===NULL)
		{
			return array(
				'status'		=> LOGIN_ERROR_EXTERNAL_AUTH,
				'error_msg'		=> 'LOGIN_ERROR_EXTERNAL_AUTH_APACHE',
				'user_row'		=> array('user_id' => ANONYMOUS),
			);
		}
		
		if ($fansubs_user->username !== $username)
		{
			return array(
				'status'	=> LOGIN_ERROR_USERNAME,
				'error_msg'	=> 'LOGIN_ERROR_USERNAME',
				'user_row'	=> array('user_id' => ANONYMOUS),
			);
		}

		$sql = 'SELECT user_id, username, user_password, user_passchg, user_email, user_type
			FROM ' . USERS_TABLE . "
			WHERE username = '" . $this->db->sql_escape($fansubs_user->username) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			// User inactive...
			if ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE)
			{
				return array(
					'status'		=> LOGIN_ERROR_ACTIVE,
					'error_msg'		=> 'ACTIVE_ERROR',
					'user_row'		=> $row,
				);
			}

			// Successful login...
			return array(
				'status'		=> LOGIN_SUCCESS,
				'error_msg'		=> false,
				'user_row'		=> $row,
			);
		}

		// this is the user's first login so create an empty profile
		return array(
			'status'		=> LOGIN_SUCCESS_CREATE_PROFILE,
			'error_msg'		=> false,
			'user_row'		=> $this->user_row($fansubs_user),
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function autologin()
	{
		$fansubs_user = $this->get_fansubs_data();
		if ($fansubs_user===NULL)
		{
			return array();
		}

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . "
			WHERE username = '" . $this->db->sql_escape($fansubs_user->username) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			return ($row['user_type'] == USER_INACTIVE || $row['user_type'] == USER_IGNORE) ? array() : $row;
		}

		if (!function_exists('user_add'))
		{
			include($this->phpbb_root_path . 'includes/functions_user.' . $this->php_ext);
		}

		// create the user if he does not exist yet
		user_add($this->user_row($fansubs_user));

		$sql = 'SELECT *
			FROM ' . USERS_TABLE . "
			WHERE username_clean = '" . $this->db->sql_escape(utf8_clean_string($fansubs_user->username)) . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row)
		{
			return $row;
		}

		return array();
	}

	/**
	 * This function generates an array which can be passed to the user_add
	 * function in order to create a user
	 *
	 * @param 	string	$username 	The username of the new user.
	 *
	 * @return 	array 				Contains data that can be passed directly to
	 *								the user_add function.
	 */
	private function user_row($fansubs_user)
	{
		// first retrieve default group id
		$sql = 'SELECT group_id
			FROM ' . GROUPS_TABLE . "
			WHERE group_name = '" . $this->db->sql_escape('REGISTERED') . "'
				AND group_type = " . GROUP_SPECIAL;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if (!$row)
		{
			trigger_error('NO_GROUP');
		}

		// generate user account data
		return array(
			'username'		=> $fansubs_user->username,
			'user_password'	=> '',
			'user_email'	=> $fansubs_user->email,
			'user_birthday'	=> date_format(date_create_from_format('Y-m-d', $fansubs_user->birthdate), 'd-m-Y'),
			'group_id'		=> (int) $row['group_id'],
			'user_type'		=> USER_NORMAL,
			'user_ip'		=> $this->user->ip,
			'user_new'		=> ($this->config['new_member_post_limit']) ? 1 : 0,
			'user_avatar'		=> 'https://static.fansubs.cat/images/avatars/'.$fansubs_user->avatar_filename,
			'user_avatar_type'	=> 'avatar.driver.remote',
		);
	}

	/**
	 * {@inheritdoc}
	 */
	public function validate_session($user)
	{
		$fansubs_user = $this->get_fansubs_data();
		// Check if Fansubs.cat user is set and handle this case
		if ($fansubs_user!==NULL)
		{
			return ($fansubs_user->username === $user['username']) ? true : false;
		}

		// Fansubs.cat user is not set. A valid session is now determined by the user type (anonymous/bot or not)
		if ($user['user_type'] == USER_IGNORE)
		{
			return true;
		}

		return false;
	}

	/**
	 * This function gets the user data from Fansubs.cat by using the session_id cookie
	 *
	 * @return 	array 				Contains the user data if found, NULL if not found or error occurred.
	 */
	private function get_fansubs_data()
	{
		//Get the session id from the cookie
		$session_id = $this->request->variable('session_id', '', TRUE, request_interface::COOKIE);
		
		if ($session_id=='') {
			$GLOBALS['site_theme'] = $this->request->variable('site_theme', 'dark', TRUE, request_interface::COOKIE);
			return NULL;
		}
		
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, 'https://usuaris.fansubs.cat/do_get_user_data.php');
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Cookie: session_id=$session_id"));
		curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$output = curl_exec($curl);
		if (empty($output)){
			$GLOBALS['site_theme'] = $this->request->variable('site_theme', 'dark', TRUE, request_interface::COOKIE);
			return NULL;
		}
		curl_close($curl);
		
		$result = json_decode($output);
		
		if (empty($result) || $result->result!='ok') {
			$GLOBALS['site_theme'] = $this->request->variable('site_theme', 'dark', TRUE, request_interface::COOKIE);
			return NULL;
		}
		$GLOBALS['site_theme'] = ($result->user->site_theme==1 ? 'light' : 'dark');
		return $result->user;
	}
}

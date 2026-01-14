<?php
namespace fansubscat\phpbbbridge;

require_once(__DIR__.'/../../../../../common/config/config.inc.php');

use voku\helper\HtmlDomParser;

/**
 * Tadaima.cat API main controller.
 */
class api_controller {
	const FANSUBSCAT_STATIC_URL = "https://static.fansubs.cat";

	const API_TOKEN_HEADER = "X-Fansubscat-Api-Token";

	const ERROR_API_TOKEN_NOT_SPECIFIED = 1;
	const ERROR_METHOD_NOT_SUPPORTED = 2;
	const ERROR_NOT_FOUND = 3;
	const ERROR_PERMISSION_DENIED = 4;
	const ERROR_INVALID_REQUEST = 5;
	const ERROR_GENERIC_ERROR = 6;

	const SYSTEM_USER_ID = 2;

	/* @var \phpbb\user */
	protected $user;

	/* @var \phpbb\auth\auth */
	protected $auth;

	/* @var \phpbb\db\driver\factory */
	protected $db;

	/* @var \phpbb\request\request */
	protected $http_request;

	/* @var \phpbb\config\config */
	protected $config;

	/* @var \phpbb\content_visibility */
	protected $content_visibility;

	/* @var \phpbb\avatar\manager */
	protected $avatar_manager;

	/* @var \phpbb\profilefields\manager */
	protected $profile_fields_manager;

	/* @var \phpbb\event\dispatcher_interface */
	protected $phpbb_dispatcher;

	/* @var string */
	protected $phpbb_root_path;

	/* @var string */
	protected $php_ext;

	/* @var string */
	protected $table_prefix;

	/**
	 * Constructor
	 *
	 * @param \phpbb\auth\auth $auth
	 * @param \phpbb\user $user
	 * @param \phpbb\db\driver\factory $db
	 * @param \phpbb\request\request $request
	 * @param \phpbb\config\config $config
	 * @param \phpbb\content_visibility $content_visibility
	 * @param \phpbb\avatar\manager $avatar_manager
	 * @param \phpbb\profilefields\manager $profile_fields_manager
	 * @param \phpbb\event\dispatcher_interface $phpbb_dispatcher
	 * @param string $phpbb_root_path
	 * @param string $php_ext
	 * @param string $table_prefix
	 */
	public function __construct(\phpbb\auth\auth $auth, \phpbb\user $user, \phpbb\db\driver\factory $db,
		\phpbb\request\request $http_request, \phpbb\config\config $config, \phpbb\content_visibility $content_visibility,
		\phpbb\avatar\manager $avatar_manager, \phpbb\profilefields\manager $profile_fields_manager, 
		\phpbb\event\dispatcher_interface $phpbb_dispatcher, $phpbb_root_path, $php_ext, $table_prefix){
		$this->auth = $auth;
		$this->user = $user;
		$this->db = $db;
		$this->http_request = $http_request;
		$this->config = $config;
		$this->content_visibility = $content_visibility;
		$this->avatar_manager = $avatar_manager;
		$this->profile_fields_manager = $profile_fields_manager;
		$this->phpbb_dispatcher = $phpbb_dispatcher;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
		$this->table_prefix = $table_prefix;
	}

	/**
	 * Controller for route /api/{method}
	 *
	 * @param string $method Requested API method
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	public function handle($method, $param=NULL){
		//We validate that the headers are set properly
		if ($this->http_request->header(self::API_TOKEN_HEADER)=='' || $this->http_request->header(self::API_TOKEN_HEADER)!=INTERNAL_SERVICES_TOKEN){
			$result = array(
				"status" => 'ko',
				"error" => array(
					"code" => self::ERROR_API_TOKEN_NOT_SPECIFIED,
					"description" => 'Incorrect API token specified, check your request.',
				),
			);
			
			return $this->create_json_response($result, 400);
		}

		switch($method){
			case 'create_user':
				$response = $this->create_user();
				break;
			case 'update_profile':
				$response = $this->update_profile();
				break;
			case 'create_fansub':
				$response = $this->create_fansub();
				break;
			case 'update_fansub':
				$response = $this->update_fansub();
				break;
			case 'add_topic':
				$response = $this->add_topic();
				break;
			case 'add_reply':
				$response = $this->add_reply();
				break;
			case 'edit_post':
				$response = $this->edit_post();
				break;
			case 'delete_post':
				$response = $this->delete_post();
				break;
			case 'delete_user':
				$response = $this->delete_user();
				break;
			case 'add_chat_message':
				$response = $this->add_chat_message();
				break;
			case 'get_user_yearly_stats':
				$response = $this->get_user_yearly_stats();
				break;
			default:
				$result = array(
					"status" => 'ko',
					"error" => array(
						"code" => self::ERROR_METHOD_NOT_SUPPORTED,
						"description" => 'This method is not supported, check your request.',
					),
				);
				
				$response = $this->create_json_response($result, 400);
		}
		
		return $response;
	}

	/**
	 * Validates if the POST request is valid. The request already has an App id.
	 *
	 * @param bool $token_required Whether the request requires a token or not
	 *
	 * @return bool true if valid, false otherwise
	 */
	protected function validate_post_request(){
		if ($this->http_request->server('REQUEST_METHOD')==='POST'){
			$request = json_decode(file_get_contents('php://input'));
			return (($request!==NULL) ? $request : FALSE);
		}
		else{
			return FALSE;
		}
	}

	/**
	 * Registers the user.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_user(){
		//WE HAVE THESE PARAMETERS:
		// -username
		// -email
		// -pronoun
		// -birthdate
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		//This is needed for the user_add function
		require_once($this->phpbb_root_path . "includes/functions_user." . $this->php_ext);
		
		
		// first retrieve default group id
		$sql = 'SELECT group_id
			FROM ' . GROUPS_TABLE . "
			WHERE group_name = '" . $this->db->sql_escape('REGISTERED') . "'
				AND group_type = " . GROUP_SPECIAL;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		$birth_date = '';
		$birth_date .= str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'j'), 2, " ", STR_PAD_LEFT);
		$birth_date .= '-'.str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'n'), 2, " ", STR_PAD_LEFT);
		$birth_date .= '-'.str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'Y'), 4, " ", STR_PAD_LEFT);

		// generate user account data
		$user_row = array(
			'username'		=> $this->get_clean_username($request->username),
			'user_password'	=> '',
			'user_email'	=> $request->email,
			'user_birthday'	=> $birth_date,
			'group_id'		=> (int) $row['group_id'],
			'user_type'		=> USER_NORMAL,
			'user_ip'		=> $this->user->ip,
			'user_new'		=> 0,
			'user_avatar'		=> self::FANSUBSCAT_STATIC_URL.'/images/site/default_avatar.jpg',
			'user_avatar_type'	=> 'avatar.driver.remote',
		);
		$cp_data = array(
			'pf_pronoms'	=> $this->convert_pronoun($request->pronoun),
		);
			
		// effectively add the user
		$user_id = user_add($user_row, $cp_data);
		
		if (!empty($user_id)) {
			$output = array(
				"status" => 'ok',
				"user_id" => $user_id,
			);
		
			return $this->create_json_response($output, 200);
		}
		
		return $this->create_error_response("User could not be added");
	}

	/**
	 * Registers the fansub.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_fansub(){
		//WE HAVE THESE PARAMETERS:
		// -fansub_id
		// -username
		// -email
		// -url
		// -bluesky_url
		// -mastodon_url
		// -twitter_url
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		//This is needed for the user_add function
		require_once($this->phpbb_root_path . "includes/functions_user." . $this->php_ext);
		
		
		// first retrieve default group id
		$sql = 'SELECT group_id
			FROM ' . GROUPS_TABLE . "
			WHERE group_name = '" . $this->db->sql_escape('Fansubs') . "'";
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		// generate user account data
		$user_row = array(
			'username'		=> $this->get_clean_username($request->username),
			'user_password'	=> '',
			'user_email'	=> $request->email,
			'group_id'		=> (int) $row['group_id'],
			'user_type'		=> USER_NORMAL,
			'user_ip'		=> $this->user->ip,
			'user_new'		=> 0,
			'user_avatar'		=> self::FANSUBSCAT_STATIC_URL.'/images/icons/'.$request->fansub_id.'.png',
			'user_avatar_type'	=> 'avatar.driver.remote',
		);
		$cp_data = array(
			'pf_bluesky'	=> !empty($request->bluesky_url) ? $request->bluesky_url : '',
			'pf_mastodon'	=> !empty($request->mastodon_url) ? $request->mastodon_url : '',
			'pf_twitter'	=> !empty($request->twitter_url) ? $request->twitter_url : '',
			'pf_phpbb_website'	=> !empty($request->url) ? $request->url : '',
		);
			
		// effectively add the user
		$user_id = user_add($user_row, $cp_data);
		
		if (!empty($user_id)) {
			$output = array(
				"status" => 'ok',
				"user_id" => $user_id,
			);
		
			return $this->create_json_response($output, 200);
		}
		
		return $this->create_error_response("User could not be added");
	}

	/**
	 * Adds a new topic.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function add_topic(){
		//WE HAVE THESE PARAMETERS:
		// -username
		// -forum_id
		// -subject
		// -message
		// -timestamp
		// -locked (1/0)
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$sql = 'SELECT user_id 
			FROM ' . USERS_TABLE . "
			WHERE username='" . $this->db->sql_escape($this->get_clean_username($request->username)) . "'";
		$result = $this->db->sql_query($sql);
		$user_id = $this->db->sql_fetchrow($result)['user_id'];
		$this->db->sql_freeresult($result);
		
		$this->begin_user_session($user_id);

		//This is needed for the submit_post function
		require_once($this->phpbb_root_path . "includes/functions_posting." . $this->php_ext);
		
		//post new topic
		$mode = 'post';
		$sql = 'SELECT forum_name, forum_id, forum_status 
			FROM ' . FORUMS_TABLE . ' 
			WHERE forum_id=' . (int)$request->forum_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row===FALSE){
			return $this->create_not_found_response();
		}
		else{
			$forum_name = $row['forum_name'];
			$forum_id = $row['forum_id'];
		}

		if (!$this->auth->acl_get('f_post', $forum_id) || $row['forum_status'] == ITEM_LOCKED){
			return $this->create_permission_denied_response();
		}

		//Submit the new post
		$poll = $uid = $bitfield = $flags = '';
		generate_text_for_storage($request->message, $uid, $bitfield, $flags, true, true, true, true, false, true, true, 'post');
		$data = array( 
		    'forum_id' => $forum_id,
		    'icon_id' => false,
		    'poster_id' => $user_id,
		    'enable_bbcode' => true,
		    'enable_smilies' => true,
		    'enable_urls' => true,
		    'enable_sig' => true,
		    'message' => $request->message,
		    'message_md5' => md5($request->message),
		    'bbcode_bitfield' => $bitfield,
		    'bbcode_uid' => !empty($uid) ? $uid : 'fansubs',
		    'post_edit_locked' => 0,
		    'topic_title' => $request->subject,
		    'notify_set' => false,
		    'notify' => false,
		    'post_time' => $request->timestamp,
		    'forum_name' => $forum_name,
		    'enable_indexing' => true,
		    'topic_status' => $request->locked==1 ? ITEM_LOCKED : ITEM_UNLOCKED,
		);

		$result = submit_post($mode, $request->subject, $this->get_clean_username($request->username), POST_NORMAL, $poll, $data);
		
		$this->end_user_session();
		
		if (!empty($result)) {
			$output = array(
				"status" => 'ok',
				"topic_id" => $data['topic_id'],
				"post_id" => $data['post_id'],
			);
		
			return $this->create_json_response($output, 200);
		} else {
			return $this->create_error_response("Topic could not be added");
		}
	}

	/**
	 * Adds a new reply to a topic.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function add_reply(){
		//WE HAVE THESE PARAMETERS:
		// -username
		// -topic_id
		// -subject
		// -message
		// -timestamp
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$sql = 'SELECT user_id 
			FROM ' . USERS_TABLE . "
			WHERE username='" . $this->db->sql_escape($this->get_clean_username($request->username)) . "'";
		$result = $this->db->sql_query($sql);
		$user_id = $this->db->sql_fetchrow($result)['user_id'];
		$this->db->sql_freeresult($result);
		
		$this->begin_user_session($user_id);

		//This is needed for the submit_post function
		require_once($this->phpbb_root_path . "includes/functions_posting." . $this->php_ext);
		
		//post new message in existing topic
		$mode = 'reply';
		$sql = 'SELECT f.forum_name, f.forum_status, t.* 
			FROM ' . TOPICS_TABLE . ' t 
			LEFT JOIN ' . FORUMS_TABLE . ' f ON t.forum_id=f.forum_id 
			WHERE topic_id=' . (int)$request->topic_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row===FALSE){
			return $this->create_not_found_response();
		}
		else{
			$forum_name = $row['forum_name'];
			$forum_id = $row['forum_id'];
			$topic_id = (int)$request->topic_id;
		}

		//if (!$this->auth->acl_get('f_reply', $forum_id) || $row['topic_status'] == ITEM_LOCKED || $row['topic_status'] == ITEM_MOVED){
		//	return $this->create_permission_denied_response();
		//}

		//Submit the new post
		$poll = $uid = $bitfield = $flags = '';
		generate_text_for_storage($request->message, $uid, $bitfield, $flags, true, true, true, true, false, true, true, 'post');
		$data = array( 
		    'forum_id' => $forum_id,
		    'topic_id' => $topic_id,
		    'icon_id' => false,
		    'poster_id' => $user_id,
		    'enable_bbcode' => true,
		    'enable_smilies' => true,
		    'enable_urls' => true,
		    'enable_sig' => true,
		    'message' => $request->message,
		    'message_md5' => md5($request->message),
		    'bbcode_bitfield' => $bitfield,
		    'bbcode_uid' => !empty($uid) ? $uid : 'fansubs',
		    'post_edit_locked' => 0,
		    'topic_title' => $request->subject,
		    'notify_set' => false,
		    'notify' => false,
		    'post_time' => $request->timestamp,
		    'forum_name' => $forum_name,
		    'enable_indexing' => true,
		);

		$result = submit_post($mode, $request->subject, $this->get_clean_username($request->username), POST_NORMAL, $poll, $data);
		
		$this->end_user_session();
		
		if (!empty($result)) {
			$output = array(
				"status" => 'ok',
				"post_id" => $data['post_id'],
			);
		
			return $this->create_json_response($output, 200);
		} else {
			return $this->create_error_response("Reply could not be added");
		}
	}

	/**
	 * Updates a post with new text.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function edit_post(){
		//WE HAVE THESE PARAMETERS:
		// -post_id
		// -subject
		// -message
		// -locked (1/0)
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		
		$sql = 'SELECT * 
			FROM ' . POSTS_TABLE . ' 
			WHERE post_id=' . (int)$request->post_id;
		$result = $this->db->sql_query($sql);
		$postrow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if ($postrow===FALSE){
			return $this->create_not_found_response();
		}
		
		$this->begin_user_session($postrow['poster_id']);

		//This is needed for the submit_post function
		require_once($this->phpbb_root_path . "includes/functions_posting." . $this->php_ext);
		
		//post new message in existing topic
		$mode = 'edit';
		$sql = 'SELECT f.forum_name, f.forum_status, t.* 
			FROM ' . TOPICS_TABLE . ' t 
			LEFT JOIN ' . FORUMS_TABLE . ' f ON t.forum_id=f.forum_id 
			WHERE topic_id=' . $postrow['topic_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row===FALSE){
			return $this->create_not_found_response();
		}
		
		$forum_name = $row['forum_name'];
		$forum_id = $row['forum_id'];
		$topic_id = $postrow['topic_id'];

		//if (!$this->auth->acl_get('f_edit', $forum_id) || $row['topic_status'] == ITEM_LOCKED || $row['topic_status'] == ITEM_MOVED){
		//	return $this->create_permission_denied_response();
		//}

		//Submit the new post
		$poll = $uid = $bitfield = $flags = '';
		generate_text_for_storage($request->message, $uid, $bitfield, $flags, true, true, true, true, false, true, true, 'post');
		$data = array( 
		    'forum_id' => $forum_id,
		    'topic_id' => $topic_id,
		    'icon_id' => false,
		    'poster_id' => $postrow['poster_id'],
		    'enable_bbcode' => true,
		    'enable_smilies' => true,
		    'enable_urls' => true,
		    'enable_sig' => true,
		    'message' => $request->message,
		    'message_md5' => md5($request->message),
		    'bbcode_bitfield' => $bitfield,
		    'bbcode_uid' => !empty($uid) ? $uid : 'fansubs',
		    'post_edit_locked' => 0,
		    'topic_title' => $request->subject,
		    'notify_set' => false,
		    'notify' => false,
		    'forum_name' => $forum_name,
		    'enable_indexing' => true,
		);

		//Additional for EDIT mode:
		$data['post_id'] = (int)$request->post_id;
		$data['topic_posts_approved'] = $row['topic_posts_approved'];
		$data['topic_posts_unapproved'] = $row['topic_posts_unapproved'];
		$data['topic_posts_softdeleted'] = $row['topic_posts_softdeleted'];
		$data['topic_first_post_id'] = $row['topic_first_post_id'];
		$data['topic_first_poster_name'] = $row['topic_first_poster_name'];
		$data['topic_last_post_id'] = $row['topic_last_post_id'];
		$data['post_username'] = $postrow['post_username'];
		$data['post_edit_reason'] = '';
		$data['post_edit_user'] = $postrow['poster_id'];

		$result = submit_post($mode, $request->subject, $this->user->data['username'], POST_NORMAL, $poll, $data);
		
		$sql = 'UPDATE ' . TOPICS_TABLE . '
			SET topic_status = ' . ($request->locked==1 ? ITEM_LOCKED : ITEM_UNLOCKED) . '
			WHERE topic_id = ' . (int) $topic_id;
		$this->db->sql_query($sql);
		
		$this->end_user_session();
		
		if (!empty($result)) {
			$output = array(
				"status" => 'ok',
				"post_id" => $data['post_id'],
			);
		
			return $this->create_json_response($output, 200);
		} else {
			return $this->create_error_response("Post could not be edited");
		}
	}

	/**
	 * Deletes a post permanently.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function delete_post(){
		//WE HAVE THESE PARAMETERS:
		// -post_id
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		
		$sql = "SELECT f.*, t.*, p.*, u.username, u.username_clean, u.user_sig, u.user_sig_bbcode_uid, u.user_sig_bbcode_bitfield
			FROM " . POSTS_TABLE . " p, " . TOPICS_TABLE . " t, " . FORUMS_TABLE . " f, " . USERS_TABLE . " u
			WHERE p.post_id = ".(int)$request->post_id."
				AND t.topic_id = p.topic_id
				AND u.user_id = p.poster_id
				AND f.forum_id = t.forum_id";
				
		$result = $this->db->sql_query($sql);
		$postrow = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if ($postrow===FALSE) {
			return $this->create_not_found_response();
		}
		
		$this->begin_user_session(self::SYSTEM_USER_ID);

		//This is needed for the delete_post function
		require_once($this->phpbb_root_path . "includes/functions_posting." . $this->php_ext);
		
		//post new message in existing topic
		$sql = 'SELECT f.forum_name, f.forum_status, t.* 
			FROM ' . TOPICS_TABLE . ' t 
			LEFT JOIN ' . FORUMS_TABLE . ' f ON t.forum_id=f.forum_id 
			WHERE topic_id=' . $postrow['topic_id'];
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($row===FALSE){
			return $this->create_not_found_response();
		}
		
		$forum_name = $row['forum_name'];
		$forum_id = $row['forum_id'];
		$topic_id = $postrow['topic_id'];

		if (!$this->auth->acl_get('f_delete', $forum_id)){
			return $this->create_permission_denied_response();
		}

		//Delete the post

		delete_post($forum_id, $topic_id, (int)$request->post_id, $postrow);
		
		$this->end_user_session();
		
		return $this->create_generic_ok_response();
	}

	/**
	 * Deletes a user permanently and all their posts.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function delete_user(){
		//WE HAVE THESE PARAMETERS:
		// -user_id
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		
		$sql = 'SELECT * 
			FROM ' . USERS_TABLE . ' 
			WHERE user_id=' . (int)$request->user_id;
		$result = $this->db->sql_query($sql);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);
		
		if ($row===FALSE) {
			return $this->create_not_found_response();
		}
		
		$this->begin_user_session(self::SYSTEM_USER_ID);

		//This is needed for the user_delete function
		require_once($this->phpbb_root_path . "includes/functions_user." . $this->php_ext);

		//Delete the user
		user_delete('remove', array($row['user_id']));
		
		$this->end_user_session();
		
		return $this->create_generic_ok_response();
	}

	/**
	 * Updates user profile attributes that are shared between Fansubs.cat and phpBB.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function update_profile(){
		//WE HAVE THESE PARAMETERS:
		// -username_old
		// -username
		// -email
		// -pronoun
		// -avatar_url
		// -birth_date
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$birth_date = '';
		$birth_date .= str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'j'), 2, " ", STR_PAD_LEFT);
		$birth_date .= '-'.str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'n'), 2, " ", STR_PAD_LEFT);
		$birth_date .= '-'.str_pad(date_format(date_create_from_format('Y-m-d', $request->birthdate), 'Y'), 4, " ", STR_PAD_LEFT);
		
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_avatar = '" . $this->db->sql_escape($request->avatar_url) . "',
				user_avatar_type = 'avatar.driver.remote',
				user_email = '" . $this->db->sql_escape($request->email) . "',
				user_birthday = '" . $birth_date . "',
				username = '" . $this->db->sql_escape($this->get_clean_username($request->username)) . "',
				username_clean = '" . $this->db->sql_escape(utf8_clean_string($this->get_clean_username($request->username))) . "'
			WHERE username = '" . $this->db->sql_escape($request->username_old) . "'";
		$this->db->sql_query($sql);
		$sql = 'UPDATE ' . PROFILE_FIELDS_DATA_TABLE . "
			SET pf_pronoms = '" . $this->db->sql_escape($this->convert_pronoun($request->pronoun)) . "'
			WHERE user_id = (SELECT u.user_id FROM " . USERS_TABLE . " u WHERE username = '" . $this->db->sql_escape($request->username) . "')";
		$this->db->sql_query($sql);
		
		//This is needed for the user_update_name function
		require_once($this->phpbb_root_path . "includes/functions_user." . $this->php_ext);
		
		user_update_name($request->username_old, $request->username);
		
		return $this->create_generic_ok_response();
	}

	/**
	 * Updates fansub profile attributes that are shared between Fansubs.cat and phpBB.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function update_fansub(){
		//WE HAVE THESE PARAMETERS:
		// -fansub_id
		// -username_old
		// -username
		// -email
		// -url
		// -bluesky_url
		// -mastodon_url
		// -twitter_url
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$sql = 'UPDATE ' . USERS_TABLE . "
			SET user_avatar = '" . $this->db->sql_escape(self::FANSUBSCAT_STATIC_URL.'/images/icons/'.$request->fansub_id.'.png') . "',
				user_avatar_type = 'avatar.driver.remote',
				user_email = '" . $this->db->sql_escape($request->email) . "',
				username = '" . $this->db->sql_escape($this->get_clean_username($request->username)) . "',
				username_clean = '" . $this->db->sql_escape(utf8_clean_string($this->get_clean_username($request->username))) . "'
			WHERE username = '" . $this->db->sql_escape($request->username_old) . "'";
		$this->db->sql_query($sql);
		$sql = 'UPDATE ' . PROFILE_FIELDS_DATA_TABLE . "
			SET pf_bluesky = '" . $this->db->sql_escape($request->bluesky_url) . "',
				pf_twitter = '" . $this->db->sql_escape($request->twitter_url) . "',
				pf_phpbb_website = '" . $this->db->sql_escape($request->url) . "',
				pf_mastodon = '" . $this->db->sql_escape($request->mastodon_url) . "'
			WHERE user_id = (SELECT u.user_id FROM " . USERS_TABLE . " u WHERE username = '" . $this->db->sql_escape($request->username) . "')";
		$this->db->sql_query($sql);
		
		//This is needed for the user_update_name function
		require_once($this->phpbb_root_path . "includes/functions_user." . $this->php_ext);
		
		user_update_name($request->username_old, $request->username);
		
		return $this->create_generic_ok_response();
	}

	/**
	 * Adds a new chat message.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function add_chat_message(){
		global $table_prefix;
		//WE HAVE THESE PARAMETERS:
		// -message
		
		define('FANSUBSCAT_API_POSTING', TRUE);
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$this->begin_user_session(self::SYSTEM_USER_ID);

		//This is needed for the submit_post function
		require_once($this->phpbb_root_path . "includes/functions_posting." . $this->php_ext);
		
		$uid = $bitfield = $flags = '';
		$message = $request->message;
		generate_text_for_storage($message, $uid, $bitfield, $flags, true, true, true, true, false, true, true, 'mchat');
		
		$sql_ary = [
			'message'			=> str_replace("'", '&#39;', $message),
			'bbcode_bitfield'	=> $bitfield,
			'bbcode_uid'		=> $uid,
			'bbcode_options'	=> $flags,
			'user_id'		=> $this->user->data['user_id'],
			'user_ip'		=> $this->user->ip,
			'message_time'		=> time(),
		];
		
		$this->db->sql_query('INSERT INTO ' . $table_prefix . 'mchat' . $this->db->sql_build_array('INSERT', $sql_ary));
		
		$this->end_user_session();
		
		return $this->create_generic_ok_response();
	}

	/**
	 * Gets user stats used for the yearly summary page.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function get_user_yearly_stats(){
		global $table_prefix;
		//WE HAVE THESE PARAMETERS:
		// -username
		// -year
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$request = $this->validate_post_request();
		if ($request===FALSE){
			return $this->create_invalid_format_response();
		}
		
		$sql = 'SELECT user_id 
			FROM ' . USERS_TABLE . "
			WHERE username='" . $this->db->sql_escape($this->get_clean_username($request->username)) . "'";
		$result = $this->db->sql_query($sql);
		$user_id = $this->db->sql_fetchrow($result)['user_id'];
		$this->db->sql_freeresult($result);
		
		$sql = 'SELECT t.topic_title 
			FROM ' . POSTS_TABLE . " p
				LEFT JOIN " . TOPICS_TABLE . " t ON p.topic_id=t.topic_id
			WHERE p.poster_id=$user_id
				AND IF(t.forum_id NOT IN (4,5,6),1,t.topic_poster<>".self::SYSTEM_USER_ID.")
				AND p.post_time>=UNIX_TIMESTAMP('".$this->db->sql_escape((int)$request->year)."-01-01 00:00:00')
				AND p.post_time<=UNIX_TIMESTAMP('".$this->db->sql_escape((int)$request->year)."-12-31 23:59:59')
			GROUP BY t.topic_id
			ORDER BY COUNT(*) DESC
			LIMIT 1";
		$result = $this->db->sql_query($sql);
		$most_posted_topic = $this->db->sql_fetchrow($result)['topic_title'];
		$this->db->sql_freeresult($result);
		
		$sql = 'SELECT COUNT(*) cnt
			FROM ' . POSTS_TABLE . " p
				LEFT JOIN " . TOPICS_TABLE . " t ON p.topic_id=t.topic_id
			WHERE p.poster_id=$user_id
				AND IF(t.forum_id NOT IN (4,5,6),1,t.topic_poster<>".self::SYSTEM_USER_ID.")
				AND p.post_time>=UNIX_TIMESTAMP('".$this->db->sql_escape((int)$request->year)."-01-01 00:00:00')
				AND p.post_time<=UNIX_TIMESTAMP('".$this->db->sql_escape((int)$request->year)."-12-31 23:59:59')";
		$result = $this->db->sql_query($sql);
		$number_of_posts = $this->db->sql_fetchrow($result)['cnt'];
		$this->db->sql_freeresult($result);
		
		$output = array(
			"status" => 'ok',
			"number_of_posts" => $number_of_posts,
			"most_posted_topic" => $most_posted_topic,
		);
	
		return $this->create_json_response($output, 200);
	}

	/************* UTILITY METHODS *************/

	/**
	 * Creates a generic OK response.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_generic_ok_response(){
		$result = array(
			"status" => 'ok',
		);
	
		return $this->create_json_response($result, 200);
	}

	/**
	 * Creates a generic invalid format response.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_error_response($error){
		$result = array(
			"status" => 'ko',
			"error" => array(
				"code" => self::ERROR_GENERIC_ERROR,
				"description" => $error,
			),
		);
	
		return $this->create_json_response($result, 500);
	}

	/**
	 * Creates a generic invalid format response.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_invalid_format_response(){
		$result = array(
			"status" => 'ko',
			"error" => array(
				"code" => self::ERROR_INVALID_REQUEST,
				"description" => 'The request has an invalid format. Please make sure that you set the appropriate headers and parameters.',
			),
		);
	
		return $this->create_json_response($result, 400);
	}

	/**
	 * Creates a generic not found response.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_not_found_response(){
		$result = array(
			"status" => 'ko',
			"error" => array(
				"code" => self::ERROR_NOT_FOUND,
				"description" => 'Not found.',
			),
		);
	
		return $this->create_json_response($result, 404);
	}

	/**
	 * Creates a generic permission denied response.
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_permission_denied_response(){
		$result = array(
			"status" => 'ko',
			"error" => array(
				"code" => self::ERROR_PERMISSION_DENIED,
				"description" => 'You are not authorized to perform this action.',
			),
		);

		return $this->create_json_response($result, 403);
	}

	/**
	 * Tells the phpBB software to start treating the current session as the specified user id.
	 *
	 * @param int $user_id
	 */
	protected function begin_user_session($user_id, $use_request_vars = FALSE){
		//Log in as that user
		$this->user->session_create($user_id, false, false, false);
		$this->auth->acl($this->user->data);
		$this->user->setup();
	}

	/**
	 * Tells the phpBB software to end the current session
	 */
	protected function end_user_session(){
		$this->user->session_kill(FALSE);
	}

	/**
	 * Replaces emojis in usernames
	 */
	protected function get_clean_username($username){
		return preg_replace("/[\x{10000}-\x{10FFFF}]/u", '_', $username);
	}

	/**
	 * Converts pronouns from Fansubs.cat system (null, male, female, nonbinary) to phpBB system (1, 2, 3, 4)
	 */
	protected function convert_pronoun($pronoun){
		if ($pronoun=='male'){
			return 2;
		} else if ($pronoun=='female'){
			return 3;
		} else if ($pronoun=='nonbinary'){
			return 4;
		}
		return 1;
	}

	/**
	 * Creates a \Symfony\Component\HttpFoundation\Response with the proper
	 * parameters for a JSON response.
	 *
	 * @param array $result Array ready for JSON generation
	 * @param int $http_code HTTP code for the response
	 *
	 * @return \Symfony\Component\HttpFoundation\Response A Symfony Response object
	 */
	protected function create_json_response($result, $http_code){
		$headers = array(
			"Content-Type" => "application/json",
		);
		return new \Symfony\Component\HttpFoundation\Response(json_encode($result), $http_code, $headers);
	}
}


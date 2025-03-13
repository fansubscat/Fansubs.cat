<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\notification\type;

use phpbb\config\config;
use phpbb\controller\helper;
use phpbb\notification\type\base;
use phpbb\user_loader;

/**
* Profile Flair notification type.
*/
class flair extends base
{
	/**
	 * @inheritDoc
	 */
	public static $notification_option = array(
		'lang'	=> 'FLAIR_NOTIFICATION_TYPE',
	);

	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var user_loader
	 */
	protected $user_loader;

	/**
	 * Set up the notification type.
	 *
	 * @param config      $config
	 * @param helper      $helper
	 * @param user_loader $user_loader
	 */
	public function setup(config $config, helper $helper, user_loader $user_loader)
	{
		$this->config = $config;
		$this->helper = $helper;
		$this->user_loader = $user_loader;
	}

	/**
	 * @inheritDoc
	 */
	public function get_type()
	{
		return 'stevotvr.flair.notification.type.flair';
	}

	/**
	 * @inheritDoc
	 */
	public static function get_item_id($data)
	{
		return $data['notification_id'];
	}

	/**
	 * @inheritDoc
	 */
	public static function get_item_parent_id($data)
	{
		return 0;
	}

	/**
	 * @inheritDoc
	 */
	public function is_available()
	{
		return (bool) $this->config['stevotvr_flair_notify_users'];
	}

	/**
	 * @inheritDoc
	 */
	public function find_users_for_notification($data, $options = array())
	{
		$options = array_merge(array(
			'ignore_users'	=> array(),
		), $options);

		return $this->check_user_notification_options((array) $data['user_id'], $options);
	}

	/**
	 * @inheritDoc
	 */
	public function users_to_query()
	{
		return array($this->get_data('user_id'));
	}

	/**
	 * @inheritDoc
	 */
	public function get_title()
	{
		return $this->language->lang('FLAIR_NOTIFICATION_TITLE', $this->get_data('flair_name'));
	}

	/**
	 * @inheritDoc
	 */
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . $this->get_data('user_id')) ;
	}

	/**
	 * @inheritDoc
	 */
	public function get_email_template()
	{
		return '@stevotvr_flair/flair';
	}

	/**
	 * @inheritDoc
	 */
	public function get_email_template_variables()
	{
		return array(
			'FLAIR_NAME'	=> $this->get_data('flair_name'),
			'U_PROFILE'	=> generate_board_url() . '/memberlist.' . $this->php_ext . '?mode=viewprofile&u=' . $this->get_data('user_id'),
		);
	}

	/**
	 * @inheritDoc
	 */
	public function get_avatar()
	{
		return $this->user_loader->get_avatar($this->get_data('user_id'), false, true);
	}

	/**
	 * @inheritDoc
	 */
	public function create_insert_array($data, $pre_create_data = array())
	{
		$this->set_data('user_id', $data['user_id']);
		$this->set_data('flair_id', $data['flair_id']);
		$this->set_data('flair_name', $data['flair_name']);

		parent::create_insert_array($data, $pre_create_data);
	}
}

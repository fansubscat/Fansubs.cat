<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\notification\type;

class group_added extends \phpbb\notification\type\base
{
	/**
	 * {@inheritdoc}
	 */
	public function get_type()
	{
		return 'phpbb.autogroups.notification.type.group_added';
	}

	/**
	 * {@inheritdoc}
	 */
	public function is_available()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_item_id($type_data)
	{
		return (int) $type_data['group_id'];
	}

	/**
	 * {@inheritdoc}
	 */
	public static function get_item_parent_id($type_data)
	{
		return 0;
	}

	/**
	 * {@inheritdoc}
	 */
	public function find_users_for_notification($type_data, $options = array())
	{
		$users = array();

		$type_data['user_ids'] = (!is_array($type_data['user_ids'])) ? array($type_data['user_ids']) : $type_data['user_ids'];

		foreach ($type_data['user_ids'] as $user_id)
		{
			$users[$user_id] = $this->notification_manager->get_default_methods();
		}

		return $users;
	}

	/**
	 * {@inheritdoc}
	 */
	public function users_to_query()
	{
		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title()
	{
		return $this->language->lang('AUTOGROUPS_NOTIFICATION_GROUP_ADDED', $this->get_data('group_name'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_url()
	{
		return append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, "mode=group&amp;g={$this->item_id}");
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_email_template()
	{
		return false;
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_email_template_variables()
	{
		return array();
	}

	/**
	 * {@inheritdoc}
	 */
	public function create_insert_array($type_data, $pre_create_data = array())
	{
		$this->set_data('group_name', $type_data['group_name']);

		parent::create_insert_array($type_data, $pre_create_data);
	}
}

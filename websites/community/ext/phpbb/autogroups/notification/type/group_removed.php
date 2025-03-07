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

class group_removed extends \phpbb\autogroups\notification\type\group_added
{
	/**
	 * {@inheritdoc}
	 */
	public function get_type()
	{
		return 'phpbb.autogroups.notification.type.group_removed';
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_title()
	{
		return $this->language->lang('AUTOGROUPS_NOTIFICATION_GROUP_REMOVED', $this->get_data('group_name'));
	}

	/**
	 * {@inheritdoc}
	 */
	public function get_url()
	{
		return '';
	}
}

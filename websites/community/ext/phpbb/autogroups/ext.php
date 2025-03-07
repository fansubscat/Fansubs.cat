<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups;

/**
 * This ext class is optional and can be omitted if left empty.
 * However you can add special (un)installation commands in the
 * methods enable_step(), disable_step() and purge_step(). As it is,
 * these methods are defined in \phpbb\extension\base, which this
 * class extends, but you can overwrite them to give special
 * instructions for those cases.
 */
class ext extends \phpbb\extension\base
{
	/**
	 * Check whether the extension can be enabled.
	 * The current phpBB version should meet or exceed
	 * the minimum version required by this extension:
	 *
	 * @return bool|array
	 * @access public
	 */
	public function is_enableable()
	{
		$enableable = $this->check_phpbb_version() && $this->check_php_version();

		if (!$enableable && phpbb_version_compare(PHPBB_VERSION, '3.3.0-b1', '>='))
		{
			$language = $this->container->get('language');
			$language->add_lang('autogroups_install', 'phpbb/autogroups');
			return $language->lang('AUTOGROUPS_NOT_ENABLEABLE');
		}

		return $enableable;
	}

	/**
	 * Require phpBB 3.2.0 due to the revised notifications system and new group helper.
	 *
	 * @return bool
	 */
	protected function check_phpbb_version()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
	}

	/**
	 * Requires PHP 5.5 due to array_column().
	 *
	 * @return bool
	 */
	protected function check_php_version()
	{
		return phpbb_version_compare(PHP_VERSION, '5.5.0', '>=');
	}

	/**
	 * Overwrite enable_step to enable Auto Groups notifications
	 * before any included migrations are installed.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function enable_step($old_state)
	{
		// if nothing has run yet
		if ($old_state === false)
		{
			// Enable Auto Groups notifications
			return $this->notification_handler('enable', array(
				'phpbb.autogroups.notification.type.group_added',
				'phpbb.autogroups.notification.type.group_removed',
			));
		}

		// Run parent enable step method
		return parent::enable_step($old_state);
	}

	/**
	 * Overwrite disable_step to disable Auto Groups notifications
	 * before the extension is disabled.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function disable_step($old_state)
	{
		// if nothing has run yet
		if ($old_state === false)
		{
			// Disable Auto Groups notifications
			return $this->notification_handler('disable', array(
				'phpbb.autogroups.notification.type.group_added',
				'phpbb.autogroups.notification.type.group_removed',
			));
		}

		// Run parent disable step method
		return parent::disable_step($old_state);
	}

	/**
	 * Overwrite purge_step to purge Auto Groups notifications before
	 * any included and installed migrations are reverted.
	 *
	 * @param mixed $old_state State returned by previous call of this method
	 * @return mixed Returns false after last step, otherwise temporary state
	 * @access public
	 */
	public function purge_step($old_state)
	{
		// if nothing has run yet
		if ($old_state === false)
		{
			// Purge Auto Groups notifications
			return $this->notification_handler('purge', array(
				'phpbb.autogroups.notification.type.group_added',
				'phpbb.autogroups.notification.type.group_removed',
			));
		}

		// Run parent purge step method
		return parent::purge_step($old_state);
	}

	/**
	 * Notification handler to call notification enable/disable/purge steps
	 *
	 * @param string $step               The step (enable, disable, purge)
	 * @param array  $notification_types The notification type names
	 * @return string Return notifications as temporary state
	 * @access protected
	 */
	protected function notification_handler($step, $notification_types)
	{
		$phpbb_notifications = $this->container->get('notification_manager');

		foreach ($notification_types as $notification_type)
		{
			$phpbb_notifications->{$step . '_notifications'}($notification_type);
		}

		return 'notifications';
	}
}

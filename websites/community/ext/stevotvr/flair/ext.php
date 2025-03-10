<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair;

use phpbb\extension\base;

/**
 * Profile Flair extension base.
 */
class ext extends base
{
	public function is_enableable()
	{
		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=');
	}

	public function enable_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->enable_notifications('stevotvr.flair.notification.type.flair');
				return 'notifications';
			default:
				return parent::enable_step($old_state);
		}
	}

	public function disable_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->disable_notifications('stevotvr.flair.notification.type.flair');
				return 'notifications';
			default:
				return parent::disable_step($old_state);
		}
	}

	public function purge_step($old_state)
	{
		switch ($old_state)
		{
			case '':
				$phpbb_notifications = $this->container->get('notification_manager');
				$phpbb_notifications->purge_notifications('stevotvr.flair.notification.type.flair');
				return 'notifications';
			default:
				return parent::purge_step($old_state);
		}
	}
}

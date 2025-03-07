<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\autogroups\conditions\manager */
	protected $manager;

	/**
	 * Constructor
	 *
	 * @param \phpbb\autogroups\conditions\manager $manager Auto groups condition manager object
	 * @access public
	 */
	public function __construct(\phpbb\autogroups\conditions\manager $manager)
	{
		$this->manager = $manager;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core
	 *
	 * @return array
	 * @static
	 * @access public
	 */
	public static function getSubscribedEvents()
	{
		return array(
			'core.delete_group_after'	=> 'delete_group_rules',
			'core.user_setup'			=> 'load_language_on_setup',

			// Auto Groups "Posts" listeners
			'core.submit_post_end'		=> 'submit_post_check',
			'core.delete_posts_after'	=> 'delete_post_check',
			'core.approve_posts_after'	=> 'approve_post_check',

			// Auto Groups "Warnings" listeners
			'core.mcp_warn_post_after'	=> 'add_warning_check',
			'core.mcp_warn_user_after'	=> 'add_warning_check',

			// Auto Groups "User Session" listeners
			'core.session_create_after'	=> [['last_visit_check'], ['membership_check']],

			// Auto Groups "Membership" listeners
			'core.user_add_after'			=> 'membership_check',
			'core.user_active_flip_after'	=> 'membership_check',
		);
	}

	/**
	 * Delete autogroups rules when their related group is deleted
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function delete_group_rules($event)
	{
		$this->manager->purge_autogroups_group($event['group_id']);
	}

	/**
	 * Load common language files during user setup
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function load_language_on_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = array(
			'ext_name' => 'phpbb/autogroups',
			'lang_set' => 'autogroups_common',
		);
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Check user's post count after submitting a post for auto groups
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function submit_post_check($event)
	{
		if (!isset($event['data']['poster_id']))
		{
			return;
		}

		$this->manager->check_condition('phpbb.autogroups.type.posts', array(
			'users'		=> $event['data']['poster_id'],
		));
	}

	/**
	 * Check user's post count after deleting a post for auto groups
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function delete_post_check($event)
	{
		$this->manager->check_condition('phpbb.autogroups.type.posts', array(
			'action'	=> 'delete',
			'users'		=> $event['poster_ids'],
		));
	}

	/**
	 * Check user's post count after approving a post for auto groups
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function approve_post_check($event)
	{
		$this->manager->check_condition('phpbb.autogroups.type.posts', array(
			'users'		=> array_column($event['post_info'], 'poster_id'),
		));
	}

	/**
	 * Check user's warnings count after receiving a warning for auto groups
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function add_warning_check($event)
	{
		$this->manager->check_condition('phpbb.autogroups.type.warnings', array(
			'users'		=> $event['user_row']['user_id'],
		));
	}

	/**
	 * Check user's last visit status when they log in
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function last_visit_check($event)
	{
		// only check session at login (this will prevent repeated excessive attempts at this check)
		if (strpos($event['session_data']['session_page'], 'login') === false)
		{
			return;
		}

		$this->manager->check_condition('phpbb.autogroups.type.lastvisit', array(
			'users'		=> $event['session_data']['session_user_id'],
		));
	}

	/**
	 * Check membership after user creation, activation, or login
	 *
	 * @param \phpbb\event\data $event The event object
	 * @return void
	 * @access public
	 */
	public function membership_check($event)
	{
		if ($event->offsetExists('session_data')) // core.session_create_after
		{
			// only check session at login (this will prevent repeated excessive attempts at this check)
			$users = strpos($event['session_data']['session_page'], 'login') !== false ? $event['session_data']['session_user_id'] : null;
		}
		else if ($event->offsetExists('user_id_ary')) // core.user_add_after
		{
			$users = $event['user_id_ary'];
		}
		else // core.user_active_flip_after
		{
			$users = $event['user_id'];
		}

		if (null === $users)
		{
			return;
		}

		$this->manager->check_condition('phpbb.autogroups.type.membership', array(
			'users'		=> $users,
		));
	}
}

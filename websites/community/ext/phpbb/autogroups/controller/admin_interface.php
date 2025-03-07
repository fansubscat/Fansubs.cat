<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\controller;

/**
 * Interface for our admin controller
 *
 * This describes all of the methods we'll use for the admin front-end of this extension
 */
interface admin_interface
{
	/**
	 * Display the auto group rules
	 *
	 * @return void
	 * @access public
	 */
	public function display_autogroups();

	/**
	 * Save an Auto Group rule
	 *
	 * @param int $autogroups_id The auto groups identifier to edit
	 * @return void
	 * @access public
	 */
	public function save_autogroup_rule($autogroups_id);

	/**
	 * Delete the auto group rule
	 *
	 * @param int $autogroups_id The auto groups identifier to delete
	 * @return void
	 * @access public
	 */
	public function delete_autogroup_rule($autogroups_id);

	/**
	 * Sync an auto group by running it's check against all users
	 *
	 * @param int $autogroups_id The auto groups identifier to delete
	 * @return void
	 * @access public
	 */
	public function resync_autogroup_rule($autogroups_id);

	/**
	 * Set form data from the ACP general options section
	 *
	 * @return void
	 * @access public
	 */
	public function submit_autogroups_options();

	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 * @return void
	 * @access public
	 */
	public function set_page_url($u_action);
}

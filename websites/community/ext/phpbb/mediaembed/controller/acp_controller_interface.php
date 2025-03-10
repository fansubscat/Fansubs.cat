<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\controller;

/**
 * phpBB Media Embed ACP module controller interface.
 */
interface acp_controller_interface
{
	/**
	 * Set page url
	 *
	 * @param string $u_action Custom form action
	 */
	public function set_page_url($u_action);

	/**
	 * Add settings template vars to the form
	 */
	public function display_settings();

	/**
	 * Add manage sites template vars to the form
	 */
	public function display_manage();

	/**
	 * Save settings data to the database
	 *
	 * @return array Message and code for trigger error
	 */
	public function save_settings();

	/**
	 * Save site managed data to the database
	 *
	 * @return array Message and code for trigger error
	 */
	public function save_manage();

	/**
	 * Purge all Media Embed cache files
	 */
	public function purge_mediaembed_cache();
}

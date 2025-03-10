<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

/**
 * Profile Flair user/group flair management MCP controller interface.
 */
interface mcp_user_interface extends acp_base_interface
{
	/**
	 * @param p_master $p_master
	 */
	public function set_p_master($p_master);

	/**
	 * Show the user search page.
	 */
	public function find_user();

	/**
	 * Handle the user flair edit mode.
	 */
	public function edit_user_flair();
}

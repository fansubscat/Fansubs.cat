<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

/**
 * Profile Flair UCP controller interface.
 */
interface ucp_flair_interface extends acp_base_interface
{
	/**
	 * Handle self flair editing.
	 */
	public function edit_flair();
}

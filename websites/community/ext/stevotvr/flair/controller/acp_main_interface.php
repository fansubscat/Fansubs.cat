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
 * Profile Flair main ACP controller interface.
 */
interface acp_main_interface extends acp_base_interface
{
	/**
	 * Display all flair.
	 */
	public function display_flair();
}

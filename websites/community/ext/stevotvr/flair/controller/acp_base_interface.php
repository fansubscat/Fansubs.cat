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
 * Profile Flair ACP controller interface.
 */
interface acp_base_interface
{
	/**
	 * @param string $page_url The URL for the current page
	 */
	public function set_page_url($page_url);
}

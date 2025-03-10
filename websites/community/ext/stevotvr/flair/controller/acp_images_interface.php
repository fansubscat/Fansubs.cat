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
 * Profile Flair images management ACP controller interface.
 */
interface acp_images_interface extends acp_base_interface
{
	/**
	 * List the existing icon images.
	 */
	public function list_images();

	/**
	 * Upload a new image and create the icon image set.
	 */
	public function add_image();

	/**
	 * Delete an icon image set.
	 *
	 * @param string $name The name of the icon.
	 */
	public function delete_image($name);
}

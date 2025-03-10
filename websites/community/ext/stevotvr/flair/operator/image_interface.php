<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\operator;

/**
 * Profile Flair image operators interface.
 */
interface image_interface
{
	/**
	 * Check if the image directory is writable, attempting to create it if it does not exist.
	 *
	 * @return boolean The image directory exists and is writable
	 */
	public function is_writable();

	/**
	 * Check if image processing is available.
	 *
	 * @return boolean Image processing is available
	 */
	public function can_process();

	/**
	 * Get a list of all available icon images.
	 *
	 * @return array The list of all available icon images
	 */
	public function get_images();

	/**
	 * Get a list of all the icon images currently in use.
	 *
	 * @return array The list of all the icon images currently in use
	 */
	public function get_used_images();

	/**
	 * Count the number of flair items using an image.
	 *
	 * @param string $image The name of the image
	 *
	 * @return int The number of items using the image
	 */
	public function count_image_items($image);

	/**
	 * Create a new icon image set from a file.
	 *
	 * @param string  $name      The name to assign to the set
	 * @param string  $file      The path to the source file
	 * @param boolean $overwrite Overwrite any existing images with the same name
	 *
	 * @throws \stevotvr\flair\exception\base
	 */
	public function add_image($name, $file, $overwrite);

	/**
	 * Delete an icon image.
	 *
	 * @param string $name The name of the icon image
	 */
	public function delete_image($name);
}

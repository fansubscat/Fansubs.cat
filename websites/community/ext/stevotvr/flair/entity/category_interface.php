<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\entity;

/**
 * Profile Flair category entity interface.
 */
interface category_interface extends entity_interface
{
	/**
	 * @return string The name of this category
	 */
	public function get_name();

	/**
	 * @param string $name The name of this category
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\missing_field
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_name($name);

	/**
	 * @return int The order of this category
	 */
	public function get_order();

	/**
	 * @param int $order The order of this category
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_order($order);

	/**
	 * @return boolean Show this item on user profile pages
	 */
	public function show_on_profile();

	/**
	 * @param boolean $show_on_profile Show this item on user profile pages
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_show_on_profile($show_on_profile);

	/**
	 * @return boolean Show this item in the user info on each post
	 */
	public function show_on_posts();

	/**
	 * @param boolean $show_on_posts Show this item in the user info on each post
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_show_on_posts($show_on_posts);

	/**
	 * @return int The limit of items to display on posts from this category
	 */
	public function get_display_limit();

	/**
	 * @param int The limit of items to display on posts from this category
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_display_limit($display_limit);
}

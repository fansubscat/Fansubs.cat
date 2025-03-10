<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\operator;

use stevotvr\flair\entity\category_interface as cat_entity;

/**
 * Profile Flair flair category operators interface.
 */
interface category_interface
{
	/**
	 * Get flair categories.
	 *
	 * @return array An array of category entities
	 */
	public function get_categories();

	/**
	 * Add a flair category.
	 *
	 * @param cat_entity $category
	 *
	 * @return cat_entity The added category entity
	 */
	public function add_category(cat_entity $category);

	/**
	 * Delete a flair category.
	 *
	 * @param int $cat_id The database ID of the category
	 *
	 * @return boolean The record was deleted
	 */
	public function delete_category($cat_id);

	/**
	 * Move a flair category in the sorting order.
	 *
	 * @param int $cat_id The database ID of the category
	 * @param int $offset The offset by which to move the category
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function move_category($cat_id, $offset);

	/**
	 * Delete all flair items from a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	public function delete_flair($cat_id);

	/**
	 * Reassign all flair items of a category to another category.
	 *
	 * @param int $cat_id     The database ID of the category
	 * @param int $new_cat_id The database ID of the new category
	 */
	public function reassign_flair($cat_id, $new_cat_id);
}

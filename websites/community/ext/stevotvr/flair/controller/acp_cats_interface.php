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
 * Profile Flair category management ACP controller interface.
 */
interface acp_cats_interface extends acp_base_interface
{
	/**
	 * Add a category.
	 */
	public function add_cat();

	/**
	 * Edit a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	public function edit_cat($cat_id);

	/**
	 * Delete a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	public function delete_cat($cat_id);

	/**
	 * Move a flair category in the sorting order.
	 *
	 * @param int $cat_id The database ID of the category
	 * @param int $offset The offset by which to move the category
	 */
	public function move_cat($cat_id, $offset);
}

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
 * Profile Flair flair management ACP controller interface.
 */
interface acp_flair_interface extends acp_base_interface
{
	/**
	 * Add a flair item.
	 */
	public function add_flair();

	/**
	 * Edit a flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 */
	public function edit_flair($flair_id);

	/**
	 * Delete a flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 */
	public function delete_flair($flair_id);

	/**
	 * Move a flair item in the sorting order.
	 *
	 * @param int $flair_id The database ID of the flair item
	 * @param int $offset   The offset by which to move the flair item
	 */
	public function move_flair($flair_id, $offset);
}

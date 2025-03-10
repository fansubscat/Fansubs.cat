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
 * Profile Flair entity interface.
 */
interface entity_interface
{
	/**
	 * Load an entity from the database.
	 *
	 * @param int $id The database ID of the entity
	 *
	 * @return entity_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function load($id);

	/**
	 * Import data from an external source.
	 *
	 * @param array $data The data to import
	 *
	 * @return entity_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\missing_field
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function import(array $data);

	/**
	 * Insert a new entity into the database.
	 *
	 * @return entity_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function insert();

	/**
	 * Save the current settings to the database.
	 *
	 * @return entity_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function save();

	/**
	 * @return int The database ID of the entity
	 */
	public function get_id();
}

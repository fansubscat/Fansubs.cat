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
 * Profile Flair flair category operator.
 */
class category extends operator implements category_interface
{
	/**
	 * @inheritDoc
	 */
	public function get_categories()
	{
		$entities = array();

		$sql = 'SELECT *
				FROM ' . $this->cat_table . '
				ORDER BY cat_order ASC, cat_id ASC';
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$entities[] = $this->container->get('stevotvr.flair.entity.category')->import($row);
		}
		$this->db->sql_freeresult();

		return $entities;
	}

	/**
	 * @inheritDoc
	 */
	public function add_category(cat_entity $category)
	{
		$category->insert();
		$cat_id = $category->get_id();
		return $category->load($cat_id);
	}

	/**
	 * @inheritDoc
	 */
	public function delete_category($cat_id)
	{
		$this->unlink_flair($cat_id);

		$sql = 'DELETE FROM ' . $this->cat_table . '
				WHERE cat_id = ' . (int) $cat_id;
		$this->db->sql_query($sql);

		return (bool) $this->db->sql_affectedrows();
	}

	/**
	 * @inheritDoc
	 */
	public function move_category($cat_id, $offset)
	{
		$ids = array();

		$sql = 'SELECT cat_id
				FROM ' . $this->cat_table . '
				ORDER BY cat_order ASC, cat_id ASC';
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$ids[] = (int) $row['cat_id'];
		}
		$this->db->sql_freeresult();

		$position = array_search($cat_id, $ids);
		array_splice($ids, $position, 1);
		$position += $offset;
		array_splice($ids, $position, 0, $cat_id);

		foreach ($ids as $pos => $id)
		{
			$sql = 'UPDATE ' . $this->cat_table . '
					SET cat_order = ' . $pos . '
					WHERE cat_id = ' . (int) $id;
			$this->db->sql_query($sql);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function delete_flair($cat_id)
	{
		$ids = array();

		$sql = 'SELECT flair_id
				FROM ' . $this->flair_table . '
				WHERE flair_category = ' . (int) $cat_id;
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$ids[] = (int) $row['flair_id'];
		}
		$this->db->sql_freeresult();

		if (!empty($ids))
		{
			$sql = 'DELETE FROM ' . $this->user_table . '
					WHERE ' . $this->db->sql_in_set('flair_id', $ids);
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . $this->fav_table . '
					WHERE ' . $this->db->sql_in_set('flair_id', $ids);
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . $this->group_table . '
					WHERE ' . $this->db->sql_in_set('flair_id', $ids);
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . $this->notif_table . '
					WHERE ' . $this->db->sql_in_set('flair_id', $ids);
			$this->db->sql_query($sql);

			$sql = 'DELETE FROM ' . $this->trigger_table . '
					WHERE ' . $this->db->sql_in_set('flair_id', $ids);
			$this->db->sql_query($sql);
		}

		$sql = 'DELETE FROM ' . $this->flair_table . '
				WHERE flair_category = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}

	/**
	 * @inheritDoc
	 */
	public function reassign_flair($cat_id, $new_cat_id)
	{
		$sql = 'UPDATE ' . $this->flair_table . '
				SET flair_category = ' . (int) $new_cat_id . '
				WHERE flair_category = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Unlink all flair items from a category.
	 *
	 * @param int $cat_id The database ID of the category
	 */
	protected function unlink_flair($cat_id)
	{
		$sql = 'UPDATE ' . $this->flair_table . '
				SET flair_category = 0
				WHERE flair_category = ' . (int) $cat_id;
		$this->db->sql_query($sql);
	}
}

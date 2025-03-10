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

use phpbb\db\driver\driver_interface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair operator base class.
 */
abstract class operator
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var driver_interface
	 */
	protected $db;

	/**
	 * The name of the flair table.
	 *
	 * @var string
	 */
	protected $flair_table;

	/**
	 * The name of the flair_categories table.
	 *
	 * @var string
	 */
	protected $cat_table;

	/**
	 * The name of the flair_favs table.
	 *
	 * @var string
	 */
	protected $fav_table;

	/**
	 * The name of the flair_groups table.
	 *
	 * @var string
	 */
	protected $group_table;

	/**
	 * The name of the flair_notif table.
	 *
	 * @var string
	 */
	protected $notif_table;

	/**
	 * The name of the flair_triggers table.
	 *
	 * @var string
	 */
	protected $trigger_table;

	/**
	 * The name of the flair_users table.
	 *
	 * @var string
	 */
	protected $user_table;

	/**
	 * @param ContainerInterface $container
	 * @param driver_interface   $db
	 * @param string             $flair_table   The name of the flair table
	 * @param string             $cat_table     The name of the flair_cats table
	 * @param string             $fav_table     The name of the flair_favs table
	 * @param string             $group_table   The name of the flair_groups table
	 * @param string             $notif_table   The name of the flair_notif table
	 * @param string             $trigger_table The name of the flair_triggers table
	 * @param string             $user_table    The name of the flair_users table
	 */
	public function __construct(ContainerInterface $container, driver_interface $db, $flair_table, $cat_table, $fav_table, $group_table, $notif_table, $trigger_table, $user_table)
	{
		$this->container = $container;
		$this->db = $db;
		$this->flair_table = $flair_table;
		$this->cat_table = $cat_table;
		$this->fav_table = $fav_table;
		$this->group_table = $group_table;
		$this->notif_table = $notif_table;
		$this->trigger_table = $trigger_table;
		$this->user_table = $user_table;
	}

	/**
	 * Import a flair item from a database query result row.
	 *
	 * @param array &$flair The array to which to add the item
	 * @param array $row    The database result row data
	 */
	protected function import_flair_item(array &$flair, array $row, array $favorites = array())
	{
		$entity = $this->container->get('stevotvr.flair.entity.category');
		if ($row['cat_id'])
		{
			$entity->import($row);
		}
		$flair[(int) $row['flair_category']]['category'] = $entity;

		$entity = $this->container->get('stevotvr.flair.entity.flair')->import($row);
		$item = array(
			'count'			=> isset($row['flair_count']) ? (int) $row['flair_count'] : 1,
			'priority'		=> in_array($entity->get_id(), $favorites) ? 1 : 0,
			'from_group'	=> isset($row['from_group']) ? (bool) $row['from_group'] : false,
			'flair'			=> $entity,
		);
		$flair[(int) $row['flair_category']]['items'][(int) $row['flair_id']] = $item;
	}

	/**
	 * Sort a flair array.
	 *
	 * @param array &$flair The flair array to sort
	 */
	static protected function sort_flair(array &$flair)
	{
		uasort($flair, array('self', 'cmp_cats'));
		foreach ($flair as &$category)
		{
			uasort($category['items'], array('self', 'cmp_items'));
			uasort($category['items'], array('self', 'cmp_items_priority'));
		}
	}

	/**
	 * Comparison function for sorting flair category arrays.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	static protected function cmp_cats($a, $b)
	{
		return $a['category']->get_order() - $b['category']->get_order();
	}

	/**
	 * Comparison function for sorting flair item arrays.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	static protected function cmp_items($a, $b)
	{
		return $a['flair']->get_order() - $b['flair']->get_order();
	}

	/**
	 * Comparison function for sorting flair item arrays based on priority.
	 *
	 * @param array $a
	 * @param array $b
	 *
	 * @return int
	 */
	static protected function cmp_items_priority($a, $b)
	{
		return $b['priority'] - $a['priority'];
	}
}

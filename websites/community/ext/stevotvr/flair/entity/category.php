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

use stevotvr\flair\exception\missing_field;
use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair flair category entity.
 */
class category extends entity implements category_interface
{
	/**
	 * @inheritDoc
	 */
	protected $columns = array(
		'cat_id'				=> 'integer',
		'cat_name'				=> 'set_name',
		'cat_order'				=> 'set_order',
		'cat_display_profile'	=> 'set_show_on_profile',
		'cat_display_posts'		=> 'set_show_on_posts',
		'cat_display_limit'		=> 'integer',
	);

	/**
	 * @inheritDoc
	 */
	protected $id_column = 'cat_id';

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return isset($this->data['cat_name']) ? (string) $this->data['cat_name'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_name($name)
	{
		$name = (string) $name;

		if ($name === '')
		{
			throw new missing_field('cat_name', 'EXCEPTION_CAT_NAME_REQUIRED');
		}

		if (truncate_string($name, 255) !== $name)
		{
			throw new unexpected_value('cat_name', 'TOO_LONG');
		}

		$this->data['cat_name'] = $name;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_order()
	{
		return isset($this->data['cat_order']) ? (int) $this->data['cat_order'] : -1;
	}

	/**
	 * @inheritDoc
	 */
	public function set_order($order)
	{
		$order = (int) $order;

		if ($order < 0 || $order > 16777215)
		{
			throw new out_of_bounds('cat_order');
		}

		$this->data['cat_order'] = $order;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function show_on_profile()
	{
		return isset($this->data['cat_display_profile']) ? (bool) $this->data['cat_display_profile'] : false;
	}

	/**
	 * @inheritDoc
	 */
	public function set_show_on_profile($show_on_profile)
	{
		$show_on_profile = (bool) $show_on_profile;

		$this->data['cat_display_profile'] = $show_on_profile;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function show_on_posts()
	{
		return isset($this->data['cat_display_posts']) ? (bool) $this->data['cat_display_posts'] : false;
	}

	/**
	 * @inheritDoc
	 */
	public function set_show_on_posts($show_on_posts)
	{
		$show_on_posts = (bool) $show_on_posts;

		$this->data['cat_display_posts'] = $show_on_posts;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_display_limit()
	{
		return isset($this->data['cat_display_limit']) ? (int) $this->data['cat_display_limit'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function set_display_limit($display_limit)
	{
		$display_limit = (int) $display_limit;

		$this->data['cat_display_limit'] = max(0, min(16777215, $display_limit));

		return $this;
	}
}

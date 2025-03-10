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
 * Profile Flair flair entity interface.
 */
interface flair_interface extends entity_interface
{
	/* Available flair types */
	const TYPE_FA = 0;
	const TYPE_IMG = 1;

	/**
	 * @return int The type of flair item
	 */
	public function get_type();

	/**
	 * @param int $type The type of flair item
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_type($type);

	/**
	 * @return int The database ID of the category
	 */
	public function get_category();

	/**
	 * @param int $cat_id The database ID of the category
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_category($cat_id);

	/**
	 * @return string The name of this flair item
	 */
	public function get_name();

	/**
	 * @param string $name The name of this flair item
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_name($name);

	/**
	 * @return string The description of this flair item for editing
	 */
	public function get_desc_for_edit();

	/**
	 * @return string The description of this flair item for display
	 */
	public function get_desc_for_display();

	/**
	 * @param string $desc The description of this flair item
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_desc($desc);

	/**
	 * @return boolean BBCode is enabled on the description
	 */
	public function is_bbcode_enabled();

	/**
	 * @param boolean $enable Enable BBCode on the description.
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_bbcode_enabled($enable);

	/**
	 * @return boolean URL parsing is enabled on the description
	 */
	public function is_magic_url_enabled();

	/**
	 * @param boolean $enable Enable URL parsing on the description.
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_magic_url_enabled($enable);

	/**
	 * @return boolean Smilies are enabled on the description
	 */
	public function is_smilies_enabled();

	/**
	 * @param boolean $enable Enable smilies on the description.
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_smilies_enabled($enable);

	/**
	 * @return int The order of this flair item
	 */
	public function get_order();

	/**
	 * @param int $order The order of this flair item
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_order($order);

	/**
	 * @return string The hex color string for this flair item
	 */
	public function get_color();

	/**
	 * @param string $color The hex color string for this flair item
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 */
	public function set_color($color);

	/**
	 * @return string The identifier for the font icon
	 */
	public function get_icon();

	/**
	 * @param string $icon The identifier for the font icon
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_icon($icon);

	/**
	 * @return string The hex color string for the icon
	 */
	public function get_icon_color();

	/**
	 * @param string $color The hex color string for the icon
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_icon_color($color);

	/**
	 * @return float The width of the icon in ems
	 */
	public function get_icon_width();

	/**
	 * @return string The hex color string for the count font
	 */
	public function get_font_color();

	/**
	 * @param string $color The hex color string for the count font
	 *
	 * @return flair_interface This object for chaining
	 *
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_font_color($color);

	/**
	 * @param int $size The size requested
	 *
	 * @return string The image path
	 */
	public function get_img($size = 1);

	/**
	 * @param string $img_path The image path
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_img($img_path);

	/**
	 * @return boolean Groups auto-assignment is enabled
	 */
	public function is_groups_auto();

	/**
	 * @param boolean $enable Enable group auto-assignment
	 *
	 * @return flair_interface This object for chaining
	 */
	public function set_groups_auto($enable);
}

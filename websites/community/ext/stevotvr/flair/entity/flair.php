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

use phpbb\config\config;
use stevotvr\flair\exception\missing_field;
use stevotvr\flair\exception\out_of_bounds;
use stevotvr\flair\exception\unexpected_value;

/**
 * Profile Flair flair entity.
 */
class flair extends entity implements flair_interface
{
	/**
	 * @var config
	 */
	protected $config;

	/**
	 * The metadata for the FA icons.
	 *
	 * @var array
	 */
	protected $icons;

	/**
	 * @inheritDoc
	 */
	protected $columns = array(
		'flair_id'						=> 'integer',
		'flair_type'					=> 'integer',
		'flair_category'				=> 'integer',
		'flair_name'					=> 'set_name',
		'flair_desc'					=> 'string',
		'flair_desc_bbcode_uid'			=> 'string',
		'flair_desc_bbcode_bitfield'	=> 'string',
		'flair_desc_bbcode_options'		=> 'integer',
		'flair_order'					=> 'set_order',
		'flair_color'					=> 'set_color',
		'flair_icon'					=> 'set_icon',
		'flair_icon_color'				=> 'set_icon_color',
		'flair_font_color'				=> 'set_font_color',
		'flair_img'						=> 'set_img',
		'flair_groups_auto'				=> 'boolean',
	);

	/**
	 * @inheritDoc
	 */
	protected $id_column = 'flair_id';

	/**
	 * Set up the entity.
	 *
	 * @param config $config
	 * @param array  $icons
	 */
	public function setup(config $config, array $icons)
	{
		$this->config = $config;
		$this->icons = $icons;
	}

	/**
	 * @inheritDoc
	 */
	public function get_type()
	{
		return isset($this->data['flair_type']) ? (int) $this->data['flair_type'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function set_type($type)
	{
		$type = (int) $type;

		$this->data['flair_type'] = max(0, min(1, $type));

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_category()
	{
		return isset($this->data['flair_category']) ? (int) $this->data['flair_category'] : 0;
	}

	/**
	 * @inheritDoc
	 */
	public function set_category($cat_id)
	{
		$cat_id = (int) $cat_id;

		if ($cat_id < 0)
		{
			throw new out_of_bounds('flair_category');
		}

		$this->data['flair_category'] = $cat_id;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_name()
	{
		return isset($this->data['flair_name']) ? (string) $this->data['flair_name'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_name($name)
	{
		$name = (string) $name;

		if ($name === '')
		{
			throw new missing_field('flair_name', 'EXCEPTION_NAME_REQUIRED');
		}

		if (truncate_string($name, 255) !== $name)
		{
			throw new unexpected_value('flair_name', 'TOO_LONG');
		}

		$this->data['flair_name'] = $name;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_desc_for_edit()
	{
		$content = isset($this->data['flair_desc']) ? $this->data['flair_desc'] : '';
		$uid = isset($this->data['flair_desc_bbcode_uid']) ? $this->data['flair_desc_bbcode_uid'] : '';
		$options = isset($this->data['flair_desc_bbcode_options']) ? (int) $this->data['flair_desc_bbcode_options'] : 0;

		$content_data = generate_text_for_edit($content, $uid, $options);

		return $content_data['text'];
	}

	/**
	 * @inheritDoc
	 */
	public function get_desc_for_display()
	{
		$content = isset($this->data['flair_desc']) ? $this->data['flair_desc'] : '';
		$uid = isset($this->data['flair_desc_bbcode_uid']) ? $this->data['flair_desc_bbcode_uid'] : '';
		$bitfield = isset($this->data['flair_desc_bbcode_bitfield']) ? $this->data['flair_desc_bbcode_bitfield'] : '';
		$options = isset($this->data['flair_desc_bbcode_options']) ? (int) $this->data['flair_desc_bbcode_options'] : 0;

		return generate_text_for_display($content, $uid, $bitfield, $options);
	}

	/**
	 * @inheritDoc
	 */
	public function set_desc($desc)
	{
		$this->config['max_post_chars'] = 0;

		$uid = $bitfield = $flags = '';
		generate_text_for_storage($desc, $uid, $bitfield, $flags, $this->is_bbcode_enabled(), $this->is_magic_url_enabled(), $this->is_smilies_enabled());

		$this->data['flair_desc'] = $desc;
		$this->data['flair_desc_bbcode_uid'] = $uid;
		$this->data['flair_desc_bbcode_bitfield'] = $bitfield;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function is_bbcode_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_BBCODE);
	}

	/**
	 * @inheritDoc
	 */
	public function set_bbcode_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_BBCODE, $enable);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function is_magic_url_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_LINKS);
	}

	/**
	 * @inheritDoc
	 */
	public function set_magic_url_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_LINKS, $enable);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function is_smilies_enabled()
	{
		return ($this->data['flair_desc_bbcode_options'] & OPTION_FLAG_SMILIES);
	}

	/**
	 * @inheritDoc
	 */
	public function set_smilies_enabled($enable)
	{
		$this->set_desc_option(OPTION_FLAG_SMILIES, $enable);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_order()
	{
		return isset($this->data['flair_order']) ? (int) $this->data['flair_order'] : -1;
	}

	/**
	 * @inheritDoc
	 */
	public function set_order($order)
	{
		$order = (int) $order;

		if ($order < 0 || $order > 16777215)
		{
			throw new out_of_bounds('flair_order');
		}

		$this->data['flair_order'] = $order;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_color()
	{
		return isset($this->data['flair_color']) ? (string) $this->data['flair_color'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_color($color)
	{
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
		{
			throw new unexpected_value('flair_color', 'INVALID_COLOR');
		}

		$this->data['flair_color'] = $color;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon()
	{
		return isset($this->data['flair_icon']) ? (string) $this->data['flair_icon'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_icon($icon)
	{
		$icon = (string) $icon;

		if (truncate_string($icon, 50) !== $icon)
		{
			throw new unexpected_value('flair_icon', 'TOO_LONG');
		}

		$this->data['flair_icon'] = $icon;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon_color()
	{
		return isset($this->data['flair_icon_color']) ? (string) $this->data['flair_icon_color'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_icon_color($color)
	{
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
		{
			throw new unexpected_value('flair_icon_color', 'INVALID_COLOR');
		}

		$this->data['flair_icon_color'] = $color;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_icon_width()
	{
		if (isset($this->data['flair_icon']))
		{
			foreach (explode(' ', $this->data['flair_icon']) as $icon)
			{
				if (isset($this->icons[$icon]))
				{
					return (float) $this->icons[$icon]['w'];
				}
			}
		}

		return 1.0;
	}

	/**
	 * @inheritDoc
	 */
	public function get_font_color()
	{
		return isset($this->data['flair_font_color']) ? (string) $this->data['flair_font_color'] : '';
	}

	/**
	 * @inheritDoc
	 */
	public function set_font_color($color)
	{
		$color = strtoupper($color);

		if ($color !== '' && !self::is_valid_color($color))
		{
			throw new unexpected_value('flair_font_color', 'INVALID_COLOR');
		}

		$this->data['flair_font_color'] = $color;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function get_img($size = 0)
	{
		if (empty($this->data['flair_img']))
		{
			return '';
		}

		$image = $this->data['flair_img'];
		if (!$size || strtolower(substr($image, strrpos($image, '.'))) === '.svg')
		{
			return $image;
		}

		$size = min(3, max(1, $size));

		$image_ext = substr($image, strrpos($image, '.'));
		return substr($image, 0, strrpos($image, '.')) . '-x' . $size . $image_ext;
	}

	/**
	 * @inheritDoc
	 */
	public function set_img($img_name)
	{
		$img_name = (string) $img_name;

		if (truncate_string($img_name, 255) !== $img_name)
		{
			throw new unexpected_value('img_name', 'TOO_LONG');
		}

		$this->data['flair_img'] = $img_name;

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function is_groups_auto()
	{
		return isset($this->data['flair_groups_auto']) ? (bool) $this->data['flair_groups_auto'] : true;
	}

	/**
	 * @inheritDoc
	 */
	public function set_groups_auto($enable)
	{
		$this->data['flair_groups_auto'] = (bool) $enable;

		return $this;
	}

	/**
	 * Set a parsing option on the description text.
	 *
	 * @param int     $option The option to set
	 * @param boolean $value  The value of the option
	 */
	protected function set_desc_option($option, $value)
	{
		$this->data['flair_desc_bbcode_options'] = isset($this->data['flair_desc_bbcode_options']) ? $this->data['flair_desc_bbcode_options'] : 0;

		if ($value && !($this->data['flair_desc_bbcode_options'] & $option))
		{
			$this->data['flair_desc_bbcode_options'] += $option;
		}

		if (!$value && $this->data['flair_desc_bbcode_options'] & $option)
		{
			$this->data['flair_desc_bbcode_options'] -= $option;
		}

		if (!empty($this->data['flair_desc']))
		{
			$content = $this->data['flair_desc'];

			decode_message($content, $this->data['flair_desc_bbcode_uid']);

			$this->set_desc($content);
		}
	}

	/**
	 * Check if a given string is a valid color hexadecimal value.
	 *
	 * @param string $color The string to check
	 *
	 * @return boolean The string is a valid color hexadecimal value
	 */
	static protected function is_valid_color($color)
	{
		return preg_match('/^[0-9A-F]{6}$/i', $color) === 1;
	}
}

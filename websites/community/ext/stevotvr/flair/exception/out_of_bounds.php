<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\exception;

/**
 * Profile Flair exception for a field that received a value out of its range.
 */
class out_of_bounds extends base
{
	/**
	 * @param string $field The name of the field
	 */
	public function __construct($field)
	{
		$message = sprintf('The field "%s" received a value out of its range.', $field);
		$lang_array = array('EXCEPTION_OUT_OF_BOUNDS', 'EXCEPTION_FIELD_' . strtoupper($field));
		parent::__construct($message, $lang_array);
	}
}

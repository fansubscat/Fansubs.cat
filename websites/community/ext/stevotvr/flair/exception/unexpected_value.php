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
 * Profile Flair exception for a field that received an unexpected value.
 */
class unexpected_value extends base
{
	/**
	 * @param string $field       The name of the field
	 * @param string $lang_string An optional language string for the message
	 */
	public function __construct($field, $lang_string = null)
	{
		$message = sprintf('The field "%s" received an unexpected value. Reason: %s', $field, $lang_string);
		$lang_array = ($lang_string) ? array('EXCEPTION_' . $lang_string, 'EXCEPTION_FIELD_' . strtoupper($field)) : null;
		parent::__construct($message, $lang_array);
	}
}

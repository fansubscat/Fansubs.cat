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
 * Profile Flair exception for a missing required field.
 */
class missing_field extends base
{
	/**
	 * @param string $field       The name of the required field
	 * @param string $lang_string An optional language string for the message
	 */
	public function __construct($field, $lang_string = null)
	{
		$message = sprintf('The required field "%s" is missing.', $field);
		parent::__construct($message, $lang_string);
	}
}

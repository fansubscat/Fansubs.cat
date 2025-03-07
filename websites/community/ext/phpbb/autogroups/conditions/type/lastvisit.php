<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2018 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\conditions\type;

/**
 * Auto Groups Last Visited class
 */
class lastvisit extends \phpbb\autogroups\conditions\type\membership
{
	/**
	 * Get condition type
	 *
	 * @return string Condition type
	 * @access public
	 */
	public function get_condition_type()
	{
		return 'phpbb.autogroups.type.lastvisit';
	}

	/**
	 * Get condition field (this is the field to check)
	 *
	 * @return string Condition field name
	 * @access public
	 */
	public function get_condition_field()
	{
		return 'user_lastvisit';
	}

	/**
	 * Get condition type name
	 *
	 * @return string Condition type name
	 * @access public
	 */
	public function get_condition_type_name()
	{
		return $this->language->lang('AUTOGROUPS_TYPE_LASTVISIT');
	}
}

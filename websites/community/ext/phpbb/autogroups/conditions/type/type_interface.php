<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\conditions\type;

/**
 * Auto Groups interface
 */
interface type_interface
{
	/**
	 * Get condition type
	 *
	 * @return string Condition type
	 * @access public
	 */
	public function get_condition_type();

	/**
	 * Get condition field (this is the field to check)
	 *
	 * @return string Condition field name
	 * @access public
	 */
	public function get_condition_field();

	/**
	 * Get condition type name
	 *
	 * @return string Condition type name
	 * @access public
	 */
	public function get_condition_type_name();

	/**
	 * Get users to apply to this condition
	 *
	 * @param array $options Array of optional data
	 * @return array Array of users ids and their post counts
	 * @access public
	 */
	public function get_users_for_condition($options = array());

	/**
	 * Get auto group rules for condition type
	 *
	 * @param string $type Auto group condition type name
	 * @return array Auto group rows
	 * @access public
	 */
	public function get_group_rules($type);

	/**
	 * Add user(s) to group
	 *
	 * @param array $user_id_ary     User(s) to add to group
	 * @param array $group_rule_data Auto group rule data
	 * @return void
	 * @access public
	 */
	public function add_users_to_group($user_id_ary, $group_rule_data);

	/**
	 * Remove user(s) from group
	 *
	 * @param array $user_id_ary     User(s) to remove from group
	 * @param array $group_rule_data Auto group rule data
	 * @return void
	 * @access public
	 */
	public function remove_users_from_group($user_id_ary, $group_rule_data);

	/**
	 * Check condition
	 *
	 * @param array $user_row Array of user data to perform checks on
	 * @param array $options  Array of optional data
	 * @return void
	 * @access public
	 */
	public function check($user_row, $options = array());
}

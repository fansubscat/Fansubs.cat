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

/**
 * Profile Flair flair trigger operators interface.
 */
interface trigger_interface
{
	/**
	 * Get all the triggers for a specified flair item.
	 *
	 * @param int $flair_id The database ID of the flair item
	 *
	 * @return array An associative array of trigger names to values
	 */
	public function get_flair_triggers($flair_id);

	/**
	 * Set a trigger for a flair item.
	 *
	 * @param int    $flair_id      The database ID of the flair item
	 * @param string $trigger_name  The trigger name
	 * @param int    $trigger_value The trigger value
	 *
	 * @throws \stevotvr\flair\exception\out_of_bounds
	 * @throws \stevotvr\flair\exception\unexpected_value
	 */
	public function set_trigger($flair_id, $trigger_name, $trigger_value);

	/**
	 * Remove a trigger for a flair item.
	 *
	 * @param int    $flair_id     The database ID of the flair item
	 * @param string $trigger_name The trigger name
	 */
	public function unset_trigger($flair_id, $trigger_name);

	/**
	 * Dispatch a trigger for a user.
	 *
	 * @param int    $user_id       The user ID
	 * @param string $trigger_name  The trigger name
	 * @param int    $trigger_value The trigger value
	 */
	public function dispatch($user_id, $trigger_name, $trigger_value);
}

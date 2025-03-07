<?php
/**
*
* Auto Groups extension for the phpBB Forum Software package.
*
* @copyright (c) 2014 phpBB Limited <https://www.phpbb.com>
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace phpbb\autogroups\acp;

class autogroups_module
{
	public $page_title;
	public $tpl_name;
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @param int $id
	 * @param string $mode
	 * @throws \Exception
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		/** @var \phpbb\language\language $language */
		$language = $phpbb_container->get('language');

		/** @var \phpbb\request\request $request */
		$request = $phpbb_container->get('request');

		// Add the auto groups ACP lang file
		$language->add_lang('autogroups_acp', 'phpbb/autogroups');

		// Get an instance of the admin controller
		$admin_controller = $phpbb_container->get('phpbb.autogroups.admin_controller');

		// Requests
		$action = $request->variable('action', '');
		$autogroups_id = $request->variable('autogroups_id', 0);

		// Make the $u_action url available in the admin controller
		$admin_controller->set_page_url($this->u_action);

		// Load a template from adm/style for our ACP auto groups
		$this->tpl_name = 'manage_autogroups';

		// Set the page title for our ACP auto groups
		$this->page_title = 'ACP_AUTOGROUPS_MANAGE';

		// Quick-submit settings from the general options form
		if ($request->is_set_post('generalsubmit'))
		{
			$admin_controller->submit_autogroups_options();
		}

		// Perform any actions submitted by the user
		switch ($action)
		{
			case 'add':
			case 'edit':
				// Set the page title for our ACP auto groups
				$this->page_title = strtoupper("ACP_AUTOGROUPS_$action");

				// Load the save auto group handle in the admin controller
				$admin_controller->save_autogroup_rule($autogroups_id);

				// Return to stop execution of this script
				return;
			break;

			case 'sync':
				// Resync applies an auto group check against all users
				$admin_controller->resync_autogroup_rule($autogroups_id);
			break;

			case 'delete':
				// Use a confirm box routine when deleting an auto group rule
				if (confirm_box(true))
				{
					// Delete auto group rule on confirmation from the user
					$admin_controller->delete_autogroup_rule($autogroups_id);
				}
				else
				{
					// Request confirmation from the user to delete the auto group rule
					confirm_box(false, $language->lang('ACP_AUTOGROUPS_DELETE_CONFIRM'), build_hidden_fields(array(
						'autogroups_id'	=> $autogroups_id,
						'mode'			=> $mode,
						'action'		=> $action,
					)));
				}
			break;
		}

		// Display auto group rules
		$admin_controller->display_autogroups();
	}
}

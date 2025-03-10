<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\acp;

/**
 * phpBB Media Embed Plugin ACP module.
 */
class main_module
{
	/** @var string $page_title */
	public $page_title;

	/** @var string $tpl_name */
	public $tpl_name;

	/** @var string $u_action */
	public $u_action;

	/**
	 * Main ACP module
	 *
	 * @param int    $id   The module ID (not used)
	 * @param string $mode The module mode (manage|settings)
	 * @throws \Exception
	 */
	public function main($id, $mode)
	{
		global $phpbb_container;

		$mode = strtolower($mode);

		/** @var \phpbb\mediaembed\controller\acp_controller $acp_controller */
		$acp_controller = $phpbb_container->get('phpbb.mediaembed.acp_controller');

		// Make the $u_action url available in the admin controller
		$acp_controller->set_page_url($this->u_action);

		// Load a template from adm/style for our ACP page
		$this->tpl_name = 'acp_phpbb_mediaembed_' . $mode;

		// Set the page title for our ACP page
		$this->page_title = 'ACP_MEDIA_' . strtoupper($mode);

		$form_key = 'phpbb/mediaembed';
		add_form_key($form_key);

		/** @var \phpbb\request\request $request */
		$request = $phpbb_container->get('request');
		if ($request->is_set_post('action_purge_cache'))
		{
			$result = $acp_controller->purge_mediaembed_cache();
			trigger_error($result['message'] . adm_back_link($this->u_action), $result['code']);
		}

		if ($request->is_set_post('submit'))
		{
			if (!check_form_key($form_key))
			{
				$language = $phpbb_container->get('language');
				trigger_error($language->lang('FORM_INVALID'), E_USER_WARNING);
			}

			$result = $acp_controller->{'save_' . $mode}();
			trigger_error($result['message'] . adm_back_link($this->u_action), $result['code']);
		}

		$acp_controller->{'display_' . $mode}();
	}
}

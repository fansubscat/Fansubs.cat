<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\ucp;

/**
 * Profile Flair UCP module.
 */
class flair_module
{
	public $u_action;
	public $tpl_name;
	public $page_title;

	public function main($id, $mode)
	{
		global $phpbb_container;
		$controller = $phpbb_container->get('stevotvr.flair.controller.ucp.flair');

		$this->tpl_name = 'ucp_flair';
		$this->page_title = 'UCP_FLAIR';

		$controller->set_page_url($this->u_action);
		$controller->edit_flair();
	}
}

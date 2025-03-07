<?php

/**
 *
 * @package phpBB Extension - mChat
 * @copyright (c) 2016 dmzx - http://www.dmzx-web.net
 * @copyright (c) 2016 kasimi - https://kasimi.net
 * @license http://opensource.org/licenses/gpl-2.0.php GNU General Public License v2
 *
 */

namespace dmzx\mchat;

use phpbb\extension\base;

class ext extends base
{
	/**
	 * Requires phpBB 3.1.7-PL1 due to usage of \phpbb\session:update_session_infos()
	 * Requires phpBB 3.1.8-RC1 due to HTTPS in version check
	 * Requires phpBB 3.2.0 due to EoL of phpBB 3.1
	 *
	 * @return bool
	 * @access public
	 */
	public function is_enableable()
	{
		$config = $this->container->get('config');

		// Here we check if any modules from the mChat MOD for phpBB 3.0.x are still in the database.
		// This is_enableable() method is called multiple times during the installation but we only
		// need to do the following check once. Checking for the absence of the mchat_version value
		// in the config guarantees that we're in the very first step of the installation process.
		// Any later call of this method doesn't need to check this again and in fact will wrongly
		// detect the extension's modules as being remnants.
		if (empty($config['mchat_version']))
		{
			$table_prefix = $this->container->getParameter('core.table_prefix');
			$module_ids = $this->get_old_module_ids($table_prefix);

			if ($module_ids)
			{
				$lang = $this->container->get('language');
				$lang->add_lang('mchat_acp', 'dmzx/mchat');
				$php_ext = $this->container->getParameter('core.php_ext');
				$error_msg = $lang->lang('MCHAT_30X_REMNANTS', $table_prefix, implode($lang->lang('COMMA_SEPARATOR'), $module_ids)) . adm_back_link(append_sid('index.' . $php_ext, ['i' => 'acp_extensions', 'mode' => 'main']));

				trigger_error($error_msg, E_USER_WARNING);
			}
		}

		return phpbb_version_compare(PHPBB_VERSION, '3.2.0', '>=') && phpbb_version_compare(PHP_VERSION, '5.4.7', '>=');
	}

	/**
	 * This method checks whether the phpbb_modules table contains remnants of the 3.0 MOD.
	 * It returns an array of the modules' IDs, or an empty array if no old modules are found.
	 *
	 * @param string $table_prefix
	 * @return array
	 */
	protected function get_old_module_ids($table_prefix)
	{
		$db = $this->container->get('dbal.conn');

		$mchat_30x_module_langnames = [
			'ACP_CAT_MCHAT',
			'ACP_MCHAT_CONFIG',
			'ACP_USER_MCHAT',
			'UCP_CAT_MCHAT',
			'UCP_MCHAT_CONFIG',
		];

		$sql = 'SELECT module_id
			FROM ' . $table_prefix . 'modules
			WHERE ' . $db->sql_in_set('module_langname', $mchat_30x_module_langnames);
		$result = $db->sql_query($sql);
		$rows = $db->sql_fetchrowset($result);
		$db->sql_freeresult($result);

		$module_ids = [];

		foreach ($rows as $row)
		{
			$module_ids[] = $row['module_id'];
		}

		return $module_ids;
	}
}

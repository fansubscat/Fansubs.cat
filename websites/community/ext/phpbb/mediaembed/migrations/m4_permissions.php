<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration 4: Add permissions
 */
class m4_permissions extends \phpbb\db\migration\migration
{
	/**
	 * {@inheritdoc
	 */
	public function effectively_installed()
	{
		$sql = 'SELECT * FROM ' . $this->table_prefix . "acl_options
			WHERE auth_option = 'f_mediaembed'";
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row !== false;
	}

	/**
	 * {@inheritdoc
	 */
	public static function depends_on()
	{
		return [
			'\phpbb\mediaembed\migrations\m1_install_data',
			'\phpbb\mediaembed\migrations\m3_plain_urls_config',
		];
	}

	/**
	 * {@inheritdoc
	 */
	public function update_data()
	{
		return [
			// Add forum permission
			['permission.add', ['f_mediaembed', false]],
			['if', [
				['permission.role_exists', ['ROLE_FORUM_FULL']],
				['permission.permission_set', ['ROLE_FORUM_FULL', 'f_mediaembed']],
			]],
			['if', [
				['permission.role_exists', ['ROLE_FORUM_POLLS']],
				['permission.permission_set', ['ROLE_FORUM_POLLS', 'f_mediaembed']],
			]],
			['if', [
				['permission.role_exists', ['ROLE_FORUM_ONQUEUE']],
				['permission.permission_set', ['ROLE_FORUM_ONQUEUE', 'f_mediaembed']],
			]],
			['if', [
				['permission.role_exists', ['ROLE_FORUM_STANDARD']],
				['permission.permission_set', ['ROLE_FORUM_STANDARD', 'f_mediaembed']],
			]],

			// Add PM permission
			['permission.add', ['u_pm_mediaembed']],
			['if', [
				['permission.role_exists', ['ROLE_USER_FULL']],
				['permission.permission_set', ['ROLE_USER_FULL', 'u_pm_mediaembed']],
			]],
			['if', [
				['permission.role_exists', ['ROLE_USER_STANDARD']],
				['permission.permission_set', ['ROLE_USER_STANDARD', 'u_pm_mediaembed']],
			]],
			['permission.permission_set', ['REGISTERED', 'u_pm_mediaembed', 'group']],
			['permission.permission_set', ['REGISTERED_COPPA', 'u_pm_mediaembed', 'group']],
		];
	}
}

<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2022 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\migrations;

/**
 * Migration 7: Add missing permissions.
 *
 * If the media embed forum permission has already been set, either by a previous migration
 * or the user, then this migration will not run. Otherwise, it probably means the core forum
 * roles don't exist anymore. So this migration will look for any custom forum roles that
 * exist with permission to use BBCodes and assume those are safe to assign Media Embed
 * permissions to, since it is essentially a BBCode itself.
 */
class m7_add_missing_permissions extends \phpbb\db\migration\migration
{
	/**
	 * If f_mediaembed has already been assigned to any permission role
	 * then this migration should not run.
	 *
	 * {@inheritdoc}
	 */
	public function effectively_installed()
	{
		$sql_array = [
			'SELECT'	=> '*',
			'FROM'		=> [
				$this->table_prefix . 'acl_roles_data'	=> 'd',
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [$this->table_prefix . 'acl_options' => 'o'],
					'ON'	=> 'o.auth_option_id = d.auth_option_id',
				],
			],
			'WHERE'		=> "o.auth_option = 'f_mediaembed'",
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query_limit($sql, 1);
		$row = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return $row !== false;
	}

	/**
	 * {@inheritdoc}
	 */
	public static function depends_on()
	{
		return [
			'\phpbb\mediaembed\migrations\m1_install_data',
			'\phpbb\mediaembed\migrations\m4_permissions',
			'\phpbb\mediaembed\migrations\m6_full_width',
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function update_data()
	{
		$install = [];

		foreach ($this->custom_forum_roles() as $role)
		{
			$install[] = ['permission.permission_set', [$role, 'f_mediaembed']];
		}

		return $install;
	}

	/**
	 * Find custom forum roles with bbcodes allowed.
	 *
	 * @return array An array of forum role names
	 */
	public function custom_forum_roles()
	{
		$sql_array = [
			'SELECT'	=> 'roles.role_id, roles.role_name',
			'FROM'		=> [
				$this->table_prefix . 'acl_roles'	=> 'roles',
			],
			'LEFT_JOIN'	=> [
				[
					'FROM'	=> [$this->table_prefix . 'acl_roles_data' => 'data'],
					'ON'	=> 'roles.role_id = data.role_id',
				],
				[
					'FROM'	=> [$this->table_prefix . 'acl_options' => 'opts'],
					'ON'	=> 'data.auth_option_id = opts.auth_option_id',
				],
			],
			'WHERE'	=> 'opts.auth_option = "f_bbcode"
				AND data.auth_setting = "1"
				AND roles.role_type = "f_"
				AND ' . $this->db->sql_in_set('roles.role_name', $this->predefined_roles(), true),
		];

		$sql = $this->db->sql_build_query('SELECT', $sql_array);
		$result = $this->db->sql_query($sql);

		$roles = [];
		while ($row = $this->db->sql_fetchrow($result))
		{
			$roles[$row['role_id']] = $row['role_name'];
		}
		$this->db->sql_freeresult($result);

		return $roles;
	}

	/**
	 * An array of phpBB's predefined forum role names
	 *
	 * @return array
	 */
	protected function predefined_roles()
	{
		return [
			'ROLE_FORUM_FULL',
			'ROLE_FORUM_STANDARD',
			'ROLE_FORUM_NOACCESS',
			'ROLE_FORUM_READONLY',
			'ROLE_FORUM_LIMITED',
			'ROLE_FORUM_BOT',
			'ROLE_FORUM_ONQUEUE',
			'ROLE_FORUM_POLLS',
			'ROLE_FORUM_LIMITED_POLLS',
		];
	}
}

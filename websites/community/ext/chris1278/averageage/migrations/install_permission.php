<?php
/**
*
* Display of the average age of the users
*
* @copyright (c) 2022, Chris1278
* @license GNU General Public License, version 2 (GPL-2.0)
*
*/

namespace chris1278\averageage\migrations;

class install_permission extends \phpbb\db\migration\migration
{
	public static function depends_on()
	{
		return ['\phpbb\db\migration\data\v32x\v329'];
	}

	public function update_data()
	{
		return [
			['permission.add', ['u_averageage', true, 'u_']],
		];
	}

	public function revert_schema()
	{
		return [
			['permission.remove', ['u_averageage', true, 'u_']],
		];
	}
}

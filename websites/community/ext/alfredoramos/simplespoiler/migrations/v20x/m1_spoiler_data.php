<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\simplespoiler\migrations\v20x;

use phpbb\db\migration\container_aware_migration;
use alfredoramos\simplespoiler\includes\helper as spoiler_helper;

class m1_spoiler_data extends container_aware_migration
{
	/** @var \alfredoramos\simplespoiler\includes\helper */
	private $spoiler = null;

	/**
	 * Migration dependencies.
	 *
	 * @return array
	 */
	static public function depends_on()
	{
		return ['\alfredoramos\simplespoiler\migrations\v13x\m1_spoiler_data'];
	}

	/**
	 * Install BBCode in database.
	 *
	 * @return array
	 */
	public function update_data()
	{
		return [
			[
				'custom',
				[
					[$this->get_helper(), 'uninstall_bbcode']
				]
			]
		];
	}

	/**
	 * Spoiler helper.
	 *
	 * @return \alfredoramos\simplespoiler\includes\helper
	 */
	private function get_helper()
	{
		if ($this->spoiler === null)
		{
			$this->spoiler = new spoiler_helper(
				$this->container->get('dbal.conn'),
				$this->container->get('filesystem'),
				$this->container->get('language'),
				$this->container->get('template'),
				$this->container->get('config'),
				$this->container->get('text_formatter.utils'),
				$this->container->get('ext.manager'),
				$this->container->getParameter('core.root_path'),
				$this->container->getParameter('core.php_ext'),
				$this->container->getParameter('tables.bbcodes')
			);
		}

		return $this->spoiler;
	}
}

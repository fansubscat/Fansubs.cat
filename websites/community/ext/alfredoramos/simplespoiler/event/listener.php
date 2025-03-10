<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\simplespoiler\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use alfredoramos\simplespoiler\includes\helper as helper;

class listener implements EventSubscriberInterface
{
	/** @var \alfredoramos\simplespoiler\includes\helper */
	protected $helper;

	/**
	 * Listener constructor.
	 *
	 * @param \alfredoramos\simplespoiler\includes\helper $helper
	 *
	 * @return void
	 */
	public function __construct(helper $helper)
	{
		$this->helper = $helper;
	}

	/**
	 * Assign functions defined in this class to event listeners in the core.
	 *
	 * @return array
	 */
	static public function getSubscribedEvents()
	{
		return [
			'core.user_setup' => 'user_setup',
			'core.text_formatter_s9e_configure_after' => 'configure_spoiler',
			'core.text_formatter_s9e_parse_after' => 'parser_check_message',
			'core.help_manager_add_block_before' => 'bbcode_help',
			'core.acp_board_config_edit_add' => 'acp_config_add',
			'core.posting_modify_template_vars' => 'posting_template_vars',
			'alfredoramos.seometadata.clean_description_after' => 'clean_description_after'
		];
	}

	/**
	 * Load language files and modify user data on every page.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function user_setup($event)
	{
		$lang_set_ext = $event['lang_set_ext'];
		$lang_set_ext[] = [
			'ext_name'	=> 'alfredoramos/simplespoiler',
			'lang_set'	=> 'posting'
		];
		$event['lang_set_ext'] = $lang_set_ext;
	}

	/**
	 * Add BBCode.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function configure_spoiler($event)
	{
		$configurator = $event['configurator'];
		$spoiler = $this->helper->bbcode_data();

		// Spoiler data must not be empty
		if (empty($spoiler) ||
			empty($spoiler['bbcode_tag']) ||
			empty($spoiler['bbcode_match']) ||
			empty($spoiler['bbcode_tpl']))
		{
			return;
		}

		// Remove previous definitions
		unset(
			$configurator->BBCodes[$spoiler['bbcode_tag']],
			$configurator->tags[$spoiler['bbcode_tag']]
		);

		// Create spoiler BBCode
		$configurator->BBCodes->addCustom(
			$spoiler['bbcode_match'],
			$spoiler['bbcode_tpl']
		);
	}

	/**
	 * Remove spoilers that are nested too deep.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function parser_check_message($event)
	{
		$event['xml'] = $this->helper->remove_nested_spoilers($event['xml']);
	}

	/**
	 * Add a new BBCode FAQ entry.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function bbcode_help($event)
	{
		$this->helper->add_bbcode_help($event['block_name']);
	}

	/**
	 * Add ACP configuration data.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function acp_config_add($event)
	{
		if ($event['mode'] !== 'post')
		{
			return;
		}

		$event['display_vars'] = $this->helper->add_acp_config($event['display_vars']);
	}

	/**
	 * Add posting template variables.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function posting_template_vars($event)
	{
		$event['page_data'] = $this->helper->posting_template_vars($event['page_data']);
	}

	/**
	 * Remove spoilers from post description.
	 *
	 * @param object $event
	 *
	 * @return void
	 */
	public function clean_description_after($event)
	{
		$event['description'] = $this->helper->remove_description_spoilers($event['description']);
	}
}

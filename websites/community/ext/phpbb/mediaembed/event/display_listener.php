<?php
/**
 *
 * phpBB Media Embed PlugIn extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2016 phpBB Limited <https://www.phpbb.com>
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbb\mediaembed\event;

use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\language\language;
use phpbb\template\template;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Add MediaEmbed data to templates and help pages.
 */
class display_listener implements EventSubscriberInterface
{
	public const MEDIA_DEMO_URL = 'https://youtu.be/Ne18ZQ7LLI0';

	/** @var config */
	protected $config;

	/** @var db_text */
	protected $config_text;

	/** @var language */
	protected $language;

	/** @var template */
	protected $template;

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.display_custom_bbcodes' => 'setup_media_bbcode',
			'core.help_manager_add_block_before' => 'media_embed_help',
			'core.page_header' => 'setup_media_configs',
			'core.page_footer' => 'append_agreement',
		];
	}

	/**
	 * Constructor.
	 *
	 * @param config   $config      Configuration service
	 * @param db_text  $config_text Text configuration service
	 * @param language $language    Language service
	 * @param template $template    Template service
	 */
	public function __construct(config $config, db_text $config_text, language $language, template $template)
	{
		$this->config = $config;
		$this->config_text = $config_text;
		$this->language = $language;
		$this->template = $template;
	}

	/**
	 * Set template switch for Media BBCode button.
	 *
	 * @return void
	 */
	public function setup_media_bbcode()
	{
		$this->language->add_lang('common', 'phpbb/mediaembed');
		$this->template->assign_var('S_BBCODE_MEDIA', $this->config->offsetGet('media_embed_bbcode'));
	}

	/**
	 * Add MediaEmbed documentation to BBCode guide.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function media_embed_help($event)
	{
		if ($event['block_name'] !== 'HELP_BBCODE_BLOCK_OTHERS')
		{
			return;
		}

		$this->language->add_lang('help', 'phpbb/mediaembed');
		$this->template->assign_block_vars('faq_block', [
			'BLOCK_TITLE' => $this->language->lang('HELP_EMBEDDING_MEDIA'),
			'SWITCH_COLUMN' => false,
		]);

		$uid = $bitfield = $flags = '';
		$demo_text = self::MEDIA_DEMO_URL;
		generate_text_for_storage($demo_text, $uid, $bitfield, $flags, true, true);
		$demo_display = generate_text_for_display($demo_text, $uid, $bitfield, $flags);

		$this->template->assign_block_vars('faq_block.faq_row', [
			'FAQ_QUESTION' => $this->language->lang('HELP_EMBEDDING_MEDIA_QUESTION'),
			'FAQ_ANSWER' => $this->language->lang('HELP_EMBEDDING_MEDIA_ANSWER', self::MEDIA_DEMO_URL, $demo_display, implode(', ', $this->get_site_ids())),
		]);
	}

	/**
	 * Assign MediaEmbed display configuration to template.
	 *
	 * @return void
	 */
	public function setup_media_configs()
	{
		$this->template->assign_vars([
			'S_MEDIA_EMBED_FULL_WIDTH' => $this->config->offsetGet('media_embed_full_width'),
			'S_MEDIA_EMBED_MAX_WIDTHS' => json_decode($this->config_text->get('media_embed_max_width'), true),
		]);
	}

	/**
	 * Append MediaEmbed language to privacy agreement.
	 *
	 * @return void
	 */
	public function append_agreement()
	{
		if (!$this->template->retrieve_var('S_AGREEMENT') || $this->template->retrieve_var('AGREEMENT_TITLE') !== $this->language->lang('PRIVACY'))
		{
			return;
		}

		$this->language->add_lang('ucp', 'phpbb/mediaembed');
		$this->template->append_var('AGREEMENT_TEXT', $this->language->lang('MEDIA_EMBED_PRIVACY_POLICY', $this->config['sitename']));
	}

	/**
	 * Get enabled media site identifiers.
	 *
	 * @return array
	 */
	protected function get_site_ids()
	{
		$site_ids = $this->config_text->get('media_embed_sites');

		return $site_ids ? json_decode($site_ids, true) : [];
	}
}

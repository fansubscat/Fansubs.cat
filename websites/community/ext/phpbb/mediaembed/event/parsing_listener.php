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

use phpbb\auth\auth;
use phpbb\config\config;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Apply MediaEmbed permissions and per-message parser policy.
 */
class parsing_listener implements EventSubscriberInterface
{
	/** @var auth */
	protected $auth;

	/** @var config */
	protected $config;

	/** @var bool */
	protected $media_allowed = true;

	/** @var bool */
	protected $disable_plugin = false;

	/** @var bool */
	protected $disable_tag = false;

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents()
	{
		return [
			'core.permissions' => 'set_permissions',
			'core.posting_modify_message_text' => 'check_forum_permission',
			'core.ucp_pm_compose_modify_parse_before' => 'check_pm_permission',
			'core.message_parser_check_message' => 'set_parse_policy',
			'core.text_formatter_s9e_parse_before' => ['configure_parser', 100],
		];
	}

	/**
	 * Constructor.
	 *
	 * @param auth   $auth   Authentication service
	 * @param config $config Configuration service
	 */
	public function __construct(auth $auth, config $config)
	{
		$this->auth = $auth;
		$this->config = $config;
	}

	/**
	 * Register MediaEmbed forum and private-message permissions.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function set_permissions($event)
	{
		$event->update_subarray('permissions', 'f_mediaembed', ['lang' => 'ACL_F_MEDIAEMBED', 'cat' => 'content']);
		$event->update_subarray('permissions', 'u_pm_mediaembed', ['lang' => 'ACL_U_PM_MEDIAEMBED', 'cat' => 'pm']);
	}

	/**
	 * Store current forum's MediaEmbed permission state.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function check_forum_permission($event)
	{
		$this->media_allowed = $this->auth->acl_get('f_mediaembed', $event['forum_id'])
			&& $this->auth->acl_get('f_bbcode', $event['forum_id']);
	}

	/**
	 * Store current user's private-message MediaEmbed permission state.
	 *
	 * @return void
	 */
	public function check_pm_permission()
	{
		$this->media_allowed = $this->auth->acl_get('u_pm_mediaembed');
	}

	/**
	 * Calculate MediaEmbed policy for current message parse.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function set_parse_policy($event)
	{
		$signature_disabled = ($event['mode'] === 'sig' || $event['mode'] === 'text_reparser.user_signature')
			&& !$this->config->offsetGet('media_embed_allow_sig');

		$this->disable_tag = !$this->media_allowed || $signature_disabled || !$event['allow_bbcode'];
		$this->disable_plugin = $this->disable_tag
			|| !$event['allow_magic_url']
			|| !$this->config->offsetGet('media_embed_parse_urls');
	}

	/**
	 * Apply current message policy to runtime parser.
	 *
	 * @param \phpbb\event\data $event Event data
	 * @return void
	 */
	public function configure_parser($event)
	{
		$parser = $event['parser']->get_parser();
		$plugin_method = $this->disable_plugin ? 'disablePlugin' : 'enablePlugin';
		$tag_method = $this->disable_tag ? 'disableTag' : 'enableTag';

		$parser->{$plugin_method}('MediaEmbed');
		$parser->{$tag_method}('MEDIA');
	}
}

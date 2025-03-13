<?php
namespace fansubscat\phpbbbridge\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class event_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return [
			'core.login_box_before' => 'intercept_login',
			'core.page_header' => 'intercept_header',
			'core.append_sid' => 'disable_append_sid',
			'core.page_header_after' => 'replace_stylesheet',
			'core.text_formatter_s9e_configure_after'	=> 'configure_bbcode',
		];
	}

	protected $db;

	protected $user;

	protected $template;

	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\factory $db Database object
	*/
	public function __construct(\phpbb\db\driver\factory $db, \phpbb\user $user, \phpbb\template\twig\twig $template, $php_ext)
	{
		$this->db = $db;
		$this->user = $user;
		$this->template = $template;
		$this->php_ext = $php_ext;
	}

	public function intercept_login($event)
	{
		if (!$event['admin']) {
			header('Location: https://usuaris.fansubs.cat/inicia-la-sessio?return_to=forum');
		}
	}

	public function intercept_header($event)
	{
		$this->template->assign_vars(array(
			'S_SITE_THEME' => $GLOBALS['site_theme'],
		));
	}

	public function disable_append_sid($event)
	{
		global $_SID, $_EXTRA_URL;
		$url = $event['url'];
		$params = $event['params'];
		$is_amp = $event['is_amp'];
		$session_id = $event['session_id'];
		$is_route = $event['is_route'];

		$params_is_array = is_array($params);

		// Get anchor
		$anchor = '';
		if (strpos($url, '#') !== false)
		{
			list($url, $anchor) = explode('#', $url, 2);
			$anchor = '#' . $anchor;
		}
		else if (!$params_is_array && strpos($params, '#') !== false)
		{
			list($params, $anchor) = explode('#', $params, 2);
			$anchor = '#' . $anchor;
		}

		// Handle really simple cases quickly
		if (($_SID == '' || !defined('NEED_SID')) && $session_id === false && empty($_EXTRA_URL) && !$params_is_array && !$anchor)
		{
			if ($params === false)
			{
				$event['append_sid_overwrite'] = $url;
				return;
			}

			$url_delim = (strpos($url, '?') === false) ? '?' : (($is_amp) ? '&amp;' : '&');
			$event['append_sid_overwrite'] = $url . ($params !== false ? $url_delim. $params : '');
			return;
		}

		// Assign sid if session id is not specified
		if ($session_id === false && defined('NEED_SID'))
		{
			$session_id = $_SID;
		}

		$amp_delim = ($is_amp) ? '&amp;' : '&';
		$url_delim = (strpos($url, '?') === false) ? '?' : $amp_delim;

		// Appending custom url parameter?
		$append_url = (!empty($_EXTRA_URL)) ? implode($amp_delim, $_EXTRA_URL) : '';

		// Use the short variant if possible ;)
		if ($params === false)
		{
			// Append session id
			if (!$session_id)
			{
				$event['append_sid_overwrite'] = $url . (($append_url) ? $url_delim . $append_url : '') . $anchor;
				return;
			}
			else
			{
				$event['append_sid_overwrite'] = $url . (($append_url) ? $url_delim . $append_url . $amp_delim : $url_delim) . 'sid=' . $session_id . $anchor;
				return;
			}
		}

		// Build string if parameters are specified as array
		if (is_array($params))
		{
			$output = array();

			foreach ($params as $key => $item)
			{
				if ($item === NULL)
				{
					continue;
				}

				if ($key == '#')
				{
					$anchor = '#' . $item;
					continue;
				}

				$output[] = $key . '=' . $item;
			}

			$params = implode($amp_delim, $output);
		}

		// Append session id and parameters (even if they are empty)
		// If parameters are empty, the developer can still append his/her parameters without caring about the delimiter
		$event['append_sid_overwrite'] = $url . (($append_url) ? $url_delim . $append_url . $amp_delim : $url_delim) . $params . ((!$session_id) ? '' : $amp_delim . 'sid=' . $session_id) . $anchor;
	}

	public function replace_stylesheet($event)
	{
		$original_stylesheet = $this->template->retrieve_var('T_STYLESHEET_LINK');
		
		if ($GLOBALS['site_theme']=='light') {
			$original_stylesheet = str_replace('stylesheet.css', 'stylesheet-light.css', $original_stylesheet);
		} else {
			$original_stylesheet = str_replace('stylesheet.css', 'stylesheet-dark.css', $original_stylesheet);
		}
		
		$this->template->assign_var('T_STYLESHEET_LINK', $original_stylesheet);
	}

	public function configure_bbcode($event)
	{
		$configurator = $event['configurator'];
		$configurator->BBCodes->addCustom(
			'[center]{TEXT}[/center]',
			'<div style="text-align: center;">{TEXT}</span>'
		);
	}
}
?>

<?php
namespace fansubscat\phpbbbridge\event;

require_once(__DIR__.'/../../../../../../common/config/config.inc.php');

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class event_listener implements EventSubscriberInterface
{
	const FANSUBSCAT_RELAY_USER_ID = 2;

	static public function getSubscribedEvents()
	{
		return [
			'core.login_box_before' => 'intercept_login',
			'core.page_header' => 'intercept_header',
			'core.append_sid' => 'disable_append_sid',
			'core.page_header_after' => 'replace_stylesheet',
			'core.text_formatter_s9e_configure_after'	=> 'configure_bbcode',
			'core.submit_post_end'	=> 'relay_submitted_post',
			'core.delete_post_after'	=> 'relay_deleted_post',
		];
	}

	protected $db;

	protected $user;

	protected $template;

	protected $phpbb_root_path;

	protected $php_ext;

	/**
	* Constructor
	*
	* @param \phpbb\db\driver\factory $db Database object
	*/
	public function __construct(\phpbb\db\driver\factory $db, \phpbb\user $user, \phpbb\template\twig\twig $template, $phpbb_root_path, $php_ext)
	{
		$this->db = $db;
		$this->user = $user;
		$this->template = $template;
		$this->phpbb_root_path = $phpbb_root_path;
		$this->php_ext = $php_ext;
	}

	public function intercept_login($event)
	{
		if (!$event['admin']) {
			header('Location: https://usuaris.fansubs.cat/inicia-la-sessio');
		}
	}

	public function intercept_header($event)
	{
		$special_day = NULL;
		if (date('m-d')=='12-28' && !DISABLE_FOOLS_DAY) {
			$special_day = 'fools';
		} else if (date('m-d')=='04-23' && !DISABLE_SANT_JORDI_DAY) { // Sant Jordi
			$special_day = 'sant_jordi';
		} else if (date('m-d')>='10-31' && date('m-d')<='11-01' && !DISABLE_HALLOWEEN_DAYS) {
			$special_day = 'tots_sants';
		} else if (((date('m-d')>='12-05' && date('m-d')<='12-31') || (date('m-d')>='01-01' && date('m-d')<='01-06')) && !DISABLE_CHRISTMAS_DAYS) {
			$special_day = 'nadal';
		}
	
		$this->template->assign_vars(array(
			'S_SITE_THEME' => $GLOBALS['site_theme'],
			'S_SPECIAL_DAY' => $special_day,
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
		$configurator->BBCodes->addCustom(
			'[header]{TEXT}[/header]',
			'<span class="post-header">{TEXT}</span>'
		);
		$configurator->BBCodes->addCustom(
			'[subheader]{TEXT}[/subheader]',
			'<span class="post-subheader">{TEXT}</span>'
		);
	}

	public function relay_submitted_post($event)
	{
		$relayed_forum_ids = array(4, 5, 6);
		$mode = $event['mode'];
		$data = $event['data'];
		
		$poster_id = $data['poster_id'];
		if ($mode=='quote') {
			$poster_id = $this->user->data['user_id'];
		}
		
		//We relay all posts by other users, but not the ones from Fansubs.cat (they must be posted via the admin site)
		//Also, not the ones being posted by the API: this would create a loop and comments would be duplicated
		if (($mode=='post' || $mode=='reply' || $mode=='edit' || $mode=='quote') && in_array($data['forum_id'], $relayed_forum_ids) && !defined('FANSUBSCAT_API_POSTING') && $poster_id != self::FANSUBSCAT_RELAY_USER_ID) {
			$post_text = $data['message'];

			//This is needed for the generate_text_for_edit function
			require_once($this->phpbb_root_path . "includes/functions_content." . $this->php_ext);
			
			//Get $post_text as BBCode source
			$post_text = generate_text_for_edit($post_text, $data['bbcode_uid'], OPTION_FLAG_BBCODE+OPTION_FLAG_SMILIES+OPTION_FLAG_LINKS)['text'];
			
			$has_spoilers = str_contains($post_text, '[spoiler');
			
			//Adapt quotes
			$post_text = preg_replace_callback(
				'/\[quote=&quot;(.*?)&quot;[^\]]*\](.*?)\[\/quote\]/is',
				function ($matches) {
					$author = $matches[1];
					$quoted_text = $matches[2];
					$quoted_text = preg_replace('/\s+/', ' ', $quoted_text);
					return $author . " ha escrit:\n> " . $quoted_text . "\n";
				},
				$post_text
			);
			$post_text = preg_replace_callback(
				'/\[quote[^\]]*\](.*?)\[\/quote\]/is',
				function ($matches) {
					$quoted_text = $matches[1];
					$quoted_text = preg_replace('/\s+/', ' ', $quoted_text);
					return "> " . $quoted_text . "\n";
				},
				$post_text
			);
			
			//Adapt list elements:
			$post_text = str_replace('[*]', '- ', $post_text);
			
			//All other BBCode: keep the inside text
			$post_text = preg_replace('/\[[^\]]+\]/', '', $post_text);
			
			//Replace &quot; with quotes
			$post_text = str_replace('&quot;', '"', $post_text);

	 
			//Invoke API: add_or_edit_comment
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://api.fansubs.cat/internal/add_or_edit_comment?token='.INTERNAL_SERVICES_TOKEN);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, 
				  http_build_query(array(
				  	'forum_user_id' => $poster_id,
				  	'forum_post_id' => $data['post_id'],
				  	'forum_topic_id' => $data['topic_id'],
				  	'text' => $post_text,
				  	'has_spoilers' => $has_spoilers,
				  	)));
			curl_exec($curl);
			curl_close($curl);
		}
	}

	public function relay_deleted_post($event)
	{
		$mode = $event['mode'];
		$post_id = $event['post_id'];
		$data = $event['data'];
		
		if (!defined('FANSUBSCAT_API_POSTING') && $data['poster_id'] != self::FANSUBSCAT_RELAY_USER_ID) {
			//Invoke API: delete_comment
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, 'https://api.fansubs.cat/internal/delete_comment?token='.INTERNAL_SERVICES_TOKEN);
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 30);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, 
				  http_build_query(array(
				  	'forum_post_id' => $post_id,
				  	)));
			curl_exec($curl);
			curl_close($curl);
		}
	}
}
?>

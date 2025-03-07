<?php
namespace fansubscat\phpbbbridge;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class event_listener implements EventSubscriberInterface
{
	static public function getSubscribedEvents()
	{
		return [
			'core.login_box_before' => 'intercept_login',
			'core.page_header' => 'intercept_header',
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
}
?>

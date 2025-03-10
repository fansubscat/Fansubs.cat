<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Profile Flair ACP controller base class.
 */
abstract class acp_base_controller implements acp_base_interface
{
	/**
	 * @var ContainerInterface
	 */
	protected $container;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var request_interface
	 */
	protected $request;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * The URL for the current page.
	 *
	 * @var string
	 */
	protected $u_action;

	/**
	 * The root phpBB path.
	 *
	 * @var string
	 */
	protected $root_path;

	/**
	 * The script file extension.
	 *
	 * @var string
	 */
	protected $php_ext;

	/**
	 * The path to the custom images.
	 *
	 * @var string
	 */
	protected $img_path;

	/**
	 * @param ContainerInterface $container
	 * @param language           $language
	 * @param request_interface  $request
	 * @param template           $template
	 */
	public function __construct(ContainerInterface $container, language $language, request_interface $request, template $template)
	{
		$this->container = $container;
		$this->language = $language;
		$this->request = $request;
		$this->template = $template;
	}

	/**
	 * Set the phpBB installation path information.
	 *
	 * @param string $root_path The root phpBB path
	 * @param string $php_ext   The script file extension
	 * @param string $img_path  The path to the custom images
	 */
	public function set_path_info($root_path, $php_ext, $img_path)
	{
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
		$this->img_path = $img_path;
	}

	/**
	 * @inheritDoc
	 */
	public function set_page_url($page_url)
	{
		$this->u_action = $page_url;
	}
}

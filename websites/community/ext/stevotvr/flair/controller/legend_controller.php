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

use phpbb\controller\helper;
use phpbb\language\language;
use phpbb\path_helper;
use phpbb\template\template;
use stevotvr\flair\operator\category_interface;
use stevotvr\flair\operator\flair_interface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Profile Flair legend controller.
 */
class legend_controller
{
	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var category_interface
	 */
	protected $cat_operator;

	/**
	 * @var flair_interface
	 */
	protected $flair_operator;

	/**
	 * The path to the custom images.
	 *
	 * @var string
	 */
	protected $img_path;

	/**
	 * @param helper             $helper
	 * @param language           $language
	 * @param path_helper        $path_helper
	 * @param template           $template
	 * @param category_interface $cat_operator
	 * @param flair_interface    $flair_operator
	 */
	public function __construct(helper $helper, language $language, path_helper $path_helper, template $template, category_interface $cat_operator, flair_interface $flair_operator)
	{
		$this->helper = $helper;
		$this->language = $language;
		$this->template = $template;
		$this->cat_operator = $cat_operator;
		$this->flair_operator = $flair_operator;

		$this->img_path = $path_helper->get_web_root_path() . 'images/flair/';
	}

	/**
	 * Handler for route /flair
	 *
	 * @return Response
	 */
	public function handle()
	{
		$available_cats = $this->cat_operator->get_categories();
		$categories = array(array('category' => $this->language->lang('FLAIR_UNCATEGORIZED')));
		foreach ($available_cats as $entity)
		{
			$categories[$entity->get_id()]['category'] = $entity->get_name();
		}

		$flair = $this->flair_operator->get_flair();
		foreach ($flair as $entity)
		{
			$categories[$entity->get_category()]['items'][] = $entity;
		}

		$show_cats = (count($categories) > 1);

		foreach ($categories as $category_id => $category)
		{
			if (!isset($category['items']))
			{
				continue;
			}

			$this->template->assign_block_vars('cat', array(
				'CAT_ID'	=> $category_id,
				'CAT_NAME'	=> $show_cats ? $category['category'] : null,
			));

			foreach ($category['items'] as $entity)
			{
				$this->template->assign_block_vars('cat.item', array(
					'FLAIR_TYPE'		=> $entity->get_type(),
					'FLAIR_SIZE'		=> 3,
					'FLAIR_ID'			=> $entity->get_id(),
					'FLAIR_NAME'		=> $entity->get_name(),
					'FLAIR_DESC'		=> $entity->get_desc_for_display(),
					'FLAIR_COLOR'		=> $entity->get_color(),
					'FLAIR_ICON'		=> $entity->get_icon(),
					'FLAIR_ICON_COLOR'	=> $entity->get_icon_color(),
					'FLAIR_ICON_WIDTH'	=> $entity->get_icon_width(),
					'FLAIR_IMG'			=> $this->img_path . $entity->get_img(3),
				));
			}
		}

		return $this->helper->render('legend.html', $this->language->lang('FLAIR_LEGEND_TITLE'));
	}
}

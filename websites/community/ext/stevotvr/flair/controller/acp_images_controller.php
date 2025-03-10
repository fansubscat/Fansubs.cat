<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\controller;

use phpbb\files\factory;
use phpbb\files\upload;
use phpbb\json_response;
use stevotvr\flair\exception\base;
use stevotvr\flair\operator\image_interface as image_operator;

/**
 * Profile Flair images management ACP controller.
 */
class acp_images_controller extends acp_base_controller implements acp_images_interface
{
	/**
	 * @var factory
	 */
	protected $files_factory;

	/**
	 * @var image_operator
	 */
	protected $image_operator;

	/**
	 * Set up the controller.
	 *
	 * @param factory        $files_factory
	 * @param image_operator $image_operator
	 */
	public function setup(factory $files_factory, image_operator $image_operator)
	{
		$this->files_factory = $files_factory;
		$this->image_operator = $image_operator;
	}

	/**
	 * @inheritDoc
	 */
	public function list_images()
	{
		$used = $this->image_operator->get_used_images();
		foreach ($this->image_operator->get_images() as $file)
		{
			$ext = substr($file, strrpos($file, '.'));
			if (strtolower($ext) === '.svg')
			{
				$vars = array(
					'IMG_NAME'	=> $file,

					'U_IMG_X1'	=> $this->img_path . $file,
					'U_IMG_X2'	=> $this->img_path . $file,
					'U_IMG_X3'	=> $this->img_path . $file,
				);
			}
			else
			{
				$name = substr($file, 0, strrpos($file, '.'));

				$vars = array(
					'IMG_NAME'	=> $file,

					'U_IMG_X1'	=> $this->img_path . $name . '-x1' . $ext,
					'U_IMG_X2'	=> $this->img_path . $name . '-x2' . $ext,
					'U_IMG_X3'	=> $this->img_path . $name . '-x3' . $ext,
				);
			}

			if (!in_array($file, $used))
			{
				$vars['U_DELETE'] = $this->u_action . '&amp;action=delete&amp;image_name=' . $file;
			}

			$this->template->assign_block_vars('imgs', $vars);
		}

		$this->template->assign_vars(array(
			'U_ADD'	=> $this->u_action . '&amp;action=add',
		));
	}

	/**
	 * @inheritDoc
	 */
	public function add_image()
	{
		$show_form = true;
		$svg_only = false;
		$notices = array();

		if (!$this->image_operator->is_writable())
		{
			$notices[] = 'ACP_ERROR_NOT_WRITABLE';
			$show_form = false;
		}

		if (!$this->image_operator->can_process())
		{
			$notices[] = 'ACP_ERROR_NO_IMG_LIB';
			$svg_only = true;
		}

		if ($show_form)
		{
			$errors = array();

			add_form_key('add_image');

			$upload = $this->files_factory->get('files.upload');

			if ($upload->is_valid('img_file'))
			{
				if (!check_form_key('add_image'))
				{
					$errors[] = 'FORM_INVALID';
				}

				$overwrite = $this->request->variable('img_overwrite', false);

				$this->upload_image($errors, $upload, $overwrite, $svg_only);
			}

			$errors = array_map(array($this->language, 'lang'), $errors);

			$this->template->assign_vars(array(
				'S_SHOW_FORM'	=> $show_form,

				'S_ERROR'	=> !empty($errors),
				'ERROR_MSG'	=> !empty($errors) ? implode('<br />', $errors) : '',

				'IMG_OVERWRITE'	=> $this->request->variable('img_overwrite', false),

				'U_ACTION'	=> $this->u_action . '&amp;action=add',
			));
		}

		$notices = array_map(array($this->language, 'lang'), $notices);

		$this->template->assign_vars(array(
			'S_ADD'	=> true,

			'S_NOTICE'		=> !empty($notices),
			'NOTICE_MSG'	=> !empty($notices) ? implode('<br />', $notices) : '',

			'U_BACK'	=> $this->u_action,
		));
	}

	/**
	 * Handle image uploading.
	 *
	 * @param array   &$errors   The array to populate with error strings
	 * @param upload  $upload    The upload object
	 * @param boolean $overwrite Overwrite any existing images with the same name
	 * @param boolean $svg_only  Only allow SVG files
	 */
	protected function upload_image(array &$errors, upload $upload, $overwrite, $svg_only)
	{
		$allowed = $svg_only ? array('svg') : array('gif', 'png', 'jpg', 'jpeg', 'svg');
		$filespec = $upload->set_allowed_extensions($allowed)
						->handle_upload('files.types.form', 'img_file');

		if (!$filespec || !empty($filespec->error))
		{
			$errors[] = 'ACP_ERROR_NOT_UPLOADED';
			return;
		}

		if (!$filespec->is_image())
		{
			$errors[] = 'ACP_ERROR_UPLOAD_INVALID';
			return;
		}

		$filespec->clean_filename('real');

		try
		{
			$this->image_operator->add_image($filespec->get('realname'), $filespec->get('filename'), $overwrite);

			trigger_error($this->language->lang('ACP_FLAIR_IMG_ADD_SUCCESS') . adm_back_link($this->u_action));
		}
		catch (base $e)
		{
			$errors[] = $e->get_message($this->language);
		}
	}

	/**
	 * @inheritDoc
	 */
	public function delete_image($name)
	{
		if ($this->image_operator->count_image_items($name))
		{
			trigger_error($this->language->lang('ACP_FLAIR_IMG_DELETE_ERRORED') . adm_back_link($this->u_action), E_USER_WARNING);
		}

		if (!confirm_box(true))
		{
			$hidden_fields = build_hidden_fields(array(
				'image_name'	=> $name,
				'mode'			=> 'images',
				'action'		=> 'delete',
			));
			confirm_box(false, $this->language->lang('ACP_FLAIR_DELETE_IMG_CONFIRM'), $hidden_fields);
			return;
		}

		$this->image_operator->delete_image($name);

		if ($this->request->is_ajax())
		{
			$json_response = new json_response();
			$json_response->send(array(
				'MESSAGE_TITLE'	=> $this->language->lang('INFORMATION'),
				'MESSAGE_TEXT'	=> $this->language->lang('ACP_FLAIR_IMG_DELETE_SUCCESS'),
				'REFRESH_DATA'	=> array(
					'time'	=> 3
				),
			));
		}

		trigger_error($this->language->lang('ACP_FLAIR_IMG_DELETE_SUCCESS') . adm_back_link($this->u_action));
	}
}

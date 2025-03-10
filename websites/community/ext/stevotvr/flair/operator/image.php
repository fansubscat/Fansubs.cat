<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2018, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\operator;

use phpbb\filesystem\filesystem_interface;
use stevotvr\flair\exception\base;

/**
 * Profile Flair image operator.
 */
class image extends operator implements image_interface
{
	/**
	 * @var filesystem_interface
	 */
	protected $filesystem;

	/**
	 * The path to the custom images.
	 *
	 * @var string
	 */
	protected $img_path;

	/**
	 * The heights in pixels associated with each image size.
	 *
	 * @var array
	 */
	protected $sizes = array(1 => 22, 2 => 44, 3 => 66);

	/**
	 * Set up the operator.
	 *
	 * @param filesystem_interface $filesystem
	 * @param string               $img_path   The path to the custom images
	 */
	public function setup(filesystem_interface $filesystem, $img_path)
	{
		$this->filesystem = $filesystem;
		$this->img_path = $img_path;
	}

	/**
	 * @inheritDoc
	 */
	public function is_writable()
	{
		if ($this->filesystem->exists($this->img_path) && $this->filesystem->is_writable($this->img_path))
		{
			return true;
		}

		if ($this->filesystem->exists($this->img_path))
		{
			$this->filesystem->phpbb_chmod($this->img_path, filesystem_interface::CHMOD_ALL);
		}
		else
		{
			$this->filesystem->mkdir($this->img_path, 0777);
		}

		return $this->filesystem->is_writable($this->img_path);
	}

	/**
	 * @inheritDoc
	 */
	public function can_process()
	{
		return function_exists('gd_info') || class_exists('Imagick');
	}

	/**
	 * @inheritDoc
	 */
	public function count_image_items($image)
	{
		$sql = 'SELECT COUNT(flair_id) AS count
				FROM ' . $this->flair_table . "
				WHERE flair_type = 1
					AND flair_img = '" . $this->db->sql_escape($image) . "'";
		$this->db->sql_query($sql);
		$count = (int) $this->db->sql_fetchfield('count');
		$this->db->sql_freeresult();

		return $count;
	}

	/**
	 * @inheritDoc
	 */
	public function get_images()
	{
		$images = array();

		foreach (glob($this->img_path . '*') as $file)
		{
			$ext = substr($file, strrpos($file, '.'));
			switch (strtolower($ext))
			{
				case '.svg':
					$images[] = basename($file);
					break;
				case '.gif':
				case '.png':
				case '.jpg':
				case '.jpeg':
					$name = substr($file, 0, strrpos($file, '-x1.'));
					if (!$this->filesystem->exists(array($name . '-x2' . $ext, $name . '-x3' . $ext)))
					{
						break;
					}
					$images[] = basename($name) . $ext;
			}
		}

		return $images;
	}

	/**
	 * @inheritDoc
	 */
	public function get_used_images()
	{
		$images = array();

		$sql = 'SELECT flair_img
				FROM ' . $this->flair_table . '
				WHERE flair_type = 1';
		$this->db->sql_query($sql);
		while ($row = $this->db->sql_fetchrow())
		{
			$images[$row['flair_img']] = true;
		}
		$this->db->sql_freeresult();

		return array_keys($images);
	}

	/**
	 * @inheritDoc
	 */
	public function add_image($name, $file, $overwrite)
	{
		if ($overwrite)
		{
			$this->delete_image($name);
		}

		$ext = substr($name, strrpos($name, '.'));
		$name = substr($name, 0, strrpos($name, '.'));

		if (strtolower($ext) === '.svg')
		{
			if ($this->filesystem->exists($this->img_path . $name . $ext))
			{
				throw new base('EXCEPTION_IMG_CONFLICT');
			}

			$this->filesystem->copy($file, $this->img_path . $name . $ext);
		}
		else
		{
			if (count(glob($this->img_path . $name . '-x[123]' . $ext)) > 0)
			{
				throw new base('EXCEPTION_IMG_CONFLICT');
			}

			if (class_exists('Imagick'))
			{
				$this->create_images_imagick($name, $ext, $file);
			}
			else if (function_exists('gd_info'))
			{
				$this->create_images_gd($name, $ext, $file);
			}
		}

	}

	/**
	 * @inheritDoc
	 */
	public function delete_image($name)
	{
		$ext = substr($name, strrpos($name, '.'));
		if (strtolower($ext) === '.svg')
		{
			$this->filesystem->remove($this->img_path . $name);
		}
		else
		{
			$name = substr($name, 0, strrpos($name, '.'));
			$this->filesystem->remove(glob($this->img_path . $name . '-x[123]' . $ext));
		}
	}

	/**
	 * Create a new image set using the Imagick library.
	 *
	 * @param string $name The base name of the output files without extension
	 * @param string $ext  The extension of the output files
	 * @param string $file The path to the source file
	 *
	 * @throws base
	 */
	protected function create_images_imagick($name, $ext, $file)
	{
		try
		{
			$image = new \Imagick($file);
			$image->stripImage();

			$src_width = $image->getImageWidth();
			$src_height = $image->getImageHeight();

			$dest_path = $this->img_path . DIRECTORY_SEPARATOR;

			foreach ($this->sizes as $size => $height)
			{
				$width = (int) ($src_width * ($height / $src_height));

				$scaled = $image->clone();
				$scaled->resizeImage($width, $height, \Imagick::FILTER_LANCZOS, 1);
				file_put_contents($dest_path . $name . '-x' . $size . $ext, $scaled);

				$scaled->clear();
			}

			$image->clear();
		}
		catch (\Exception $e)
		{
			throw new base('EXCEPTION_IMG_PROCESSING');
		}
	}

	/**
	 * Create a new image set using the GD library.
	 *
	 * @param string $name The base name of the output files without extension
	 * @param string $ext  The extension of the output files
	 * @param string $file The path to the source file
	 *
	 * @throws base
	 */
	protected function create_images_gd($name, $ext, $file)
	{
		$type = null;
		switch (strtolower($ext))
		{
			case '.gif':
				$type = 'gif';
			break;
			case '.png':
				$type = 'png';
			break;
			case '.jpg':
			case '.jpeg':
				$type = 'jpeg';
			break;
			default:
				throw new base('EXCEPTION_IMG_PROCESSING');
		}

		$image = call_user_func('imagecreatefrom' . $type, $file);

		if (!$image)
		{
			throw new base('EXCEPTION_IMG_PROCESSING');
		}

		$src_width = imagesx($image);
		$src_height = imagesy($image);
		$palette = imagecolorstotal($image) > 0;
		$trans_color = array('red' => 0, 'green' => 0, 'blue' => 0);
		if ($type === 'gif' || $type === 'png')
		{
			$trans_idx = imagecolortransparent($image);
			if ($trans_idx >= 0)
			{
				$trans_color = imagecolorsforindex($image, $trans_idx);
			}
			$trans_color = imagecolorallocatealpha(
				$image,
				$trans_color['red'],
				$trans_color['green'],
				$trans_color['blue'],
				127
			);
		}

		foreach ($this->sizes as $size => $height)
		{
			$width = (int) ($src_width * ($height / $src_height));

			$scaled = imagecreatetruecolor($width, $height);

			if ($type === 'gif' || $type === 'png')
			{
				imagealphablending($scaled, false);
				imagecolortransparent($scaled, $trans_color);
				imagefill($scaled, 0, 0, $trans_color);
			}

			imagecopyresampled($scaled, $image, 0, 0, 0, 0, $width, $height, $src_width, $src_height);

			if ($type === 'gif' || $type === 'png')
			{
				for ($y = 0; $y < $height; $y++)
				{
					for ($x = 0; $x < $width; $x++)
					{
						if (((imagecolorat($scaled, $x, $y) >> 24) & 0x7f) >= 100)
						{
							imagesetpixel($scaled, $x, $y, $trans_color);
						}
					}
				}

				if ($palette)
				{
					imagetruecolortopalette($scaled, true, 255);
				}

				imagesavealpha($scaled, true);
			}

			$dest = $this->img_path . $name . '-x' . $size . $ext;
			call_user_func('image' . $type, $scaled, $dest);

			imagedestroy($scaled);
		}

		imagedestroy($image);
	}
}

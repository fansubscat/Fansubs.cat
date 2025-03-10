<?php
/**
 *
 * Profile Flair. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\flair\exception;

use phpbb\language\language;

/**
 * Profile Flair base exception class.
 */
class base extends \Exception
{
	/**
	 * The parameters for the translated message.
	 *
	 * @var array
	 */
	protected $lang_array;

	/**
	 * The exceptions language file is loaded.
	 *
	 * @var boolean
	 */
	protected $lang_loaded = false;

	/**
	 * @param string       $message    The raw exception message
	 * @param array|string $lang_array The parameters for the translated message
	 */
	public function __construct($message = null, $lang_array = null)
	{
		parent::__construct($message);
		$this->lang_array = ($lang_array) ? (array) $lang_array : null;
	}

	/**
	 * Get the translated message.
	 *
	 * @param language $language
	 *
	 * @return string The translated message
	 */
	public function get_message(language $language)
	{
		$this->add_lang($language);

		if ($this->lang_array)
		{
			$lang_array = $this->translate_parts($language);
			return call_user_func_array(array($language, 'lang'), $lang_array);
		}

		return $this->message;
	}

	/**
	 * Load the exceptions language file.
	 *
	 * @param language $language
	 */
	protected function add_lang(language $language)
	{
		if ($this->lang_loaded)
		{
			return;
		}

		$language->add_lang('exceptions', 'stevotvr/flair');

		$this->lang_loaded = true;
	}

	/**
	 * Translate the parameters of the language array.
	 *
	 * @param language $language
	 *
	 * @return array The translated language parameters
	 */
	protected function translate_parts(language $language)
	{
		$lang_array = array();

		foreach ($this->lang_array as $key => $value)
		{
			if ($key === 0)
			{
				$lang_array[] = $value;
				continue;
			}

			$translated = $language->lang($value);
			if ($translated === $value && strpos($value, 'EXCEPTION_FIELD_') === 0)
			{
				$lang_array[] = strtolower(substr($value, 16));
				continue;
			}

			$lang_array[] = $translated;
		}

		return $lang_array;
	}
}

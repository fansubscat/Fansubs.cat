<?php

/**
 * Simple Spoiler extension for phpBB.
 * @author Alfredo Ramos <alfredo.ramos@yandex.com>
 * @copyright 2017 Alfredo Ramos
 * @license GPL-2.0-only
 */

namespace alfredoramos\simplespoiler\includes;

use phpbb\db\driver\factory as database;
use phpbb\filesystem\filesystem;
use phpbb\language\language;
use phpbb\template\template;
use phpbb\config\config;
use phpbb\textformatter\s9e\utils;
use phpbb\extension\manager as ext_manager;

class helper
{
	/** @var \phpbb\db\driver\factory */
	protected $db;

	/** @var \phpbb\filesystem\filesystem */
	protected $filesystem;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

	/** @var \phpbb\extension\manager */
	protected $ext_manager;

	/** @var string */
	protected $root_path;

	/** @var string */
	protected $php_ext;

	/** @var \acp_bbcodes */
	protected $acp_bbcodes;

	/** @var array */
	protected $tables;

	/**
	 * Constructor of the helper class.
	 *
	 * @param \phpbb\db\driver\factory			$db
	 * @param \phpbb\filesystem\filesystem		$filesystem
	 * @param \phpbb\language\language			$language
	 * @param \phpbb\template\template			$template
	 * @param \phpbb\config\config				$config
	 * @param \phpbb\textformatter\s9e\utils	$utils
	 * @param \phpbb\extension\manager			$ext_manager
	 * @param string							$root_path
	 * @param string							$php_ext
	 * @param string							$bbcodes_table
	 *
	 * @return void
	 */
	public function __construct(database $db, filesystem $filesystem, language $language, template $template, config $config, utils $utils, ext_manager $ext_manager, $root_path, $php_ext, $bbcodes_table)
	{
		$this->db = $db;
		$this->filesystem = $filesystem;
		$this->language = $language;
		$this->template = $template;
		$this->config = $config;
		$this->utils = $utils;
		$this->ext_manager = $ext_manager;
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;

		// Assign tables
		if (empty($this->tables))
		{
			$this->tables = [
				'bbcodes' => $bbcodes_table
			];
		}
	}

	/**
	 * Install the new BBCode adding it in the database or updating it if it already exists.
	 *
	 * @return void
	 */
	public function install_bbcode()
	{
		$data = $this->bbcode_data();

		if (empty($data))
		{
			return;
		}

		// Lazy load BBCodes helper
		if (!isset($this->acp_bbcodes))
		{
			if (!class_exists('acp_bbcodes'))
			{
				include($this->root_path . 'includes/acp/acp_bbcodes.' . $this->php_ext);
			}

			$this->acp_bbcodes = new \acp_bbcodes;
		}

		// Remove conflicting BBCode
		$this->remove_bbcode('spoiler=');

		$data['bbcode_id'] = (int) $this->bbcode_id();
		$data = array_replace(
			$data,
			$this->acp_bbcodes->build_regexp(
				$data['bbcode_match'],
				$data['bbcode_tpl']
			)
		);

		// Get old BBCode ID
		$old_bbcode_id = (int) $this->bbcode_exists($data['bbcode_tag']);

		// Update or add BBCode
		if ($old_bbcode_id > NUM_CORE_BBCODES)
		{
			$this->update_bbcode($old_bbcode_id, $data);
		}
		else
		{
			$this->add_bbcode($data);
		}
	}

	/**
	 * Uninstall the BBCode from the database.
	 *
	 * @return void
	 */
	public function uninstall_bbcode()
	{
		$this->remove_bbcode('spoiler');
	}

	/**
	 * Check whether BBCode already exists.
	 *
	 * @param string $bbcode_tag
	 *
	 * @return integer
	 */
	public function bbcode_exists($bbcode_tag = '')
	{
		if (empty($bbcode_tag))
		{
			return -1;
		}

		$sql = 'SELECT bbcode_id
			FROM ' . $this->tables['bbcodes'] . '
			WHERE ' . $this->db->sql_build_array('SELECT', ['bbcode_tag' => $bbcode_tag]);
		$result = $this->db->sql_query($sql);
		$bbcode_id = (int) $this->db->sql_fetchfield('bbcode_id');
		$this->db->sql_freeresult($result);

		// Set invalid index if BBCode doesn't exist to avoid
		// getting the first record of the table
		$bbcode_id = $bbcode_id > NUM_CORE_BBCODES ? $bbcode_id : -1;

		return $bbcode_id;
	}

	/**
	 * Calculate the ID for the BBCode that is about to be installed.
	 *
	 * @return integer
	 */
	public function bbcode_id()
	{
		$sql = 'SELECT MAX(bbcode_id) as last_id
			FROM ' . $this->tables['bbcodes'];
		$result = $this->db->sql_query($sql);
		$bbcode_id = (int) $this->db->sql_fetchfield('last_id');
		$this->db->sql_freeresult($result);
		$bbcode_id += 1;

		if ($bbcode_id <= NUM_CORE_BBCODES)
		{
			$bbcode_id = NUM_CORE_BBCODES + 1;
		}

		return $bbcode_id;
	}


	/**
	 * Add the BBCode in the database.
	 *
	 * @param array $data
	 *
	 * @return void
	 */
	public function add_bbcode($data = [])
	{
		if (empty($data) ||
			(!empty($data['bbcode_id']) && (int) $data['bbcode_id'] > BBCODE_LIMIT))
		{
			return;
		}

		$sql = 'INSERT INTO ' . $this->tables['bbcodes'] . '
			' . $this->db->sql_build_array('INSERT', $data);
		$this->db->sql_query($sql);

	}

	/**
	 * Remove BBCode by tag.
	 *
	 * @param string $bbcode_tag
	 *
	 * @return void
	 */
	public function remove_bbcode($bbcode_tag = '')
	{
		if (empty($bbcode_tag))
		{
			return;
		}

		$bbcode_id = (int) $this->bbcode_exists($bbcode_tag);

		// Remove only if exists
		if ($bbcode_id > NUM_CORE_BBCODES)
		{
			$sql = 'DELETE FROM ' . $this->tables['bbcodes'] . '
				WHERE bbcode_id = ' . $bbcode_id;
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Update BBCode data if it already exists.
	 *
	 * @param integer	$bbcode_id
	 * @param array		$data
	 *
	 * @return void
	 */
	public function update_bbcode($bbcode_id = -1, $data = [])
	{
		$bbcode_id = (int) $bbcode_id;

		if ($bbcode_id <= NUM_CORE_BBCODES || empty($data))
		{
			return;
		}

		unset($data['bbcode_id']);

		$sql = 'UPDATE ' . $this->tables['bbcodes'] . '
			SET ' . $this->db->sql_build_array('UPDATE', $data) . '
			WHERE bbcode_id = ' . $bbcode_id;
		$this->db->sql_query($sql);
	}

	/**
	 * Add a new entry in the BBCode FAQ.
	 *
	 * @param string $block_name
	 *
	 * @return void
	 */
	public function add_bbcode_help($block_name = '')
	{
		if (empty($block_name) || $block_name !== 'HELP_BBCODE_BLOCK_OTHERS')
		{
			return;
		}

		// Load language keys
		$this->language->add_lang('help/bbcode', 'alfredoramos/simplespoiler');

		// FAQ helper, it just stores language keys
		$faq = [
			'title' => 'HELP_BBCODE_BLOCK_SPOILERS',
			'questions' => [
				'HELP_BBCODE_SPOILERS_BASIC_QUESTION' => 'HELP_BBCODE_SPOILERS_BASIC_ANSWER',
				'HELP_BBCODE_SPOILERS_TITLE_QUESTION' => 'HELP_BBCODE_SPOILERS_TITLE_ANSWER'
			]
		];

		// Add help block
		$this->template->assign_block_vars('faq_block', [
			'BLOCK_TITLE'	=> $this->language->lang($faq['title']),
			'SWITCH_COLUMN'	=> false
		]);

		// Arguments for functions generate_text_for_{storage,display}()
		$uid = $bitfield = $flags = null;

		// Generate questions and answers
		foreach ($faq['questions'] as $key => $value)
		{
			$has_title = (strpos($key, 'TITLE') !== false);
			$text = sprintf(
				'[spoiler%2$s]%1$s[/spoiler]',
				$this->language->lang('HELP_BBCODE_SPOILERS_DEMO_BODY'),
				$has_title ? sprintf(' title=%s', $this->language->lang('HELP_BBCODE_SPOILERS_DEMO_TITLE')) : ''
			);
			generate_text_for_storage($text, $uid, $bitfield, $flags, true, true, true);
			$parsed_text = generate_text_for_display($text, $uid, $bitfield, $flags);

			$this->template->assign_block_vars('faq_block.faq_row', [
				'FAQ_QUESTION'	=> $this->language->lang($key),
				'FAQ_ANSWER'	=> $this->language->lang(
					$value,
					$parsed_text,
					$this->language->lang('HELP_BBCODE_SPOILERS_DEMO_BODY'),
					($has_title ? $this->language->lang('HELP_BBCODE_SPOILERS_DEMO_TITLE') : null)
				)
			]);
		}
	}

	/**
	 * Add ACP configuration data.
	 *
	 * @param array $display_vars
	 *
	 * @return array
	 */
	public function add_acp_config($display_vars = [])
	{
		if (empty($display_vars) || empty($display_vars['vars']))
		{
			return [];
		}

		if (!function_exists('phpbb_insert_config_array'))
		{
			include($this->root_path . 'includes/functions_acp.' . $this->php_ext);
		}

		$display_vars['vars'] = phpbb_insert_config_array(
			$display_vars['vars'],
			[
				'max_spoiler_depth' => [
					'lang' => 'SPOILER_DEPTH_LIMIT',
					'validate' => 'int:0:9999',
					'type' => 'number:0:9999',
					'explain' => true
				]
			],
			['after' => 'max_quote_depth']
		);

		return $display_vars;
	}

	/**
	 * Remove nested spoilers at given depth.
	 *
	 * @param string $xml
	 *
	 * @return string
	 */
	public function remove_nested_spoilers($xml = '')
	{
		if (empty($xml))
		{
			return '';
		}

		$max_depth = (int) $this->config['max_spoiler_depth'];

		if ($max_depth <= 0)
		{
			return $xml;
		}

		return $this->utils->remove_bbcode($xml, 'SPOILER', $max_depth);
	}

	/**
	 * Remove spoilers from post description.
	 *
	 * @param string $description
	 *
	 * @see \alfredoramos\seometadata\includes\helper::clean_description()
	 *
	 * @return string
	 */
	public function remove_description_spoilers($description = '')
	{
		if (empty($description))
		{
			return '';
		}

		// Remove spoilers at any depth
		return $this->utils->remove_bbcode($description, 'SPOILER', 0);
	}

	/**
	 * Set template variable for ABBC3 icon type.
	 *
	 * @param array $template_vars
	 *
	 * @return array
	 */
	public function posting_template_vars($template_vars = [])
	{
		if (empty($template_vars) || !$this->ext_manager->is_enabled('vse/abbc3') || empty($this->config['abbc3_icons_type']))
		{
			return $template_vars;
		}

		return array_merge($template_vars, [
			'SIMPLE_SPOILER_ABBC3_ICON_TYPE' => $this->config['abbc3_icons_type']
		]);
	}

	/**
	 * BBCode data used in the migration files.
	 *
	 * @return array
	 */
	public function bbcode_data()
	{
		// Return absolute path if file exists
		$xsl = $this->filesystem->realpath(
			__DIR__ . '/../styles/all/template/spoiler.xsl'
		);

		// Store the (trimmed) file content if it is readable
		$template = $this->filesystem->is_readable($xsl) ? trim(file_get_contents($xsl)) : '';

		// The spoiler template should not be empty
		if (empty($template))
		{
			return [];
		}

		return [
			'bbcode_tag'	=> 'spoiler',
			'bbcode_match'	=> '[spoiler={PARSE=/(?<title>.+)/} title={TEXT2;optional}]{TEXT1}[/spoiler]',
			'bbcode_tpl'	=> $template,

			// Kept for backwards compatibility
			'bbcode_helpline'		=> 'SPOILER_HELPLINE',
			'display_on_posting'	=> 1
		];
	}
}

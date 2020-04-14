<?php
/**
 *
 * phpBB Studio - Admin Dashboard. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\admindashboard\controller;

/**
 * phpBB Studio - Admin Dashboard: Admin controller
 */
class admin
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\template\context */
	protected $context;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbbstudio\admindashboard\kasimi */
	protected $kasimi;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var string Table prefix */
	protected $table_prefix;

	/** @var string Admin relative path */
	protected $admin_path;

	/** @var string phpBB root path */
	protected $root_path;

	/** @var string php File extension */
	protected $php_ext;

	/** @var array Dashboard extension parameters */
	protected $params;

	/** @var string Custom form action */
	protected $u_action;

	/**
	 * Constructor.
	 *
	 * @param \phpbb\auth\auth						$auth			Auth object
	 * @param \phpbb\template\context				$context		Template context object
	 * @param \phpbb\db\driver\driver_interface		$db				Database object
	 * @param \phpbbstudio\admindashboard\kasimi	$kasimi			Kasimi the person
	 * @param \phpbb\language\language				$language		Language object
	 * @param \phpbb\path_helper					$path_helper	Path helper object
	 * @param \phpbb\request\request				$request		Request object
	 * @param \phpbb\template\template				$template		Template object
	 * @param \phpbb\user							$user			User object
	 * @param string								$table_prefix	Table prefix
	 * @param array									$params			Dashboard extension parameters
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\template\context $context,
		\phpbb\db\driver\driver_interface $db,
		\phpbbstudio\admindashboard\kasimi $kasimi,
		\phpbb\language\language $language,
		\phpbb\path_helper $path_helper,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		string $table_prefix,
		array $params
	)
	{
		$this->auth			= $auth;
		$this->context		= $context;
		$this->db			= $db;
		$this->kasimi		= $kasimi;
		$this->language		= $language;
		$this->request		= $request;
		$this->template		= $template;
		$this->user			= $user;
		$this->table_prefix	= $table_prefix;
		$this->admin_path	= $path_helper->get_phpbb_root_path() . $path_helper->get_adm_relative_path();
		$this->root_path	= $path_helper->get_phpbb_root_path();
		$this->php_ext		= $path_helper->get_php_ext();
		$this->params		= $params;
	}

	/**
	 * Handle any actions for the Admin Dashboard.
	 *
	 * @return void
	 */
	public function handle(): void
	{
		$action = $this->request->variable('action', '', true);
		$action = $this->request->is_set_post('search') ? 'search' : $action;
		$action = $this->request->is_set('dashboard') ? 'dashboard' : $action;

		switch ($action)
		{
			case 'settings':
				$data = [
					'header_fixed'   => $this->request->is_set_post('header_fixed'),
					'header_colour'  => $this->request->variable('header_colour', '', true),
					'sidebar_fixed'  => $this->request->is_set_post('sidebar_fixed'),
					'sidebar_colour' => $this->request->variable('sidebar_colour', '', true),
					'sidebar_corner' => $this->request->variable('sidebar_corner', '', true),
					'sidebar_size'   => $this->request->variable('sidebar_size', '', true),
				];

				$data = array_merge($data, [
					'header_colour'  => in_array($data['header_colour'], $this->params['colours']) ? $data['header_colour'] : 'white',
					'sidebar_colour' => in_array($data['sidebar_colour'], $this->params['colours']) ? $data['sidebar_colour'] : 'purple-indigo',
					'sidebar_corner' => in_array($data['sidebar_corner'], $this->params['corners']) ? $data['sidebar_corner'] : 'rounded',
					'sidebar_size'   => in_array($data['sidebar_size'], $this->params['sizes']) ? $data['sidebar_size'] : 'large',
				]);

				$this->save_data($data);

				$json_response = new \phpbb\json_response;
				$json_response->send([true]);
			break;

			case 'dashboard':
				$opposite = $this->request->variable('dashboard', '', true);
				$settings = [
					'detach_qa', 'display_qa',
					'display_logs', 'display_users', 'display_notes',
					'display_stats', 'remodel_stats',
				];

				if ($opposite)
				{
					$this->save_opposite_data($opposite);
				}
				else
				{
					$data = [];

					foreach ($settings as $setting)
					{
						$data[$setting] = $this->request->is_set_post($setting);
					}

					$this->save_data($data);
				}

				$json_response = new \phpbb\json_response;
				$json_response->send([
					'success'	=> true,
					'setting'	=> $opposite,
				]);
			break;

			case 'search':
				$data = $this->get_search_data();
				$results = [];
				$modules = [];

				$keywords = $this->request->variable('search', '', true);
				$keywords = preg_split('/\s+/', $keywords);
				$keywords = array_map('utf8_strtolower', $keywords);
				$keywords = array_unique($keywords);
				$keywords = array_filter($keywords, function($keyword)
				{
					return strlen($keyword) > 2;
				});

				foreach ($data as $mode => $keys)
				{
					foreach ($keys['vars'] as $key => $strings)
					{
						foreach ($strings as $string)
						{
							$string = utf8_strtolower($this->language->lang($string));

							foreach ($keywords as $word)
							{
								if (strpos($string, $word) !== false)
								{
									$results[$mode][] = $key;

									// Break out of keywords and strings
									break 2;
								}
							}
						}
					}
				}

				foreach ($results as $mode => $keys)
				{
					$this->template->assign_block_vars('results', [
						'TITLE'		=> $this->language->lang($data[$mode]['title']),
						'U_VIEW'	=> append_sid("{$this->admin_path}index.{$this->php_ext}", ['i' => 'acp_board', 'mode' => $mode]),
					]);

					foreach ($keys as $key)
					{
						if (isset($data[$mode]['vars'][$key][1]))
						{
							$explain = $this->language->lang($data[$mode]['vars'][$key][1]);
						}

						$this->template->assign_block_vars('results.settings', [
							'TITLE'		=> $this->language->lang($data[$mode]['vars'][$key][0]),
							'EXPLAIN'	=> !empty($explain) ? $explain : '',
							'U_VIEW'	=> append_sid("{$this->admin_path}index.{$this->php_ext}", ['i' => 'acp_board', 'mode' => $mode, 'studio_search' => $key, '#' => $key]),
						]);
					}
				}

				$context_data = $this->context->get_data_ref();
				$categories = $context_data['l_block1'];

				foreach ($categories as $cat)
				{
					$subcategories = false;

					if (!empty($cat['l_block2']))
					{
						foreach ($cat['l_block2'] as $sub)
						{
							$items = false;

							if (!empty($sub['l_block3']))
							{
								foreach ($sub['l_block3'] as $item)
								{
									foreach ($keywords as $word)
									{
										if ($this->in_title($word, $item['L_TITLE']))
										{
											$modules[$cat['ID']]['subcategories'][$sub['ID']]['items'][$item['ID']] = [
												'TITLE'		=> $item['L_TITLE'],
												'U_VIEW'	=> $item['U_TITLE'],
											];

											$subcategories = true;
											$items = true;

											// Break out of keywords
											break;
										}
									}
								}
							}

							if ($items)
							{
								/**
								 * Add the subcategory regardless
								 * as an item has been found
								 */
								$modules[$cat['ID']]['subcategories'][$sub['ID']]['TITLE'] = $sub['L_TITLE'];
								$modules[$cat['ID']]['subcategories'][$sub['ID']]['U_VIEW'] = $sub['U_TITLE'];
							}
							else
							{
								foreach ($keywords as $word)
								{
									if ($this->in_title($word, $sub['L_TITLE']))
									{
										$modules[$cat['ID']]['subcategories'][$sub['ID']] = [
											'TITLE'		=> $sub['L_TITLE'],
											'U_VIEW'	=> $sub['U_TITLE'],
										];

										$subcategories = true;

										// Break out of keyword
										break;
									}
								}
							}
						}
					}

					if ($subcategories)
					{
						/**
						 * Add the category regardless
						 * as a subcategory or item has been found
						 */
						$modules[$cat['ID']]['TITLE'] = $cat['L_TITLE'];
						$modules[$cat['ID']]['U_VIEW'] = $cat['U_TITLE'];
					}
					else
					{
						foreach ($keywords as $word)
						{
							if ($this->in_title($word, $cat['L_TITLE']))
							{
								$modules[$cat['ID']] = [
									'TITLE'		=> $cat['L_TITLE'],
									'U_VIEW'	=> $cat['U_TITLE'],
								];

								// Break out of keywords
								break;
							}
						}
					}
				}

				$this->template->assign_vars([
					'STUDIO_KEYWORDS'	=> $keywords,
					'STUDIO_MODULES'	=> $modules,
				]);
			break;
		}
	}

	/**
	 * Save the settings' data in the Admin Dashboard table.
	 *
	 * @param array		$data		The settings
	 * @return void
	 */
	protected function save_data(array $data): void
	{
		if ($this->save_data_update())
		{
			$sql = 'UPDATE ' . $this->table_prefix . 'admin_dashboard
					SET ' . $this->db->sql_build_array('UPDATE', $data) . '
					WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
		else
		{
			$data['user_id'] = $this->user->data['user_id'];

			$sql = 'INSERT INTO ' . $this->table_prefix . 'admin_dashboard ' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Toggle a specific setting in the Admin Dashboard table.
	 *
	 * @param string	$column		The setting to toggle
	 * @return void
	 */
	protected function save_opposite_data(string $column): void
	{
		if ($this->save_data_update())
		{
			$column = $this->db->sql_escape($column);

			$sql = 'UPDATE ' . $this->table_prefix . 'admin_dashboard
					SET ' . $column . ' = NOT ' . $column . '
					WHERE user_id = ' . (int) $this->user->data['user_id'];
			$this->db->sql_query($sql);
		}
		else
		{
			$data = [
				'user_id'	=> (int) $this->user->data['user_id'],
				$column		=> in_array($column, ['detach_qa', 'display_qa']) ? true : false,
			];

			$sql = 'INSERT INTO ' . $this->table_prefix . 'admin_dashboard ' . $this->db->sql_build_array('INSERT', $data);
			$this->db->sql_query($sql);
		}
	}

	/**
	 * Check whether a record already exists.
	 *
	 * @return bool			Whether to run an UPDATE or INSERT INTO
	 */
	protected function save_data_update(): bool
	{
		$sql = 'SELECT 1
				FROM ' . $this->table_prefix . 'admin_dashboard
				WHERE user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query_limit($sql, 1);
		$update = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		return (bool) $update;
	}

	/**
	 * Helper function to see if a keyword is within a title string.
	 *
	 * @param string	$keyword	The keyword to search for
	 * @param string	$title		The title string to search within
	 * @return bool
	 */
	protected function in_title(string $keyword, string $title): bool
	{
		return strpos(utf8_strtolower($title), $keyword) !== false;
	}

	/**
	 * Get the available settings for the various modes in acp_board
	 *
	 * @return array
	 */
	protected function get_search_data(): array
	{
		include($this->root_path . 'includes/acp/acp_board.' . $this->php_ext);

		$data = [];
		$lang = [];
		$modes = [];
		$board = new \acp_board();

		$this->kasimi->set_happy(true);

		if ($this->auth->acl_get('a_board'))
		{
			$modes += ['settings', 'features', 'avatar', 'message', 'post', 'signature', 'feed', 'registration'];
		}

		if ($this->auth->acl_get('a_server'))
		{
			$modes += ['auth', 'email', 'cookie', 'load', 'server', 'security'];
		}

		foreach ($modes as $mode)
		{
			try
			{
				$board->main('', $mode);
			}
			catch (\phpbbstudio\admindashboard\exception\admin_dashboard_exception $e)
			{
				$data[$mode] = $this->kasimi->get_brain();
			}
		}

		foreach ($data as $mode => $vars)
		{
			if (isset($vars['lang']))
			{
				$this->language->add_lang($vars['lang']);
			}

			foreach ($vars['vars'] as $key => $value)
			{
				if (is_array($value) && isset($value['lang']))
				{
					$lang[$mode]['title'] = $vars['title'];
					$lang[$mode]['vars'][$key][] = $value['lang'];

					if ($value['explain'] && $this->language->is_set($value['lang'] . '_EXPLAIN'))
					{
						$lang[$mode]['vars'][$key][] = $value['lang'] . '_EXPLAIN';
					}
				}
			}
		}

		$this->language->add_lang('acp/board');

		return $lang;
	}

	/**
	 * Set custom form action.
	 *
	 * @param  string	$u_action	Custom form action
	 * @return void
	 */
	public function set_page_url(string $u_action): void
	{
		$this->u_action = $u_action;
	}
}

<?php
/**
 *
 * phpBB Studio - Admin Dashboard. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace phpbbstudio\admindashboard\event;

use phpbbstudio\admindashboard\exception\admin_dashboard_exception;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * phpBB Studio - Admin Dashboard: Event listener
 */
class listener implements EventSubscriberInterface
{
	/** @var \phpbb\auth\auth */
	protected $auth;

	/** @var \phpbb\config\config */
	protected $config;

	/** @var \phpbb\config\db_text */
	protected $config_text;

	/** @var \phpbb\db\driver\driver_interface */
	protected $db;

	/** @var \phpbbstudio\admindashboard\kasimi */
	protected $kasimi;

	/** @var \phpbb\language\language */
	protected $language;

	/** @var \phpbb\notification\manager */
	protected $notifications;

	/** @var \phpbb\textformatter\s9e\parser */
	protected $parser;

	/** @var \phpbb\textformatter\s9e\renderer */
	protected $renderer;

	/** @var \phpbb\request\request */
	protected $request;

	/** @var \phpbb\template\template */
	protected $template;

	/** @var \phpbb\user */
	protected $user;

	/** @var \phpbb\textformatter\s9e\utils */
	protected $utils;

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

	/**
	 * Constructor.
	 *
	 * @param \phpbb\auth\auth						$auth				Auth object
	 * @param \phpbb\config\config					$config				Config object
	 * @param \phpbb\config\db_text					$config_text		Config text object
	 * @param \phpbb\db\driver\driver_interface		$db					Database object
	 * @param \phpbbstudio\admindashboard\kasimi	$kasimi				Kasimi the person
	 * @param \phpbb\language\language				$language			Language object
	 * @param \phpbb\notification\manager			$notifications		Notifications manager object
	 * @param \phpbb\textformatter\s9e\parser		$parser				Textformatter parser object
	 * @param \phpbb\textformatter\s9e\renderer		$renderer			Textformatter renderer object
	 * @param \phpbb\request\request				$request			Request object
	 * @param \phpbb\template\template				$template			Template object
	 * @param \phpbb\user							$user				User object
	 * @param \phpbb\textformatter\s9e\utils		$utils				Textformatter utilities object
	 * @param string								$table_prefix		Table prefix
	 * @param string								$admin_path			Admin relative path
	 * @param string								$root_path			phpBB root path
	 * @param string								$php_ext			php File extension
	 * @param array									$params				Dashboard extension parameters
	 */
	public function __construct(
		\phpbb\auth\auth $auth,
		\phpbb\config\config $config,
		\phpbb\config\db_text $config_text,
		\phpbb\db\driver\driver_interface $db,
		\phpbbstudio\admindashboard\kasimi $kasimi,
		\phpbb\language\language $language,
		\phpbb\notification\manager $notifications,
		\phpbb\textformatter\s9e\parser $parser,
		\phpbb\textformatter\s9e\renderer $renderer,
		\phpbb\request\request $request,
		\phpbb\template\template $template,
		\phpbb\user $user,
		\phpbb\textformatter\s9e\utils $utils,
		string $table_prefix,
		string $admin_path,
		string $root_path,
		string $php_ext,
		array $params = []
	)
	{
		$this->auth				= $auth;
		$this->config			= $config;
		$this->config_text		= $config_text;
		$this->db				= $db;
		$this->kasimi			= $kasimi;
		$this->language			= $language;
		$this->notifications	= $notifications;
		$this->parser			= $parser;
		$this->renderer			= $renderer;
		$this->request			= $request;
		$this->template			= $template;
		$this->user				= $user;
		$this->utils			= $utils;

		$this->table_prefix		= $table_prefix;
		$this->admin_path		= $root_path . $admin_path;
		$this->root_path		= $root_path;
		$this->php_ext			= $php_ext;
		$this->params			= $params;
	}

	/**
	 * {@inheritDoc}
	 */
	public static function getSubscribedEvents(): array
	{
		return [
			'core.acp_board_config_edit_add'	=> 'studio_search',
			'core.adm_page_header_after'		=> 'studio_acp',
			'core.acp_main_notice'				=> 'studio_dashboard',
			'core.build_config_template'		=> 'studio_config_template',
		];
	}

	/**
	 * Throw an exception when this event is manually triggered by this extension.
	 *
	 * @param \phpbb\event\data		$event		The event object
	 * @return void
	 */
	public function studio_search(\phpbb\event\data $event): void
	{
		if ($this->kasimi->is_happy())
		{
			$this->kasimi->set_brain($event['display_vars']);

			throw new admin_dashboard_exception();
		}
	}

	/**
	 * Additional dashboard data available throughout the ACP.
	 *
	 * @return void
	 */
	public function studio_acp(): void
	{
		$sql = 'SELECT *
				FROM ' .  $this->table_prefix . 'admin_dashboard
				WHERE user_id = ' . (int) $this->user->data['user_id'];
		$result = $this->db->sql_query_limit($sql, 1);
		$settings = $this->db->sql_fetchrow($result);
		$this->db->sql_freeresult($result);

		if ($this->config['load_notifications'] && $this->config['allow_board_notifications'])
		{
			$notifications = $this->notifications->load_notifications('notification.method.board', [
				'all_unread'	=> true,
				'limit'			=> 5,
			]);

			/** @var \phpbb\notification\type\base $notification */
			foreach ($notifications['notifications'] as $notification)
			{
				$this->template->assign_block_vars('notifications', $notification->prepare_for_display());
			}

			$this->template->assign_vars([
				'UNREAD_NOTIFICATIONS_COUNT'	=> $notifications['unread_count'],
				'NOTIFICATIONS_COUNT'			=> $notifications['unread_count'],

				'S_NOTIFICATIONS_DISPLAY'		=> true,

				'U_VIEW_ALL_NOTIFICATIONS'		=> append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=ucp_notifications'),
				'U_MARK_ALL_NOTIFICATIONS'		=> append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=ucp_notifications&amp;mode=notification_list&amp;mark=all&amp;token=' . generate_link_hash('mark_all_notifications_read')),
				'U_NOTIFICATION_SETTINGS'		=> append_sid("{$this->root_path}ucp.{$this->php_ext}", 'i=ucp_notifications&amp;mode=notification_options'),
			]);
		}


		if ($this->template->retrieve_var('S_STUDIO_DASHBOARD') === true)
		{
			if (isset($settings['display_logs']) && empty($settings['display_logs']))
			{
				$this->template->destroy_block_vars('log');
			}

			if (isset($settings['display_users']) && empty($settings['display_users']))
			{
				$this->template->assign_var('S_INACTIVE_USERS', false);
			}
		}

		$this->template->assign_vars([
			'USER_AVATAR'				=> phpbb_get_user_avatar($this->user->data),
			'USERNAME_COLOUR'			=> get_username_string('full', $this->user->data['user_id'], $this->user->data['username'], $this->user->data['user_colour']),

			'T_STUDIO_CORNERS'			=> !empty($this->params['corners']) ? $this->params['corners'] : [],
			'T_STUDIO_COLOURS'			=> !empty($this->params['colours']) ? $this->params['colours'] : [],
			'T_STUDIO_SIZES'			=> !empty($this->params['sizes']) ? $this->params['sizes'] : [],

			'T_STUDIO_HEADER_COLOUR'	=> !empty($settings['header_colour']) ? $settings['header_colour'] : 'white',
			'S_STUDIO_HEADER_FIXED'		=> isset($settings['header_fixed']) ? $settings['header_fixed'] : true,

			'T_STUDIO_SIDEBAR_COLOUR'	=> !empty($settings['sidebar_colour']) ? $settings['sidebar_colour'] : 'purple-indigo',
			'T_STUDIO_SIDEBAR_CORNER'	=> !empty($settings['sidebar_corner']) ? $settings['sidebar_corner'] : 'rounded',
			'T_STUDIO_SIDEBAR_SIZE'		=> !empty($settings['sidebar_size']) ? $settings['sidebar_size'] : 'large',
			'S_STUDIO_SIDEBAR_FIXED'	=> isset($settings['sidebar_fixed']) ? $settings['sidebar_fixed'] : true,

			'S_STUDIO_DETACH_QA'		=> isset($settings['detach_qa']) ? $settings['detach_qa'] : false,
			'S_STUDIO_DISPLAY_QA'		=> isset($settings['display_qa']) ? $settings['display_qa'] : false,
			'S_STUDIO_DISPLAY_NOTES'	=> isset($settings['display_notes']) ? $settings['display_notes'] : true,
			'S_STUDIO_DISPLAY_LOGS'		=> isset($settings['display_logs']) ? $settings['display_logs'] : true,
			'S_STUDIO_DISPLAY_USERS'	=> isset($settings['display_users']) ? $settings['display_users'] : true,
			'S_STUDIO_DISPLAY_STATS'	=> isset($settings['display_stats']) ? $settings['display_stats'] : true,
			'S_STUDIO_REMODEL_STATS'	=> isset($settings['remodel_stats']) ? $settings['remodel_stats'] : false,

			'U_STUDIO_ADMIN_DASHBOARD'	=> append_sid("{$this->admin_path}index.{$this->php_ext}", [
				'i'		=> '-phpbbstudio-admindashboard-acp-main_module',
				'mode'	=> 'general',
			]),
		]);
	}

	/**
	 * Additional dashboard data available on the Dashboard page
	 *
	 * @return void
	 */
	public function studio_dashboard(): void
	{
		$notes = $this->config_text->get('admin_dashboard_notes');

		if ($this->request->is_set_post('submit_notes'))
		{
			$notes = $this->request->variable('admin_notes', '', true);
			$notes = $this->parser->parse($notes);

			$this->config_text->set('admin_dashboard_notes', $notes);
		}

		if ($this->auth->acl_get('a_modules'))
		{
			$sql = 'SELECT module_id 
					FROM ' . $this->table_prefix . "modules
					WHERE module_langname = 'ACP_QUICK_ACCESS'";
			$result = $this->db->sql_query_limit($sql, 1);
			$qa_id = $this->db->sql_fetchfield('module_id');
			$this->db->sql_freeresult($result);

			$this->template->assign_var('U_STUDIO_ADMIN_QA', append_sid("{$this->admin_path}index.{$this->php_ext}", [
				'i'			=> 'acp_modules',
				'mode'		=> 'acp',
				'parent_id'	=> $qa_id,
			]));
		}

		$this->template->assign_vars([
			'STUDIO_ADMIN_NOTES'		=> $this->renderer->render(htmlspecialchars_decode($notes, ENT_COMPAT)),
			'STUDIO_ADMIN_NOTES_EDIT'	=> $this->utils->unparse($notes),

			'S_STUDIO_DASHBOARD'		=> true,
			'S_STUDIO_ADMIN_NOTES_EDIT'	=> $this->request->is_set('edit_notes'),
		]);
	}

	/**
	 * Override the radio template so it becomes stylable.
	 *
	 * @param \phpbb\event\data		$event		The event object
	 * @return void
	 */
	public function studio_config_template(\phpbb\event\data $event): void
	{
		if ($event['tpl_type'][0] === 'radio')
		{
			$key = $event['key'];
			$new = $event['new'];
			$name = $event['name'];
			$type = $event['tpl_type'];

			$config_key = str_replace('config[', '', $name);
			$config_key = trim($config_key, ']');

			$template = '';
			$id_added = false;

			foreach (explode('_', $type[1]) as $label)
			{
				$negation	= in_array($label, ['no', 'disabled']);

				$checked	= $negation ? !$new[$config_key] : $config_key;
				$checked	= $checked ? ' checked="checked"' : '';
				$class		= $negation ? ' studio-bool-no' : 'studio-bool-yes';
				$value		= $negation ? ' value="0"' : ' value="1"';
				$id			= !$id_added ? ' id="' . $key . '"' : '';

				$id_added	= true;

				$template .= '<label>';
				$template .= '<input class="studio-bool hidden"' . $id . ' name="' . $name . '" type="radio" ' . $value . $checked . ' />';
				$template .= '<span class="' . $class . '">' . $this->language->lang(strtoupper($label)) . '</span>';
				$template .= '</label>';
			}

			$event['tpl'] = $template;
		}
	}
}

<?php
/**
 *
 * phpBB Studio - Admin Dashboard. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2020, phpBB Studio, https://www.phpbbstudio.com
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = [];
}

// Some characters you may want to copy&paste: ’ » “ ” …
$lang = array_merge($lang, [
	'ACP_INDEX'							=> 'Dashboard',
	'ACP_ADMIN_DASHBOARD_SEARCH'		=> 'Search',

	'STUDIO_DASHBOARD_ADMIN_NOTES'		=> 'Admin notes',
	'STUDIO_DASHBOARD_AVATARS'			=> 'Avatars',
	'STUDIO_DASHBOARD_BOARD'			=> 'Board',
	'STUDIO_DASHBOARD_COLOUR'			=> 'Colour',
	'STUDIO_DASHBOARD_CORNER'			=> 'Corner',
	'STUDIO_DASHBOARD_DETACH_QA'		=> 'Detach quick access',
	'STUDIO_DASHBOARD_DISPLAY_LOGS'		=> 'Display admin logs',
	'STUDIO_DASHBOARD_DISPLAY_NOTES'	=> 'Display admin notes',
	'STUDIO_DASHBOARD_DISPLAY_QA'		=> 'Display quick access',
	'STUDIO_DASHBOARD_DISPLAY_STATS'	=> 'Display statistics',
	'STUDIO_DASHBOARD_DISPLAY_USERS'	=> 'Display inactive users',
	'STUDIO_DASHBOARD_FIXED'			=> 'Fixed',
	'STUDIO_DASHBOARD_GZIP'				=> 'GZip',
	'STUDIO_DASHBOARD_HEADER'			=> 'Header',
	'STUDIO_DASHBOARD_MODULES'			=> 'Modules',
	'STUDIO_DASHBOARD_MODULES_MATCHING'	=> 'Matching modules',
	'STUDIO_DASHBOARD_MODULES_NONE'		=> 'No modules matched your criteria.',
	'STUDIO_DASHBOARD_ORPHANS'			=> [
		1 => '%d orphan',
		2 => '%d orphans',
	],
	'STUDIO_DASHBOARD_PER_DAY'			=> '%s / day',
	'STUDIO_DASHBOARD_REMODEL_STATS'	=> 'Restyle statistics',
	'STUDIO_DASHBOARD_RESULTS_NO'		=> 'No results',
	'STUDIO_DASHBOARD_RESULTS_NONE'		=> 'No results matched your criteria.',
	'STUDIO_DASHBOARD_RESULTS_NOTE'		=> 'However, this does not mean what you are looking for does not exist. 
											<br>Unfortunately there are only limited possibilities to searching for settings.
											<br>Mostly we can only find settings from the “General” tab, with a few exceptions.',
	'STUDIO_DASHBOARD_SEARCH_ACP'		=> 'Search the ACP',
	'STUDIO_DASHBOARD_SIDEBAR'			=> 'Sidebar',
	'STUDIO_DASHBOARD_SIZE'				=> 'Size',
]);

if (isset($lang['POWERED_BY']) && strpos($lang['POWERED_BY'], 'phpBB Studio') === false)
{
	$lang['POWERED_BY'] = 'Powered by <a href="https://phpbbstudio.com">Admin dashboard</a> &copy; phpBB Studio<br>' . $lang['POWERED_BY'];
}

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
	'ACP_INDEX'							=> 'Tablero',
	'ACP_ADMIN_DASHBOARD_SEARCH'		=> 'Buscar',

	'STUDIO_DASHBOARD_ADMIN_NOTES'		=> 'Notas del Admin',
	'STUDIO_DASHBOARD_AVATARS'			=> 'Avatares',
	'STUDIO_DASHBOARD_BOARD'			=> 'Foro',
	'STUDIO_DASHBOARD_COLOUR'			=> 'Color',
	'STUDIO_DASHBOARD_CORNER'			=> 'Corner',
	'STUDIO_DASHBOARD_DETACH_QA'		=> 'Separar acceso rápido',
	'STUDIO_DASHBOARD_DISPLAY_LOGS'		=> 'Mostrar registros del Admin',
	'STUDIO_DASHBOARD_DISPLAY_NOTES'	=> 'Mostrar notas del Admin',
	'STUDIO_DASHBOARD_DISPLAY_QA'		=> 'Mostrar acceson rápido',
	'STUDIO_DASHBOARD_DISPLAY_STATS'	=> 'Mostrar estadísticas',
	'STUDIO_DASHBOARD_DISPLAY_USERS'	=> 'Mostrar usuarios inactivos',
	'STUDIO_DASHBOARD_FIXED'			=> 'Fijado',
	'STUDIO_DASHBOARD_GZIP'				=> 'GZip',
	'STUDIO_DASHBOARD_HEADER'			=> 'Encabezado',
	'STUDIO_DASHBOARD_MODULES'			=> 'Módulos',
	'STUDIO_DASHBOARD_MODULES_MATCHING'	=> 'Módulos a juego',
	'STUDIO_DASHBOARD_MODULES_NONE'		=> 'Ningún módulo coincide con sus criterios.',
	'STUDIO_DASHBOARD_ORPHANS'			=> [
		1 => '%d huérfano',
		2 => '%d huérfanos',
	],
	'STUDIO_DASHBOARD_PER_DAY'			=> '%s / día',
	'STUDIO_DASHBOARD_REMODEL_STATS'	=> 'Estadísticas de estilo',
	'STUDIO_DASHBOARD_RESULTS_NO'		=> 'No hay resultados',
	'STUDIO_DASHBOARD_RESULTS_NONE'		=> 'Ningún resultado coincide con sus criterios.',
	'STUDIO_DASHBOARD_RESULTS_NOTE'		=> 'Sin embargo, esto no significa que lo que está buscando no existe. 
											<br>Desafortunadamente, solo hay posibilidades limitadas para buscar configuraciones.
											<br>La mayoría de las veces solo podemos encontrar configuraciones en la pestaña “General”, con algunas excepciones.',
	'STUDIO_DASHBOARD_SEARCH_ACP'		=> 'Buscar en el PCA',
	'STUDIO_DASHBOARD_SIDEBAR'			=> 'Barra lateral',
	'STUDIO_DASHBOARD_SIZE'				=> 'Tamaño',
]);

if (isset($lang['POWERED_BY']))
{
	$lang['POWERED_BY'] = 'Desarrollado por <a href="https://phpbbstudio.com">Admin dashboard</a> &copy; phpBB Studio<br>' . $lang['POWERED_BY'];
}

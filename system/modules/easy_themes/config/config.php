<?php if (!defined('TL_ROOT')) die('You cannot access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Yanick Witschi 2011
 * @author     Yanick Witschi <yanick.witschi@certo-net.ch>
 * @package    easy_themes
 * @license    LGPL
 * @filesource
 */

/**
 * easy_themes version
 */
@define('ET_VERSION', '1.4');
@define('ET_BUILD', '0');
 
// Define an own function as we cannot use Environment here because we're disturbing the singleton stack if so
function getBaseScript()
{
  $scriptName = (php_sapi_name() == 'cgi' || php_sapi_name() == 'cgi-fcgi') && ($_SERVER['ORIG_PATH_INFO'] ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO']) ? ($_SERVER['ORIG_PATH_INFO'] ? $_SERVER['ORIG_PATH_INFO'] : $_SERVER['PATH_INFO']) : ($_SERVER['ORIG_SCRIPT_NAME'] ? $_SERVER['ORIG_SCRIPT_NAME'] : $_SERVER['SCRIPT_NAME']);
  return preg_replace('/^' . preg_quote(TL_PATH, '/') . '\/?/i', '', $scriptName);
}

/**
 * Theme modules
 * EXAMPLE OF HOW YOU COULD EXTEND EASY_THEMES WITH YOUR OWN EXTENSION USING THE FOLLOW GLOBALS ARRAY
 * $GLOBALS['TL_EASY_THEMES_MODULES']['my_module'] = array
 * (
 * 'title'         => 'My Module',
 * 'label'         => 'My Module',
 * 'href'          => 'main.php?do=my_module&theme=%s',
 * 'href_fragment' => 'table=tl_additional_source',
 * 'icon'          => 'system/modules/mein_modul/html/mein_modul.png'
 * );
 * 
 * title:			optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][1]
 * label:			optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][0]
 * href:			optional, alternative to href_fragment, overwrites href_fragment!
 * href_fragment:	alternative to href, will be added to the url like this: main.php?do=themes&id=<theme id>
 * icon:			optional, if not given, easy_themes will try to load an icon using Controller::generateImage('my_module.gif', ...)
 */
$GLOBALS['TL_EASY_THEMES_MODULES'] = array_merge
(
	array
	(
		'edit' => array
		(
			'href_fragment' => 'act=edit'
		),
		'css' => array
		(
			'href_fragment' => 'table=tl_style_sheet'
		),
		'modules' => array
		(
			'href_fragment' => 'table=tl_module'
		),
		'layout' => array
		(
			'href_fragment' => 'table=tl_layout'
		)
	),
	is_array($GLOBALS['TL_EASY_THEMES_MODULES']) ? $GLOBALS['TL_EASY_THEMES_MODULES'] : array()
);


/**
 * Hooks
 */
// fix uninstall exception - see #756
// fix database error - see #822
if(!(($_GET['do'] == 'repository_manager' && $_GET['uninstall'] == 'easy_themes') || getBaseScript() == $GLOBALS['TL_CONFIG']['websitePath'] . '/contao/install.php'))
{
	if(TL_MODE == 'BE')
	{
		$GLOBALS['TL_HOOKS']['parseBackendTemplate'][]              = array('EasyThemes', 'addContainer');
		$GLOBALS['TL_HOOKS']['loadLanguageFile']['EasyThemesHook']  = array('EasyThemes', 'addHeadings');
		$GLOBALS['TL_HOOKS']['loadDataContainer'][]					= array('EasyThemes', 'setUser');
		$GLOBALS['TL_HOOKS']['getUserNavigation'][]					= array('EasyThemes', 'modifyUserNavigation');
	}
}


/**
 * Backend form fields
 */
$GLOBALS['BE_FFL']['checkbox_minOne'] = 'CheckBoxChooseAtLeastOne';
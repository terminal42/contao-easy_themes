<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

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
 * @copyright  Yanick Witschi 2010
 * @author     Yanick Witschi <http://www.certo-net.ch>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */

/**
 * Table tl_user
 */
$GLOBALS['TL_LANG']['tl_user']['enableEasyTheme']     = array('Enable EasyTheme','Enable EasyTheme for faster theme editing.');
$GLOBALS['TL_LANG']['tl_user']['EasyThemeMode']       = array('EasyTheme Mode','Decide whether you prefer to use EasyTheme with the contextmenu, the mousover or the direct DOM-Inject mode.');
$GLOBALS['TL_LANG']['tl_user']['showShortEasyTheme']  = array('Enable short view','Enable the short view for EasyTheme.');
$GLOBALS['TL_LANG']['tl_user']['activeModules']       = array('Active modules','Choose the modules you want to be displayed.');
$GLOBALS['TL_LANG']['tl_user']['activeThemes']        = array('Active themes','Choose the themes you want to be displayed.');
$GLOBALS['TL_LANG']['tl_user']['contextmenu']         = 'Contextmenu';
$GLOBALS['TL_LANG']['tl_user']['mouseover']           = 'Mouseover';
$GLOBALS['TL_LANG']['tl_user']['inject']              = 'DOM-Inject';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_user']['EasyTheme_legend'] = 'EasyTheme';

/**
 * Error message for Widget
 */
$GLOBALS['TL_LANG']['tl_user']['chooseAtLeastOne'] = 'You have to choose at least one option for the field "%s"!';
?>
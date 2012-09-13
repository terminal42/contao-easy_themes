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
 * @copyright  Yanick Witschi 2010 - 2012
 * @author     Yanick Witschi <yanick.witschi@certo-net.ch>
 * @package    easy_themes
 * @license    LGPL
 * @filesource
 */

/**
 * Table tl_user
 */
$GLOBALS['TL_LANG']['tl_user']['et_enable']			= array('Enable EasyTheme','Enable EasyTheme for faster theme editing.');
$GLOBALS['TL_LANG']['tl_user']['et_mode']			= array('EasyTheme Mode','Decide whether you prefer to use EasyTheme with the contextmenu, the mousover, the direct DOM-Inject or the back end module mode.');
$GLOBALS['TL_LANG']['tl_user']['et_short']			= array('Enable short view','Enable the short view for EasyTheme.');
$GLOBALS['TL_LANG']['tl_user']['et_activeModules']	= array('Active modules','Choose the modules you want to be displayed.');
$GLOBALS['TL_LANG']['tl_user']['et_bemodRef']		= array('Group reference','Please choose a group as reference. The themes get listed <strong>after</strong> after this group (on top if none is chosen).');
$GLOBALS['TL_LANG']['tl_user']['contextmenu']		= 'Contextmenu';
$GLOBALS['TL_LANG']['tl_user']['mouseover']			= 'Mouseover';
$GLOBALS['TL_LANG']['tl_user']['inject']			= 'DOM-Inject';
$GLOBALS['TL_LANG']['tl_user']['be_mod']			= 'Back end modules';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_user']['et_legend'] = 'EasyTheme';

/**
 * Error message for Widget
 */
$GLOBALS['TL_LANG']['tl_user']['chooseAtLeastOne'] = 'You have to choose at least one option for the field "%s"!';
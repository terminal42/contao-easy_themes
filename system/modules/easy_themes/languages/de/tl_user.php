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
 * Table tl_user
 */
$GLOBALS['TL_LANG']['tl_user']['et_enable']       	= array('EasyTheme aktivieren','Aktivieren Sie diese Checkbox, wenn Sie EasyTheme verwenden möchten.');
$GLOBALS['TL_LANG']['tl_user']['et_mode']         	= array('EasyTheme Modus','Wählen Sie aus, ob Sie EasyTheme lieber als Kontextmenü, beim Mouseover oder als direkten DOM-Inject anzeigen möchten.');
$GLOBALS['TL_LANG']['tl_user']['et_short']    		= array('Kurzansicht aktivieren','Aktivieren Sie die Kurzansicht für EasyTheme.');
$GLOBALS['TL_LANG']['tl_user']['et_activeModules']	= array('Aktive Module','Wählen Sie hier die Module aus, die Sie angezeigt haben möchten.');
$GLOBALS['TL_LANG']['tl_user']['contextmenu']		= 'Kontextmenü';
$GLOBALS['TL_LANG']['tl_user']['mouseover']			= 'Mouseover';
$GLOBALS['TL_LANG']['tl_user']['inject']			= 'DOM-Inject';
$GLOBALS['TL_LANG']['tl_user']['be_mod']			= 'Backend Module';

/**
 * Legends
 */
$GLOBALS['TL_LANG']['tl_user']['et_legend'] = 'easy_themes';


/**
 * Error message for Widget
 */
$GLOBALS['TL_LANG']['tl_user']['chooseAtLeastOne'] = 'Sie müssen beim Feld "%s" mindestens eine Wahl treffen!';
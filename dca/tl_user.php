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
// replace palettes
foreach($GLOBALS['TL_DCA']['tl_user']['palettes'] as $palette =>$v)
{
	if($palette == '__selector__')
	{
		continue;
	}
    
    if(BackendUser::getInstance()->hasAccess('themes', 'modules'))
    {
        $GLOBALS['TL_DCA']['tl_user']['palettes'][$palette] = str_replace('oldBeTheme;','oldBeTheme;{EasyTheme_legend},enableEasyTheme;',$GLOBALS['TL_DCA']['tl_user']['palettes'][$palette]);        
    }
}

// extend selector
$GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'enableEasyTheme';

// extend subpalettes
$GLOBALS['TL_DCA']['tl_user']['subpalettes']['enableEasyTheme'] = 'showShortEasyTheme,EasyThemeMode,activeThemes,activeModules';

// add fields
$GLOBALS['TL_DCA']['tl_user']['fields']['enableEasyTheme'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['enableEasyTheme'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('submitOnChange'=>true, 'tl_class'=>'tl_checkbox_single_container')
);
$GLOBALS['TL_DCA']['tl_user']['fields']['showShortEasyTheme'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['showShortEasyTheme'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('tl_class'=>'w50 cbx m12')
);
$GLOBALS['TL_DCA']['tl_user']['fields']['EasyThemeMode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['EasyThemeMode'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('contextmenu','mouseover','inject'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_user'],
	'eval'					  => array('tl_class'=>'w50')
);
$GLOBALS['TL_DCA']['tl_user']['fields']['activeModules'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['activeModules'],
	'exclude'                 => true,
    'inputType'               => 'checkbox_minOne',
    'options_callback'        => array('EasyThemes', 'getThemeModules'),
	'eval'					  => array('multiple'=>true)
);
$GLOBALS['TL_DCA']['tl_user']['fields']['activeThemes'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['activeThemes'],
	'exclude'                 => true,
    'inputType'               => 'checkbox_minOne',
    'options_callback'        => array('EasyThemes', 'getThemes'),
	'eval'					  => array('multiple'=>true, 'tl_class'=>'clr'),
	'load_callback'			  => array(array('EasyThemes', 'checkOnUpdate'))
);

?>
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
// replace palettes
foreach($GLOBALS['TL_DCA']['tl_user']['palettes'] as $palette =>$v)
{
	if($palette == '__selector__')
	{
		continue;
	}
    
    if(BackendUser::getInstance()->hasAccess('themes', 'modules'))
    {      
    	$GLOBALS['TL_DCA']['tl_user']['palettes'][$palette] = str_replace('backendTheme;','backendTheme;{et_legend},et_enable;',$GLOBALS['TL_DCA']['tl_user']['palettes'][$palette]);
	}
}

// extend selector
$GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'et_enable';

// extend subpalettes
$GLOBALS['TL_DCA']['tl_user']['subpalettes']['et_enable'] = 'et_mode,et_short,et_activeModules';

// add fields
$GLOBALS['TL_DCA']['tl_user']['fields']['et_enable'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['et_enable'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('submitOnChange'=>true, 'tl_class'=>'tl_checkbox_single_container')
);
$GLOBALS['TL_DCA']['tl_user']['fields']['et_short'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['et_short'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'eval'					  => array('tl_class'=>'clr')
);
$GLOBALS['TL_DCA']['tl_user']['fields']['et_mode'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_user']['et_mode'],
	'exclude'                 => true,
	'inputType'               => 'select',
	'options'                 => array('contextmenu','mouseover','inject','be_mod'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_user'],
	'eval'					  => array('tl_class'=>'clr', 'submitOnChange'=>true)
);
$GLOBALS['TL_DCA']['tl_user']['fields']['et_activeModules'] = array
(
    'label'                   => &$GLOBALS['TL_LANG']['tl_user']['et_activeModules'],
	'exclude'                 => true,
    'inputType'               => 'checkbox_minOne',
    'options_callback'        => array('tl_user_easy_themes', 'getThemeModules'),
	'reference'               => &$GLOBALS['TL_LANG']['tl_user']['et_activeModules'],
	'eval'					  => array('multiple'=>true,'tl_class'=>'clr')
);

class tl_user_easy_themes extends Backend
{

    /**
     * Initialize the object and import EasyThemes
     */ 	
    public function __construct()
    {
        parent::__construct();
		$this->import('EasyThemes');
    }

	
    /**
     * Return an array of the theme modules with their corresponding language label
	 * @param DataContainer
     * @return array
     */
	public function getThemeModules(DataContainer $dc)
	{
		// if we don't have any themes at all this is going to be as empty as void
		$arrThemes = $this->EasyThemes->getThemes();
		if(!$arrThemes)
		{
			return array();
		}
		
		// build the modules array
		$this->loadLanguageFile('tl_theme');
		$arrModules  = array();
		foreach($GLOBALS['TL_EASY_THEMES_MODULES'] as $strModule => $arrModule)
		{
			if(isset($arrModule['label']))
			{
				$label = $arrModule['label'];
			}
			else
			{
				$label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];
			}
			
			$arrModules[$strModule] = $label;
		}

		// add the modules array to every theme
		$arrReturn = array();
		foreach($arrThemes as $intThemeId => $strThemeName)
		{
			foreach($arrModules as $strModule => $strLabel)
			{
				// add it to the array
				$arrReturn['theme_' . $intThemeId][specialchars($intThemeId . '::' . $strModule)] = $strLabel;
			}
					
			// add the label
			$GLOBALS['TL_LANG']['tl_user']['et_activeModules']['theme_' . $intThemeId] = $strThemeName;
		}
		return $arrReturn;
	}
}
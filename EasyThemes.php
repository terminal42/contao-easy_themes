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
 * Class EasyThemes
 *
 * Adds the container the JS is calling for to the backend template
 * @copyright  Yanick Witschi 2010
 * @author     Yanick Witschi <http://www.certo-net.ch>
 * @package    Backend
 */
class EasyThemes extends Backend
{

    /**
     * Initialize the object, import the user class and the tl_theme lang file
     */ 	
    public function __construct()
    {
        parent::__construct();
        $this->import('BackendUser', 'User');
    }
  
  
	/**
	 * Add CSS and Javascript
	 * @param string
	 * @return boolean
	 */	
	public function addHeadings($strName, $strLanguage)
	{
		if($this->User->enableEasyTheme == 1)
		{
			$strType = ($this->User->showShortEasyTheme == 1) ? 'Short' : 'Long';
			
			if($this->User->EasyThemeMode == 'inject')
			{
				$strType .= 'Inject';
			}
			
			$GLOBALS['TL_CSS']['EasyThemesHeadingsCSS'.$strType] = 'system/modules/easy_themes/html/EasyThemes'.$strType.'.css|screen';
			$GLOBALS['TL_JAVASCRIPT']['EasyThemesHeadingsJS'] = 'system/modules/easy_themes/html/EasyThemes_class.js';
		}
		
		// make sure the hook is only executed once
		unset($GLOBALS['TL_HOOKS']['loadLanguageFile']['EasyThemesHook']);
		
		return false;
	}
	
  
	/**
	 * Add the container
	 * @param string
	 * @param string
	 * @return string
	 */
	public function addContainer($strContent, $strTemplate)
	{
		if($strTemplate == 'be_main')
		{
			$strContent = str_replace('<div id="container">','<div id="container">'."\n".$this->generateContainerContent(), $strContent);
		}
		
		return $strContent;
	}
	
  
	/**
	 * Generate the container content
	 * @return string
	 */
	protected function generateContainerContent()
	{
        $objTemplate = new BackendTemplate('be_easythemes');
		
		// if the module is inactive we set the template var and stop it here
		if($this->User->enableEasyTheme != 1)
		{			
			$objTemplate->isActive = false;
			return $objTemplate->parse();
		}
		
		$objTemplate->isActive = true;
		
		// set mode ('contextmenu' or 'mouseover'), default ist contextmenu
		$objTemplate->mode = $this->User->EasyThemeMode;
		
		// get all the themes
		$arrThemes = $this->Database->query("SELECT id,name FROM tl_theme ORDER BY name")->fetchAllAssoc();

		// filter for the themes we don't want to show
		// if activeThemes is not set yet, we just show all themes - see #999
		$activeThemes = null;
		if($this->User->activeThemes)
		{
			$activeThemes = $this->User->activeThemes;
		}
		else
		{
			$arr = array();
			$arrAllThemes = $this->getThemes();
			
			foreach($arrAllThemes as $themeId => $themeName)
			{
				$arr[$themeId] = 1;
			}
			
			$activeThemes = $arr;
		}

		foreach($arrThemes as $k => $theme)
		{
			if(!in_array($theme[id], $activeThemes))
			{
				unset($arrThemes[$k]);
			}
		}
		
		if(!count($arrThemes))
		{
			$objTemplate->hasThemes = false;
			$objTemplate->noThemes = '<p>No theme added yet!</p>';
			return $objTemplate->parse();
		}
		
        $this->loadLanguageFile('tl_theme');
		$objTemplate->hasThemes = true;
 
        // check which theme modules the user wants to display
        // if the user hasn't stored any active modules yet we just show the edit button
        $activeModules = $this->User->activeModules;
        $arrModules = array_keys($GLOBALS['TL_EASY_THEMES_MODULES']);
        
        if(!is_array($activeModules) && count($activeModules) == 0)
        {
          $activeModules = $arrModules;
        }

        $activeModules = array_intersect($arrModules, $activeModules);
        
        // start the list and loop through all themes
        $strHTML = '  <ul class="easytheme_level1">' . "\n";
    
		foreach($arrThemes as $theme)
		{
            $strHTML .= '    <li class="easytheme_level_1_group">' . $theme['name'] . "\n";
            $strHTML .= '      <ul class="easytheme_level2">' . "\n";
      
            foreach($activeModules as $module)
            {
                $strHTML .= '        <li class="easytheme_level_2_link">' . "\n";
                
        		// $title - takes the given title from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][1]
				if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['title']))
				{
					$title = $GLOBALS['TL_EASY_THEMES_MODULES'][$module]['title'];
				}
				else
				{
					$title = sprintf($GLOBALS['TL_LANG']['tl_theme'][$module][1], $theme['id']);
				}

				// $label - takes the given label from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][0]
				if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['label']))
				{
					$label = ' ' . $GLOBALS['TL_EASY_THEMES_MODULES'][$module]['label'];
				}
				else
				{
					$label = ' ' . $GLOBALS['TL_LANG']['tl_theme'][$module][0];
				}

				// $href - also see the comments in config/config.php
				if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['href']))
				{
					$href = sprintf($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['href'], $theme['id']);
				}
				else if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['href_fragment']))
				{
					$href = $this->Environment->script . '?do=themes&amp;' . $GLOBALS['TL_EASY_THEMES_MODULES'][$module]['href_fragment'] . '&amp;id=' . $theme['id'];
				}
				else
				{
					$href = 'javascript:alert(\'No href_fragment or href is specified for this module!\');';
				}

				// $icon - takes the given icon from the TL_EASY_THEMES_MODULES array or by default uses the generateImage() method
				if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['icon']))
				{
					$img = $this->generateImage($GLOBALS['TL_EASY_THEMES_MODULES'][$module]['icon'], $label);
				}
				else
				{
					$img = $this->generateImage($module . '.gif', $label);
				}
        
                $strHTML .= '          <a title="' . $title . '" href="' . $href . '">' . $img . (($this->User->showShortEasyTheme == 1) ? '' : $label) . '</a>' . "\n";    
                $strHTML .= '        </li>' . "\n";  
            }
      
            $strHTML .= '      </ul>' . "\n";
            $strHTML .= '    </li>' . "\n";
        }
    
        $strHTML .= '  </ul>' . "\n";
		$objTemplate->themes = $strHTML;
		return $objTemplate->parse();
	}


    /**
     * Return an array of the theme modules with their corresponding language label
     * @return array
     */
	public function getThemeModules()
	{
		$this->loadLanguageFile('tl_theme');
		$arrReturn  = array();

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
			$arrReturn[$strModule] = $label;
		}
		
		return $arrReturn;
	}	
	

	/**
     * Return an array of all Themes available
     * @return array
     */
	public function getThemes()
	{
		$arrReturn;
		$objThemes = $this->Database->query("SELECT id,name FROM tl_theme ORDER BY name");
		while($objThemes->next())
		{
			$arrReturn[$objThemes->id] = $objThemes->name;
		}
		
		return $arrReturn;		
	}
	

	/**
	 * Set the GET-Param for the user id so the subpalette can work
	 * @param string
	 */
	public function setUser($strTable)
	{
		if ($strTable == 'tl_user' && $this->Input->get('do') == 'login')
		{
			$this->import('BackendUser', 'User');
			$this->Input->setGet('id', $this->User->id);
		}
	}
	
	
	/**
	 * Checks if there has been made a choice of themes already and if not, sets all themes by default
	 * @param mixed
	 * @param object
	 * @return
	 */
	public function checkOnUpdate($varValue, DataContainer $dc)
	{
		if(!$varValue)
		{
			$arr = array();
			$this->import('BackendUser', 'User');
			$arrAllThemes = $this->getThemes();
			
			foreach($arrAllThemes as $themeId => $themeName)
			{
				$arr[$themeId] = 1;
			}
			
			$varValue = $arr;
		}

		return $varValue;
	}
}

?>
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
 * Class EasyThemes
 *
 * Adds the container the JS is calling for to the backend template
 * @copyright  Yanick Witschi 2011
 * @author     Yanick Witschi <yanick.witschi@certo-net.ch>
 * @package    easy_themes
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
		if($this->User->et_enable == 1)
		{
			$GLOBALS['TL_CSS'][]		= 'system/modules/easy_themes/html/easy_themes.css|screen';
			$GLOBALS['TL_JAVASCRIPT'][]	= 'system/modules/easy_themes/html/easy_themes.js';
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
		// we disable easy_themes if:
		// - it has been disabled (what a luminary)
		// - there is no theme at all
		// - the user has no module activated at all
		$arrAllThemes	= $this->getThemes();
		$arrNavArray	= $this->prepareBackendNavigationArray();
		
		if($this->User->et_enable != 1 || !$arrAllThemes || !$arrNavArray)
		{			
			return '';
		}
		
		$objTemplate = new BackendTemplate('be_easythemes');
		$objTemplate->mode		= $this->User->et_mode;
		$objTemplate->short		= $this->User->et_short;
		$objTemplate->class		= 'easythemes_' . $this->User->et_mode . ' easythemes_' . ($this->User->et_short ? 'short' : 'long');
		$objTemplate->themes	= $arrNavArray;
		return $objTemplate->parse();
	}


	/**
     * Return an array of all Themes available
     * @return array|false
     */
	public function getThemes()
	{
		$objThemes = $this->Database->execute('SELECT id,name FROM tl_theme ORDER BY name');
		if(!$objThemes->numRows)
		{
			return false;
		}
		
		$arrReturn = array();
		
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
	 * Prepares an array for the backend navigation
	 * @param boolean
	 * @return array|false
	 */
	protected function prepareBackendNavigationArray($blnForceToShowLabel=false)
	{
        // check which theme modules the user wants to display
        // if the user hasn't stored any active modules yet we return false
        $arrActiveModules = $this->User->et_activeModules;
		if(!is_array($arrActiveModules))
		{
			return false;
		}
		
        $this->loadLanguageFile('tl_theme');
		$arrReturn = array();
		
		foreach($arrActiveModules as $strConfig)
		{
			$arrConfig = explode('::', $strConfig, 2);
			$intThemeId = (int) $arrConfig[0];
			$strModule = $arrConfig[1];
			
			// get the theme title
			$objTitle = $this->Database->prepare("SELECT name FROM tl_theme WHERE id=?")->execute($intThemeId);
			$arrReturn[$intThemeId]['label']	= $objTitle->name;
			$arrReturn[$intThemeId]['href']		= $this->Environment->script . '?do=themes&amp;act=edit&amp;id=' . $intThemeId;;
			
    		// $title - takes the given title from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][1]
			if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title']))
			{
				$title = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title'];
			}
			else
			{
				$title = sprintf($GLOBALS['TL_LANG']['tl_theme'][$strModule][1], $intThemeId);
			}

			// $label - takes the given label from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][0]
			if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label']))
			{
				$label = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label'];
			}
			else
			{
				$label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];
			}

			// $href - also see the comments in config/config.php
			if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href']))
			{
				$href = sprintf($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href'], $intThemeId);
			}
			else if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment']))
			{
				$href = $this->Environment->script . '?do=themes&amp;' . $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment'] . '&amp;id=' . $intThemeId;
			}
			else
			{
				$href = 'javascript:alert(\'No href_fragment or href is specified for this module!\');';
			}

			// $icon - takes the given icon from the TL_EASY_THEMES_MODULES array or by default uses the generateImage() method
			if(isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon']))
			{
				$img = $this->generateImage($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon'], $label);
				$imgOrgPath = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon'];
			}
			else
			{
				$img = $this->generateImage($strModule . '.gif', $label);
				$imgOrgPath = sprintf('system/themes/%s/images/%s', $this->getTheme(), $strModule . '.gif');
			}
			
			// add it to the array
			$arrReturn[$intThemeId]['modules'][$strModule] = array
			(
				'title'			=> $title,
				'href'			=> $href,
				'img'			=> $img,
				'imgOrgPath'	=> $imgOrgPath,
				'label'			=> (($this->User->et_short == 1 && !$blnForceToShowLabel) ? '' : $label)
			);
		}
		
		return $arrReturn;
	}
}
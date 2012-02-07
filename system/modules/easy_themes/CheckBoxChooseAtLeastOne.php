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
 * Class CheckBoxChooseAtLeastOne
 *
 * Checks if there has been at least one entry been chosen on a multiple checkbox wizard
 * @author     Yanick Witschi <yanick.witschi@certo-net.ch>
 * @package    easy_themes
 * @package    Backend
 */
class CheckBoxChooseAtLeastOne extends Checkbox
{

  /**
   * Add specific attributes
   * @param string
   * @param mixed
   */
  public function __set($strKey, $varValue)
  {
    parent::__set($strKey, $varValue);
  }


 /**
  * Generate the widget
  */
  public function generate()
  {
    return parent::generate();
  }


 /**
  * Check for the entry
  */
  public function validate()
  {
    parent::validate();
    
    if(!is_array($this->varValue) || count($this->varValue) < 1)
    {
      $this->addError(sprintf($GLOBALS['TL_LANG']['tl_user']['chooseAtLeastOne'], $this->strLabel));
    }
  }
}
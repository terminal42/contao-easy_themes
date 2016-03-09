<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (C) 2009 - 2016 terminal42 gmbh
 *
 * @package    easy_themes
 * @link       http://www.terminal42.ch
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
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

        if (!is_array($this->varValue) || count($this->varValue) < 1) {
            $this->addError(sprintf($GLOBALS['TL_LANG']['tl_user']['chooseAtLeastOne'], $this->strLabel));
        }
    }
}

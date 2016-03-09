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

class EasyThemesRunOnce extends Controller
{

    /**
     * Initialize the object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Database');
    }


    /**
     * Run the controller
     */
    public function run()
    {
        $this->updateTo130();
    }


    /**
     * Update to version 1.3.0
     */
    private function updateTo130()
    {
        // activeThemes has been deleted so version 1.3.0 is already running
        if (!$this->Database->fieldExists('activeThemes', 'tl_user')) {
            return;
        }

        // get the activeThemes and activeModules so we can merge them
        $objData = $this->Database->execute("SELECT id,activeThemes,activeModules FROM tl_user");

        // do the update for every user
        while ($objData->next()) {
            $arrActiveThemes = deserialize($objData->activeThemes, true);
            $arrActiveModules = deserialize($objData->activeModules, true);

            // no themes have ever been activated, the job is done for this user
            if (!count($arrActiveThemes)) {
                break;
            }

            // themes have been activated but no modules, the job is done for this user
            if (!count($arrActiveModules)) {
                break;
            }

            // both have been activated so we merge them together
            $arrNewActiveModules = array();
            foreach ($arrActiveThemes as $intThemeId) {
                foreach ($arrActiveModules as $strModule) {
                    $arrNewActiveModules[] = $intThemeId . '::' . $strModule;
                }
            }

            $arrSet = array();
            $arrSet['activeModules'] = $arrNewActiveModules;

            $this->Database->prepare("UPDATE tl_user %s WHERE id=?")
                ->set($arrSet)
                ->execute($objData->id);

        }

        // now we rename the database fields to be more consistent
        // tl_user.enableEasyTheme has been renamed to tl_user.et_enable
        if ($this->Database->fieldExists('enableEasyTheme', 'tl_user')) {
            $this->Database->query("ALTER TABLE tl_user CHANGE COLUMN enableEasyTheme et_enable char(1) NOT NULL default ''");
        }

        // tl_user.EasyThemeMode has been renamed to tl_user.et_mode
        if ($this->Database->fieldExists('EasyThemeMode', 'tl_user')) {
            $this->Database->query("ALTER TABLE tl_user CHANGE COLUMN EasyThemeMode et_mode varchar(32) NOT NULL default 'contextmenu'");
        }

        // tl_user.showShortEasyTheme has been renamed to tl_user.et_short
        if ($this->Database->fieldExists('EasyThemeMode', 'tl_user')) {
            $this->Database->query("ALTER TABLE tl_user CHANGE COLUMN showShortEasyTheme et_short char(1) NOT NULL default ''");
        }

        // tl_user.activeModules has been renamed to tl_user.et_activeModules
        if ($this->Database->fieldExists('EasyThemeMode', 'tl_user')) {
            $this->Database->query("ALTER TABLE tl_user CHANGE COLUMN activeModules et_activeModules blob NULL");
        }

        // finally drop the activeThemes column as it is no longer needed
        $this->Database->query("ALTER TABLE tl_user DROP COLUMN activeThemes");
    }
}


/**
 * Instantiate controller
 */
$objEasyThemesRunOnce = new EasyThemesRunOnce();
$objEasyThemesRunOnce->run();

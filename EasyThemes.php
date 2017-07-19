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

class EasyThemes extends Backend
{

    /**
     * Load ET
     * @var bool
     */
    private $blnLoadET = true;


    /**
     * Initialize the object, import the user class and the tl_theme lang file
     */
    public function __construct()
    {
        parent::__construct();

        // We never need to do anything at all if the user has no access to the themes module
        if (TL_MODE !== 'BE'
            || BackendUser::getInstance()->et_enable != 1
            || !BackendUser::getInstance()->hasAccess('themes', 'modules')
            || Input::get('popup')
        ) {
            $this->blnLoadET = false;
        } else {
            $GLOBALS['TL_CSS'][] = 'system/modules/easy_themes/html/easy_themes.css|screen';
            $GLOBALS['TL_JAVASCRIPT'][] = 'system/modules/easy_themes/html/easy_themes.js';
        }
    }

    /**
     * Add the container
     * @param string
     * @param string
     * @return string
     */
    public function addContainer($strContent, $strTemplate)
    {
        if (!$this->blnLoadET) {
            return $strContent;
        }

        if ($strTemplate == 'be_main') {
            $strContent = preg_replace('/(<div id="container"[^>]*>)/',
                '$1' . "\n" . $this->generateContainerContent(),
                $strContent,
                1);
        }

        return $strContent;
    }


    /**
     * Generate the container content
     * @return string
     */
    protected function generateContainerContent()
    {
        $user = BackendUser::getInstance();
        
        // we disable easy_themes if:
        // - it has been disabled (what a luminary)
        // - the mode is "be_mod"
        // - there is no theme at all
        // - the user has no module activated at all
        $arrAllThemes = $this->getAllThemes();
        $arrNavArray = $this->prepareBackendNavigationArray($this->isContao4());

        if ($user->et_mode == 'be_mod' || !$arrAllThemes || !$arrNavArray) {
            return '';
        }

        $classes = [];
        $classes[] = 'easythemes_' . BackendUser::getInstance()->et_mode;
        $classes[] = 'easythemes_' . (BackendUser::getInstance()->et_short ? 'short' : 'long');

        if ($this->isContao4()) {
            $classes[] = 'isContao4';
        }

        $objTemplate = new BackendTemplate('be_easythemes');
        $objTemplate->mode = BackendUser::getInstance()->et_mode;
        $objTemplate->short = BackendUser::getInstance()->et_short;
        $objTemplate->class =  implode(' ', $classes);
        $objTemplate->themes = $arrNavArray;
        $objTemplate->isContao4 = $this->isContao4();

        return $objTemplate->parse();
    }


    /**
     * Return an array of all Themes available
     * @return array|false
     */
    public function getAllThemes()
    {
        $objThemes = Database::getInstance()->execute('SELECT id,name FROM tl_theme ORDER BY name');
        if (!$objThemes->numRows) {
            return false;
        }

        $arrReturn = array();

        while ($objThemes->next()) {
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
        if ($strTable == 'tl_user' && Input::get('do') == 'login') {
            Input::setGet('id', BackendUser::getInstance()->id);
        }
    }

    /**
     * @param $objTemplate
     */
    public function addContaoVersionCssClass($objTemplate)
    {
        if ('be_main' === $objTemplate->getName() && $this->isContao4()) {

            $objTemplate->ua .= ' isContao4';
        }
    }


    /**
     * Prepares an array for the backend navigation
     * @param boolean
     * @return array|false
     */
    protected function prepareBackendNavigationArray($blnForceToShowLabel = false)
    {
        // check which theme modules the user wants to display
        // if the user hasn't stored any active modules yet we return false
        $arrActiveModules = BackendUser::getInstance()->et_activeModules;
        if (!is_array($arrActiveModules)) {
            return false;
        }

        System::loadLanguageFile('tl_theme');
        $arrReturn = array();

        foreach ($arrActiveModules as $strConfig) {
            $arrConfig = explode('::', $strConfig, 2);
            $intThemeId = (int)$arrConfig[0];
            $strModule = $arrConfig[1];

            // get the theme title
            $objTitle = Database::getInstance()->prepare('SELECT name,easy_themes_internalTitle FROM tl_theme WHERE id=?')->execute($intThemeId);
            $arrReturn[$intThemeId]['label'] = $objTitle->easy_themes_internalTitle ?: $objTitle->name;
            $arrReturn[$intThemeId]['href'] = TL_SCRIPT . '?do=themes&amp;act=edit&amp;id=' . $intThemeId . '&rt=' . REQUEST_TOKEN;

            // Append the module only if condition matches
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendIf'])) {
                if ($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendIf']($intThemeId) !== true) {
                    continue;
                }
            }

            // $title - takes the given title from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][1]
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title'])) {
                $title = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title'];
            } else {
                $title = sprintf($GLOBALS['TL_LANG']['tl_theme'][$strModule][1], $intThemeId);
            }

            // $label - takes the given label from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][0]
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label'])) {
                $label = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label'];
            } else {
                $label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];
            }

            // $href - also see the comments in config/config.php
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href'])) {
                $href = sprintf($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href'], $intThemeId);
            } else if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment'])) {
                $href = TL_SCRIPT . '?do=themes&amp;' . $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment'] . '&amp;id=' . $intThemeId;
            } else {
                $href = 'javascript:alert(\'No href_fragment or href is specified for this module!\');';
            }

            // $icon - takes the given icon from the TL_EASY_THEMES_MODULES array or by default uses the Image::getHtml() method
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon'])) {
                $path = str_replace('##backend_theme##', Backend::getTheme(), $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon']);
                $img = Image::getHtml($path, $label);
                $imgOrgPath = $path;
            } else {
                $extension = $this->isContao4() ? '.svg' : '.gif';
                $folder = $this->isContao4() ? 'icons' : 'images';

                $img = \Image::getHtml($strModule . $extension, $label);
                $imgOrgPath = sprintf('system/themes/%s/%s/%s', Backend::getTheme(), $folder, $strModule . $extension);
            }

            // request token
            if ($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendRT']) {
                $href .= ((strpos($href, '?') !== false) ? '&' : '?') . 'rt=' . REQUEST_TOKEN;
            }

            $themeIdToCompare = (int) \Input::get('id');
            if (($table = \Input::get('table')) && $themeIdToCompare) {
                \Controller::loadDataContainer($table);

                if (isset($GLOBALS['TL_DCA'][$table]['config']['ptable']) && 'tl_theme' === $GLOBALS['TL_DCA'][$table]['config']['ptable']) {
                    $themeIdToCompare =  (int) \Database::getInstance()->prepare('SELECT id FROM ' . $table . ' WHERE pid=?')->execute($themeIdToCompare);
                }
            }

            list(,$queryStringOfHref) = explode('?', str_replace('&amp;', '&', $href), 2);
            list(,$queryStringOfCurrent) = explode('?', \Environment::get('requestUri'), 2);

            $paramsOfHref = $this->getRelevantParametersFromQueryString($queryStringOfHref);
            $paramsOfCurrent = $this->getRelevantParametersFromQueryString($queryStringOfCurrent);

            // Adjust theme ID
            $paramsOfCurrent['id'] = (string) $themeIdToCompare;

            $isActive = $paramsOfHref === $paramsOfCurrent;

            // add it to the array
            $arrReturn[$intThemeId]['modules'][$strModule] = array
            (
                'title'         => $title,
                'href'          => $href,
                'img'           => $img,
                'imgOrgPath'    => $imgOrgPath,
                'label'         => ((BackendUser::getInstance()->et_short == 1 && !$blnForceToShowLabel) ? '' : $label),
                'isActive'      => $isActive,
            );
        }

        return $arrReturn;
    }


    /**
     * Modifies the user navigation
     * @param array the modules
     * @param boolean show all
     * @return array
     */
    public function modifyUserNavigation($arrModules, $blnShowAll)
    {
        if (!$this->blnLoadET) {

            return $arrModules;
        }

        // add some CSS classes to the design module
        $strClass = 'easy_themes_toggle ';

        $strClass .= ($arrModules['design']['icon'] == 'modPlus.gif' && !$this->isContao4()) ? 'easy_themes_collapsed' : 'easy_themes_expanded';

        $arrModules['design']['class'] = ' ' . trim($arrModules['design']['class']) . ((trim($arrModules['design']['class'])) ? ' ' : '') . $strClass;

        // mode 'navigation'
        if (BackendUser::getInstance()->et_mode != 'be_mod') {
            return $arrModules;
        }

        $arrThemes = $this->prepareBackendNavigationArray(true);

        if (!is_array($arrThemes) || empty($arrThemes)) {
            return $arrModules;
        }

        $session = $this->Session->getData();
        $arrThemeNavigation = array();
        foreach ($arrThemes as $intThemeId => $arrTheme) {
            $strKey = 'theme_' . $intThemeId;
            $blnOpen = (isset($session['backend_modules'][$strKey]) && $session['backend_modules'][$strKey]) || $blnShowAll || $this->isContao4();
            $arrThemeNavigation[$strKey]['icon'] = 'modMinus.gif';

            if ($this->isContao4()) {
                $arrThemeNavigation[$strKey]['class'] = ' easy_themes node-expanded';
            }

            $arrThemeNavigation[$strKey]['title'] = specialchars($GLOBALS['TL_LANG']['MSC']['collapseNode']);
            $arrThemeNavigation[$strKey]['label'] = specialchars($arrTheme['label']);
            $arrThemeNavigation[$strKey]['href'] = $this->addToUrl('mtg=' . $strKey);

            // Do not show the modules if the group is closed
            if (!$blnOpen) {
                $arrThemeNavigation[$strKey]['modules'] = false;
                $arrThemeNavigation[$strKey]['icon'] = 'modPlus.gif';

                if ($this->isContao4()) {
                    $arrThemeNavigation[$strKey]['class'] = ' easy_themes node-collapsed';
                }

                $arrThemeNavigation[$strKey]['title'] = specialchars($GLOBALS['TL_LANG']['MSC']['expandNode']);

                continue;
            }

            // now the theme modules
            if (is_array($arrTheme['modules']) && count($arrTheme['modules'])) {
                foreach ($arrTheme['modules'] as $strModuleName => $arrModule) {
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['title'] = specialchars($arrModule['title']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['label'] = specialchars($arrModule['label']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['icon'] = sprintf(' style="background-image:url(\'%s%s\')"', TL_SCRIPT_URL, $arrModule['imgOrgPath']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['class'] = 'navigation ' . $strModuleName;
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['href'] = $arrModule['href'];
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['isActive'] = $arrModule['isActive'];
                }
            }
        }

        if (BackendUser::getInstance()->et_bemodRef) {
            $intPosition = array_search(BackendUser::getInstance()->et_bemodRef, array_keys($arrModules));
            $intPosition++;
            array_insert($arrModules, $intPosition, $arrThemeNavigation);

            return $arrModules;
        }

        return array_merge($arrThemeNavigation, $arrModules);
    }


    /**
     * Removes a theme from the settings when a theme gets deleted
     * @param DataContainer
     */
    public function removeTheme(DataContainer $dc)
    {
        $objUser = Database::getInstance()->execute('SELECT id,et_enable,et_activeModules FROM tl_user');

        while ($objUser->next()) {
            // if the user doesn't use easy_themes, we skip
            if ($objUser->et_enable == '') {
                continue;
            }

            $arrModulesOld = deserialize($objUser->et_activeModules);
            $arrModulesNew = array();

            // if there's no data we skip
            if (!is_array($arrModulesOld) || !count($arrModulesOld)) {
                continue;
            }

            foreach ($arrModulesOld as $strConfig) {
                $arrChunks = explode('::', $strConfig);
                $intThemeID = (int)$arrChunks[0];

                // we only add it to the new array if it's NOT the one being deleted
                if ($intThemeID != $dc->id) {
                    $arrModulesNew[] = $strConfig;
                }
            }

            // update the database
            Database::getInstance()->prepare('UPDATE tl_user SET et_activeModules=? WHERE id=?')->execute(serialize($arrModulesNew), $objUser->id);
        }
    }

    /**
     * Checks if it is Contao4
     *
     * @return bool
     */
    private function isContao4()
    {
        return version_compare(VERSION, '4.4', '>=');
    }

    /**
     * Extract the relevant parameters from a query string.
     *
     * @param string $queryString
     *
     * @return array
     */
    private function getRelevantParametersFromQueryString($queryString)
    {
        parse_str($queryString, $params);

        foreach (array_keys($params) as $k) {
            if (!in_array($k, ['do', 'id', 'table'])) {
                unset($params[$k]);
            }

            if ($params['table'] === 'tl_theme') {
                unset($params['table']);
            }
        }

        ksort($params);

        return (array) $params;
    }
}

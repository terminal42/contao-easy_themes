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

/**
 * Table tl_user
 */
// modify palette
$GLOBALS['TL_DCA']['tl_user']['config']['onload_callback'][] = array('tl_user_easy_themes', 'buildPalette');


// add fields
$GLOBALS['TL_DCA']['tl_user']['fields']['et_enable'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['et_enable'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array('submitOnChange' => true, 'tl_class' => 'cbx'),
    'sql'       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_activeModules'] = array
(
    'label'            => &$GLOBALS['TL_LANG']['tl_user']['et_activeModules'],
    'exclude'          => true,
    'inputType'        => 'checkbox_minOne',
    'options_callback' => array('tl_user_easy_themes', 'getThemeModules'),
    'reference'        => &$GLOBALS['TL_LANG']['tl_user']['et_activeModules'],
    'eval'             => array('multiple' => true, 'tl_class' => 'clr'),
    'sql'              => "blob NULL",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_short'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['et_short'],
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array('tl_class' => 'clr'),
    'sql'       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_mode'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['et_mode'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array('contextmenu', 'mouseover', 'inject', 'be_mod'),
    'reference' => &$GLOBALS['TL_LANG']['tl_user'],
    'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
    'sql'       => "varchar(32) NOT NULL default 'contextmenu'",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_bemodRef'] = array
(
    'label'     => &$GLOBALS['TL_LANG']['tl_user']['et_bemodRef'],
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array_keys($GLOBALS['BE_MOD']),
    'reference' => &$GLOBALS['TL_LANG']['MOD'],
    'eval'      => array('tl_class' => 'w50', 'includeBlankOption' => true),
    'sql'       => "varchar(32) NOT NULL default ''",
);


class tl_user_easy_themes extends Backend
{
    /**
     * Build the palette string
     * @param DataContainer
     */
    public function buildPalette(DataContainer $dc)
    {
        $objUser = Database::getInstance()->prepare('SELECT * FROM tl_user WHERE id=?')->execute($dc->id);

        foreach ($GLOBALS['TL_DCA']['tl_user']['palettes'] as $palette => $v) {
            if ($palette == '__selector__') {
                continue;
            }

            if (BackendUser::getInstance()->hasAccess('themes', 'modules')) {
                $arrPalettes = explode(';', $v);
                $arrPalettes[] = '{et_legend},et_enable;';
                $GLOBALS['TL_DCA']['tl_user']['palettes'][$palette] = implode(';', $arrPalettes);
            }
        }

        // extend selector
        $GLOBALS['TL_DCA']['tl_user']['palettes']['__selector__'][] = 'et_enable';

        // extend subpalettes
        $strSubpalette = 'et_activeModules,et_mode';

        if ($objUser->et_mode == 'be_mod') {
            $strSubpalette .= ',et_bemodRef';
        } elseif (version_compare(VERSION, '4.4', '<')) {
            $strSubpalette .= ',et_short';
        }

        $GLOBALS['TL_DCA']['tl_user']['subpalettes']['et_enable'] = $strSubpalette;
    }


    /**
     * Return an array of the theme modules with their corresponding language label
     * @param DataContainer
     * @return array
     */
    public function getThemeModules(DataContainer $dc)
    {
        // if we don't have any themes at all this is going to be as empty as void
        $et = new EasyThemes();
        $arrThemes = $et->getAllThemes();
        if (!$arrThemes) {
            return array();
        }

        // build the modules array
        System::loadLanguageFile('tl_theme');
        $arrModules = array();
        foreach ($GLOBALS['TL_EASY_THEMES_MODULES'] as $strModule => $arrModule) {
            if (isset($arrModule['label'])) {
                $label = $arrModule['label'];
            } else {
                $label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];
            }

            $arrModules[$strModule] = $label;
        }

        // add the modules array to every theme
        $arrReturn = array();
        foreach ($arrThemes as $intThemeId => $strThemeName) {
            foreach ($arrModules as $strModule => $strLabel) {
                // Append the module only if condition matches
                if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendIf'])) {
                    if ($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendIf']($intThemeId) !== true) {
                        continue;
                    }
                }

                // add it to the array
                $arrReturn['theme_' . $intThemeId][specialchars($intThemeId . '::' . $strModule)] = $strLabel;
            }

            // add the label
            $GLOBALS['TL_LANG']['tl_user']['et_activeModules']['theme_' . $intThemeId] = $strThemeName;
        }
        return $arrReturn;
    }
}

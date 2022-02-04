<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Contao\StringUtil;
use Contao\System;
use Terminal42\EasyThemesBundle\Helper;

/**
 * @Callback(table="tl_user", target="fields.et_activeModules.options")
 */
class ThemeOptionsListener
{
    private Helper $easyThemes;

    public function __construct(Helper $easyThemes)
    {
        $this->easyThemes = $easyThemes;
    }

    public function __invoke(DataContainer $dc): array
    {
        // if we don't have any themes at all this is going to be as empty as void
        $arrThemes = $this->easyThemes->getAllThemes();

        if (empty($arrThemes)) {
            return [];
        }

        // build the modules array
        System::loadLanguageFile('tl_theme');
        $arrModules = [];

        foreach ($GLOBALS['TL_EASY_THEMES_MODULES'] as $strModule => $arrModule) {
            if (isset($arrModule['label'])) {
                $label = $arrModule['label'];
            } else {
                $label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];

                if (isset($GLOBALS['TL_LANG']['MOD'][$strModule])) {
                    $label = $GLOBALS['TL_LANG']['MOD'][$strModule];
                } elseif (isset($arrModule['href_fragment']) && preg_match('/table=([a-zA-Z_]+)/', $arrModule['href_fragment'], $matches) && isset($GLOBALS['TL_LANG']['MOD'][$matches[1]])) {
                    // Extract the table
                    $label = $GLOBALS['TL_LANG']['MOD'][$matches[1]];
                }
            }

            $arrModules[$strModule] = $label;
        }

        // add the modules array to every theme
        $arrReturn = [];

        foreach ($arrThemes as $intThemeId => $strThemeName) {
            foreach ($arrModules as $strModule => $strLabel) {
                // add it to the array
                $arrReturn['theme_'.$intThemeId][StringUtil::specialchars($intThemeId.'::'.$strModule)] = $strLabel;
            }

            // add the label
            $GLOBALS['TL_LANG']['tl_user']['et_activeModules']['theme_'.$intThemeId] = $strThemeName;
        }

        return $arrReturn;
    }
}

<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle\EventListener;

use Contao\ArrayUtil;
use Contao\Backend;
use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\StringUtil;
use Terminal42\EasyThemesBundle\Helper;

/**
 * @Hook("getUserNavigation")
 */
class UserNavigationListener
{
    private Helper $easyThemes;

    public function __construct(Helper $easyThemes)
    {
        $this->easyThemes = $easyThemes;
    }

    public function __invoke(array $arrModules)
    {
        if (!$this->easyThemes->isEnabled()) {
            return $arrModules;
        }

        // This will also load the javascript for BackendTemplateListener
        $GLOBALS['TL_CSS'][] = 'bundles/terminal42easythemes/easy_themes.css|screen';
        $GLOBALS['TL_JAVASCRIPT'][] = 'bundles/terminal42easythemes/easy_themes.js';

        // add some CSS classes to the design module
        $strClass = 'easy_themes_toggle easy_themes_expanded';
        $arrModules['design']['class'] = ' '.trim($arrModules['design']['class']).(trim($arrModules['design']['class']) ? ' ' : '').$strClass;

        if ('be_mod' !== $this->easyThemes->getCurrentMode()) {
            return $arrModules;
        }

        $arrThemes = $this->easyThemes->prepareBackendNavigationArray();

        if (!\is_array($arrThemes) || empty($arrThemes)) {
            return $arrModules;
        }

        $arrThemeNavigation = [];

        foreach ($arrThemes as $intThemeId => $arrTheme) {
            $strKey = 'theme_'.$intThemeId;
            $arrThemeNavigation[$strKey]['icon'] = 'modMinus.gif';
            $arrThemeNavigation[$strKey]['class'] = ' easy_themes node-expanded';

            $arrThemeNavigation[$strKey]['title'] = StringUtil::specialchars($GLOBALS['TL_LANG']['MSC']['collapseNode']);
            $arrThemeNavigation[$strKey]['label'] = StringUtil::specialchars($arrTheme['label']);
            $arrThemeNavigation[$strKey]['href'] = Backend::addToUrl('mtg='.$strKey);
            $arrThemeNavigation[$strKey]['href'] = StringUtil::ampersand($arrThemeNavigation[$strKey]['href'], false);

            // now the theme modules
            if (\is_array($arrTheme['modules']) && \count($arrTheme['modules'])) {
                foreach ($arrTheme['modules'] as $strModuleName => $arrModule) {
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['title'] = StringUtil::specialchars($arrModule['title']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['label'] = StringUtil::specialchars($arrModule['label']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['icon'] = sprintf(' style="background-image:url(\'%s\')"', $arrModule['imgOrgPath']);
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['class'] = 'navigation '.$strModuleName;
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['href'] = $arrModule['href'];
                    $arrThemeNavigation[$strKey]['modules'][$strModuleName]['isActive'] = $arrModule['isActive'];
                }
            }
        }

        if ($parentNavigation = $this->easyThemes->getParentNavigation()) {
            $intPosition = array_search($parentNavigation, array_keys($arrModules), true);
            ++$intPosition;
            ArrayUtil::arrayInsert($arrModules, $intPosition, $arrThemeNavigation);

            return $arrModules;
        }

        return array_merge($arrThemeNavigation, $arrModules);
    }
}

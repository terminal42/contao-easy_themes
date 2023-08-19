<?php

declare(strict_types=1);

namespace Terminal42\EasyThemesBundle;

use Contao\Backend;
use Contao\BackendUser;
use Contao\Controller;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\Image;
use Contao\Input;
use Contao\StringUtil;
use Contao\System;
use Doctrine\DBAL\Connection;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Security;

class Helper
{
    private Security $security;
    private RequestStack $requestStack;
    private ScopeMatcher $scopeMatcher;
    private Connection $connection;
    private RouterInterface $router;

    public function __construct(Security $security, RequestStack $requestStack, ScopeMatcher $scopeMatcher, Connection $connection, RouterInterface $router)
    {
        $this->security = $security;
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
        $this->connection = $connection;
        $this->router = $router;
    }

    public function isEnabled(string $mode = null): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $user = $this->security->getUser();

        return null !== $request
            && $user instanceof BackendUser
            && $user->et_enable
            && (null === $mode || $mode === $user->et_mode)
            && $this->scopeMatcher->isBackendRequest($request)
            && $user->hasAccess('themes', 'modules')
            && !Input::get('popup');
    }

    public function getCurrentMode(): string
    {
        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            return '';
        }

        return $user->et_mode;
    }

    public function getParentNavigation(): string
    {
        $user = $this->security->getUser();

        if (!$user instanceof BackendUser) {
            return '';
        }

        return $user->et_bemodRef;
    }

    /**
     * Return an array of all Themes available.
     */
    public function getAllThemes(): array
    {
        $themes = $this->connection->fetchAllAssociative('SELECT id, name FROM tl_theme ORDER BY name');

        return array_combine(array_column($themes, 'id'), array_column($themes, 'name'));
    }

    /**
     * Prepares an array for the backend navigation.
     *
     * @param bool
     *
     * @return array|false
     */
    public function prepareBackendNavigationArray()
    {
        $user = $this->security->getUser();

        // check which theme modules the user wants to display
        // if the user hasn't stored any active modules yet we return false
        if (!$user instanceof BackendUser || !\is_array($arrActiveModules = $user->et_activeModules)) {
            return false;
        }

        System::loadLanguageFile('tl_theme');
        $arrReturn = [];
        $requestToken = System::getContainer()->get('contao.csrf.token_manager')->getDefaultTokenValue();

        foreach ($arrActiveModules as $strConfig) {
            $arrConfig = explode('::', $strConfig, 2);
            $intThemeId = (int) $arrConfig[0];
            $strModule = $arrConfig[1];

            // check if module is existing
            if (!isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule])) {
                continue;
            }

            // get the theme title
            $arrTheme = $this->connection->fetchAssociative(
                'SELECT name, easy_themes_internalTitle FROM tl_theme WHERE id=?',
                [$intThemeId]
            );
            $arrReturn[$intThemeId]['label'] = $arrTheme['easy_themes_internalTitle'] ?: $arrTheme['name'];
            $arrReturn[$intThemeId]['href'] = $this->router->generate('contao_backend', [
                'do' => 'themes',
                'act' => 'edit',
                'id' => $intThemeId,
                'rt' => System::getContainer()->get('contao.csrf.token_manager')->getToken(System::getContainer()->getParameter('contao.csrf_token_name'))->getValue(),
            ]);

            $arrReturn[$intThemeId]['href'] = StringUtil::ampersand($arrReturn[$intThemeId]['href'], false);

            // $title - takes the given title from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][1]
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title'])) {
                $title = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['title'];
            } else {
                $title = \is_array($GLOBALS['TL_LANG']['tl_theme'][$strModule]) ? $GLOBALS['TL_LANG']['tl_theme'][$strModule][1] : $GLOBALS['TL_LANG']['tl_theme'][$strModule];
                $title = sprintf($title, $intThemeId);
            }

            // $label - takes the given label from the TL_EASY_THEMES_MODULES array or by default $GLOBALS['TL_LANG']['tl_theme']['...'][0]
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label'])) {
                $label = $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['label'];
            } else {
                $label = $GLOBALS['TL_LANG']['tl_theme'][$strModule][0];

                if (isset($GLOBALS['TL_LANG']['MOD'][$strModule])) {
                    $label = $GLOBALS['TL_LANG']['MOD'][$strModule];
                } elseif (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment']) && preg_match('/table=([a-zA-Z_]+)/', $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment'], $matches) && isset($GLOBALS['TL_LANG']['MOD'][$matches[1]])) {
                    // Extract the table
                    $label = $GLOBALS['TL_LANG']['MOD'][$matches[1]];
                }
            }

            // $href - also see the comments in config/config.php
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href'])) {
                $href = sprintf($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href'], $intThemeId);
            } else {
                if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment'])) {
                    $arrHrefFragment = explode('=', $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['href_fragment']);
                    $href = $this->router->generate('contao_backend', [
                        'do' => 'themes',
                        $arrHrefFragment[0] => $arrHrefFragment[1],
                        'id' => $intThemeId,
                    ]);
                } else {
                    $href = 'javascript:alert(\'No href_fragment or href is specified for this module!\');';
                }
            }

            $href = StringUtil::ampersand($href, false);

            // $icon - takes the given icon from the TL_EASY_THEMES_MODULES array or by default uses the Image::getHtml() method
            if (isset($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon'])) {
                $path = str_replace('##backend_theme##', Backend::getTheme(), $GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['icon']);
                $img = Image::getHtml($path, $label);
                $imgOrgPath = $path;
            } else {
                $img = Image::getHtml($strModule.'.svg', $label);
                $imgOrgPath = sprintf('system/themes/%s/icons/%s', Backend::getTheme(), $strModule.'.svg');
            }

            // request token
            if ($GLOBALS['TL_EASY_THEMES_MODULES'][$strModule]['appendRT'] ?? false) {
                $href .= (false !== strpos($href, '?') ? '&' : '?').'rt='.$requestToken;
            }

            $currentId = (int) Input::get('id');

            if ($currentId && ($table = Input::get('table')) && 'edit' === Input::get('act')) {
                Controller::loadDataContainer($table);

                if (isset($GLOBALS['TL_DCA'][$table]['config']['ptable']) && 'tl_theme' === $GLOBALS['TL_DCA'][$table]['config']['ptable']) {
                    $currentId = (int) $this->connection->fetchOne('SELECT pid FROM '.$table.' WHERE id=?', [$currentId]);
                }
            }

            [, $queryStringOfHref] = explode('?', str_replace('&amp;', '&', $href), 2);

            $paramsOfHref = $this->getRelevantParametersFromQueryString($queryStringOfHref);
            $paramsOfCurrent = $this->getRelevantParametersFromQueryString((string) $this->requestStack->getCurrentRequest()->getQueryString());

            // Adjust theme ID
            $paramsOfCurrent['id'] = (string) $currentId;

            $isActive = $paramsOfHref === $paramsOfCurrent;

            // add it to the array
            $arrReturn[$intThemeId]['modules'][$strModule] = [
                'title' => $title,
                'href' => $href,
                'img' => $img,
                'imgOrgPath' => $imgOrgPath,
                'label' => $label,
                'isActive' => $isActive,
            ];
        }

        return $arrReturn;
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
            if (!\in_array($k, ['do', 'id', 'table'], true)) {
                unset($params[$k]);
            }

            if ('tl_theme' === ($params['table'] ?? null)) {
                unset($params['table']);
            }
        }

        ksort($params);

        return (array) $params;
    }
}

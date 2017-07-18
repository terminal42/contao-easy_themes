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
 * Theme modules
 * EXAMPLE OF HOW YOU COULD EXTEND EASY_THEMES WITH YOUR OWN EXTENSION USING THE FOLLOWING GLOBALS ARRAY
 * $GLOBALS['TL_EASY_THEMES_MODULES']['my_module'] = array
 * (
 *     'title'         => 'My Module',
 *     'label'         => 'My Module',
 *     'href'          => 'main.php?do=my_module&theme=%s',
 *     'href_fragment' => 'table=tl_additional_source',
 *     'icon'          => 'system/modules/my_module/html/my_module_icon.png',
 *     'appendRT'      => true,
 *     'appendIf'      => function($intThemeId) {}
 * );
 *
 * title:            optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][1]
 * label:            optional, otherwise easy_themes uses $GLOBALS['TL_LANG']['tl_theme']['...'][0]
 * href:             optional, alternative to href_fragment, overwrites href_fragment!
 * href_fragment:    alternative to href, will be added to the url like this: main.php?do=themes&id=<theme id>
 * icon:             optional, if not given, easy_themes will try to load an icon using Controller::generateImage('my_module.gif', ...)
 * appendRT:         boolean, optional, if set to true, easy_themes will append the request token (&rt=<REQUEST_TOKEN>)
 * appendIf:         Closure, optional, the module will only be appended if the closure returns true
 */
$GLOBALS['TL_EASY_THEMES_MODULES'] = array_merge
(
    array
    (
        'edit' => array
        (
            'href_fragment' => 'act=edit',
            'appendRT' => true
        ),
        'css' => array
        (
            'href_fragment' => 'table=tl_style_sheet'
        ),
        'modules' => array
        (
            'href_fragment' => 'table=tl_module'
        ),
        'layout' => array
        (
            'href_fragment' => 'table=tl_layout'
        ),
        'imageSizes' => array
        (
            'href_fragment' => 'table=tl_image_size',
            'icon'          => 'system/themes/##backend_theme##/images/sizes.gif',
            'appendIf'      => function($themeId) {
                return version_compare(VERSION, '3.4', '>=');
            }
        )
    ),
    is_array($GLOBALS['TL_EASY_THEMES_MODULES']) ? $GLOBALS['TL_EASY_THEMES_MODULES'] : array()
);


/**
 * Hooks
 */
// fix uninstall exception - see #756
// fix database error - see #822
// fix install exception - see #4
$repositoryManager = $_GET['do'] == 'repository_manager' && $_GET['update'] == 'database';
$installTool = strpos($_SERVER['PHP_SELF'], 'contao/install.php') !== false;
$composer = $_GET['do'] == 'composer' && $_GET['update'] == 'database';
$beMode = TL_MODE == 'BE';

if ($beMode
    && !$repositoryManager
    && !$installTool
    && !$composer
) {
    $GLOBALS['TL_HOOKS']['parseBackendTemplate'][] = array('EasyThemes', 'addContainer');
    $GLOBALS['TL_HOOKS']['getUserNavigation'][] = array('EasyThemes', 'modifyUserNavigation');
    $GLOBALS['TL_HOOKS']['loadDataContainer'][] = array('EasyThemes', 'setUser');
    $GLOBALS['TL_HOOKS']['parseTemplate'][] = array('EasyThemes', 'addContaoVersionCssClass');
}


/**
 * Backend form fields
 */
$GLOBALS['BE_FFL']['checkbox_minOne'] = 'CheckBoxChooseAtLeastOne';

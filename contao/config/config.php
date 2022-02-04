<?php

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
$GLOBALS['TL_EASY_THEMES_MODULES'] = $GLOBALS['TL_EASY_THEMES_MODULES'] ?? [];
$GLOBALS['TL_EASY_THEMES_MODULES'] += [
    'edit' => [
        'label' => &$GLOBALS['TL_LANG']['EASY_THEMES']['edit'],
        'href_fragment' => 'act=edit',
        'appendRT' => true,
    ],
    'css' => [
        'href_fragment' => 'table=tl_style_sheet',
    ],
    'modules' => [
        'href_fragment' => 'table=tl_module',
    ],
    'layout' => [
        'href_fragment' => 'table=tl_layout',
    ],
    'imageSizes' => [
        'href_fragment' => 'table=tl_image_size',
        'icon' => 'system/themes/##backend_theme##/images/sizes.gif',
    ],
];

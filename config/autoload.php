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
 * Register the classes
 */
ClassLoader::addClasses(array
(
    // Easy_themes
    'CheckBoxChooseAtLeastOne' => 'system/modules/easy_themes/CheckBoxChooseAtLeastOne.php',
    'EasyThemes' => 'system/modules/easy_themes/EasyThemes.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
    'be_easythemes' => 'system/modules/easy_themes/templates',
));

<?php

/**
 * Contao Open Source CMS
 *
 * Copyright (C) 2005-2012 Leo Feyer
 *
 * @package Easy_themes
 * @link    http://www.contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
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

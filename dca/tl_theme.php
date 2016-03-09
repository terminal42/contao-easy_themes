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
 * Table tl_theme
 */
$GLOBALS['TL_DCA']['tl_theme']['config']['ondelete_callback'][] = array('EasyThemes', 'removeTheme');
$GLOBALS['TL_DCA']['tl_theme']['palettes']['default'] = str_replace(
    'author',
    'author,easy_themes_internalTitle',
    $GLOBALS['TL_DCA']['tl_theme']['palettes']['default']
);

/**
 * Fields
 */
$GLOBALS['TL_DCA']['tl_theme']['fields']['easy_themes_internalTitle'] = array(
    'label'                   => &$GLOBALS['TL_LANG']['tl_theme']['easy_themes_internalTitle'],
    'inputType'               => 'text',
    'exclude'                 => true,
    'search'                  => true,
    'eval'                    => array('maxlength'=>128, 'tl_class'=>'w50'),
    'sql'                     => "varchar(128) NOT NULL default ''"
);

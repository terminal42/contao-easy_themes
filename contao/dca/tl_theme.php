<?php

/**
 * Table tl_theme
 */
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

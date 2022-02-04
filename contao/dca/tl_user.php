<?php

$GLOBALS['TL_DCA']['tl_user']['fields']['et_enable'] = array
(
    'exclude'   => true,
    'inputType' => 'checkbox',
    'eval'      => array('submitOnChange' => true, 'tl_class' => 'clr'),
    'sql'       => "char(1) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_activeModules'] = array
(
    'exclude'          => true,
    'inputType'        => 'checkbox',
    'reference'        => &$GLOBALS['TL_LANG']['tl_user']['et_activeModules'],
    'eval'             => array('mandatory' => true, 'multiple' => true, 'tl_class' => 'clr'),
    'sql'              => 'blob NULL',
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_mode'] = array
(
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array('contextmenu', 'mouseover', 'inject', 'be_mod'),
    'reference' => &$GLOBALS['TL_LANG']['tl_user'],
    'eval'      => array('tl_class' => 'w50', 'submitOnChange' => true),
    'sql'       => "varchar(32) NOT NULL default 'contextmenu'",
);

$GLOBALS['TL_DCA']['tl_user']['fields']['et_bemodRef'] = array
(
    'exclude'   => true,
    'inputType' => 'select',
    'options'   => array_keys($GLOBALS['BE_MOD']),
    'reference' => &$GLOBALS['TL_LANG']['MOD'],
    'eval'      => array('tl_class' => 'w50', 'includeBlankOption' => true),
    'sql'       => "varchar(32) NOT NULL default ''",
);

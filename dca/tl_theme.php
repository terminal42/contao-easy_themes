<?php

/**
 * Extension for Contao Open Source CMS
 *
 * Copyright (C) 2009 - 2013 terminal42 gmbh
 *
 * @package    easy_themes
 * @link       http://www.terminal42.ch
 * @license    http://opensource.org/licenses/lgpl-3.0.html LGPL
 */

/**
 * Table tl_theme
 */
$GLOBALS['TL_DCA']['tl_theme']['config']['ondelete_callback'][] = array('EasyThemes', 'removeTheme');
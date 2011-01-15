-- **********************************************************
-- *                                                        *
-- * IMPORTANT NOTE                                         *
-- *                                                        *
-- * Do not import this file manually but use the Contao    *
-- * install tool to create and maintain database tables!   *
-- *                                                        *
-- **********************************************************


-- --------------------------------------------------------

-- 
-- Table `tl_user`
-- 

CREATE TABLE `tl_user` (
  `enableEasyTheme` char(1) NOT NULL default '',
  `EasyThemeMode` varchar(32) NOT NULL default 'contextmenu',
  `showShortEasyTheme` char(1) NOT NULL default '',
  `activeModules` blob NULL,  
  `activeThemes` blob NULL,  
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
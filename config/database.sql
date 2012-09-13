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
  `et_enable` char(1) NOT NULL default '',
  `et_mode` varchar(32) NOT NULL default 'contextmenu',
  `et_short` char(1) NOT NULL default '',
  `et_activeModules` blob NULL,
  `et_bemodRef` varchar(32) NOT NULL default '',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
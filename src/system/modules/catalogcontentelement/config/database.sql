-- 
-- Table `tl_catalog_ce`
-- 

CREATE TABLE `tl_catalog_ce` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `pid` int(10) unsigned NOT NULL default '0',
  `tstamp` int(10) unsigned NOT NULL default '0',
  `name` varchar(64) NOT NULL default '',
  `catalog` int(10) unsigned NOT NULL default '0',
  `catalog_display_ce` varchar(64) NOT NULL default '',
  `catalog_template` varchar(64) NOT NULL default '',
  `catalog_layout` varchar(64) NOT NULL default '',
  `catalog_visible` blob NULL,
  `catalog_link_override` char(1) NOT NULL default '',
  `catalog_islink` blob NULL,
  `catalog_link_window` char(1) NOT NULL default '',
  `catalog_thumbnails_override` char(1) NOT NULL default '',
  `catalog_imagemain_field` varchar(64) NOT NULL default '',
  `catalog_imagemain_size` varchar(255) NOT NULL default '',
  `catalog_imagemain_fullsize` char(1) NOT NULL default '',
  `catalog_imagegallery_field` varchar(64) NOT NULL default '',
  `catalog_imagegallery_size` varchar(255) NOT NULL default '',
  `catalog_imagegallery_fullsize` char(1) NOT NULL default '',
-- catalog conditions
  `catalog_where` text NULL,
  `catalog_order` text NULL,
  PRIMARY KEY  (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- 
-- Table `tl_content`
-- 

CREATE TABLE `tl_content` (
  `cat_cetemplate` int(11) NOT NULL default '0',
  `cat_item` int(11) NOT NULL default '0',
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


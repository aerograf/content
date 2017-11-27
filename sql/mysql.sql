#
# Table structure for table `content`
#
#Create Table

CREATE TABLE content (
  `storyid` int(8) NOT NULL auto_increment,
  `parent_id` int(8) NOT NULL default '0',
  `blockid` int(8) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  `ptitle` varchar(255) default NULL,
  `text` longtext,
  `keywords` longtext,
  `page_description` longtext,
  `visible` tinyint(1) NOT NULL default '0',
  `homepage` tinyint(1) NOT NULL default '0',
  `epage` tinyint(1) default '0',
  `nohtml` tinyint(1) NOT NULL default '0',
  `nosmiley` tinyint(1) NOT NULL default '0',
  `nobreaks` tinyint(1) NOT NULL default '0',
  `nocomments` tinyint(1) NOT NULL default '0',
  `link` tinyint(1) NOT NULL default '0',
  `address` varchar(255) default NULL,
  `submenu` tinyint(1) NOT NULL default '0',
  `newwindow` tinyint(1) NOT NULL default '0',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `assoc_module` int(8) unsigned default NULL,
  `header_img` varchar(255) default NULL,
  PRIMARY KEY  (`storyid`),
  KEY `title` (`title`(40))
) ENGINE=MyISAM;
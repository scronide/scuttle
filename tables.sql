-- 
-- Table structure for table `sc_bookmarks`
-- 

CREATE TABLE `sc_bookmarks` (
  `bId` int(11) NOT NULL auto_increment,
  `uId` int(11) NOT NULL default '0',
  `bIp` varchar(40) default NULL,
  `bStatus` tinyint(1) NOT NULL default '0',
  `bDatetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `bModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `bTitle` varchar(255) NOT NULL default '',
  `bAddress` text NOT NULL,
  `bDescription` varchar(255) default NULL,
  `bHash` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`bId`),
  KEY `sc_bookmarks_usd` (`uId`,`bStatus`,`bDatetime`),
  KEY `sc_bookmarks_hui` (`bHash`,`uId`,`bId`),
  KEY `sc_bookmarks_du` (`bDatetime`,`uId`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `sc_tags`
-- 

CREATE TABLE `sc_tags` (
  `id` int(11) NOT NULL auto_increment,
  `bId` int(11) NOT NULL default '0',
  `tag` varchar(32) NOT NULL default '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `sc_tags_tag_bId` (`tag`,`bId`),
  KEY `sc_tags_bId` (`bId`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `scUsers`
-- 

CREATE TABLE `sc_users` (
  `uId` int(11) NOT NULL auto_increment,
  `username` varchar(25) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `uDatetime` datetime NOT NULL default '0000-00-00 00:00:00',
  `uModified` datetime NOT NULL default '0000-00-00 00:00:00',
  `name` varchar(50) default NULL,
  `email` varchar(50) NOT NULL default '',
  `homepage` varchar(255) default NULL,
  `uContent` text,
  PRIMARY KEY  (`uId`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `sc_watched`
-- 

CREATE TABLE `sc_watched` (
  `wId` int(11) NOT NULL auto_increment,
  `uId` int(11) NOT NULL default '0',
  `watched` int(11) NOT NULL default '0',
  PRIMARY KEY  (`wId`),
  KEY `sc_watched_uId` (`uId`)
);
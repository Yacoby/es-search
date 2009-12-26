SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- --------------------------------------------------------
DROP TABLE IF EXISTS CookieJar;
CREATE TABLE IF NOT EXISTS CookieJar (
  `Domain` varchar(255) NOT NULL,
  `Name` varchar(255) NOT NULL,
  `Value` text NOT NULL,
  `Expires` int,
  `Path` text NOT NULL,
  `Secure` tinyint NOT NULL,
  PRIMARY KEY  (`Domain`,`Name`),
  KEY `Domain` (`Domain`),
  KEY `Expires` (`Expires`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS ErrorLog;
CREATE TABLE IF NOT EXISTS ErrorLog (
  ID int(11) NOT NULL auto_increment,
  Level int(11) NOT NULL,
  Message text NOT NULL,
  PRIMARY KEY  (ID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS ModLocation;
CREATE TABLE IF NOT EXISTS ModLocation (
  ModID int(11) unsigned NOT NULL,
  URL varchar(512) NOT NULL,
  Version varchar(16) NOT NULL,
  Description text NOT NULL,
  Category varchar(64) NOT NULL,
  PRIMARY KEY  (`ModID`,`URL`),
  KEY ModID (ModID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS Mods;
CREATE TABLE IF NOT EXISTS Mods (
  ModID int(11) unsigned NOT NULL,
  `Name` varchar(256) NOT NULL,
  Author varchar(256) NOT NULL,
  Game enum('OB','MW','UN') NOT NULL,
  PRIMARY KEY  (ModID)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS VisitedPage;
CREATE TABLE IF NOT EXISTS VisitedPage (
  HostName varchar(64) NOT NULL,
  URL varchar(512) NOT NULL,
  LastVisited int(10) unsigned NOT NULL default '0' COMMENT 'Time',
  NeedRevisit int(10) unsigned NOT NULL default '0' COMMENT 'TimeFlagged',
  PRIMARY KEY  (URL)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS Website;
CREATE TABLE IF NOT EXISTS Website (
  HostName varchar(64) NOT NULL,
  ByteLimit int(10) unsigned NOT NULL default '0',
  BytesUsed int(10) unsigned NOT NULL default '0',
  BytesLastUpdated int(10) unsigned NOT NULL default '0' COMMENT 'Time',
  NextUpdate int(11) NOT NULL default '0',
  Enabled TINYINT NOT NULL default '1',
  PRIMARY KEY  (HostName)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

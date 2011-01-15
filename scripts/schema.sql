--NO LONGER USED
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
-- --------------------------------------------------------

DROP TABLE IF EXISTS ModLocation;
DROP TABLE IF EXISTS `Mod`;
DROP TABLE IF EXISTS ModCategory;
DROP TABLE IF EXISTS Page;
DROP TABLE IF EXISTS SearchIndexVersion;
DROP TABLE IF EXISTS SearchIndexChanges;
-- --------------------------------------------------------
DROP TABLE IF EXISTS CookieJar;
CREATE TABLE IF NOT EXISTS CookieJar (
	`Domain` varchar(255) NOT NULL,
	`Data` BLOB NOT NULL,
	PRIMARY KEY  (`Domain`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS ErrorLog;
CREATE TABLE IF NOT EXISTS ErrorLog (
	`Id` int(11) NOT NULL auto_increment,
	`Level` int(11) NOT NULL,
	`Message` text NOT NULL,
	PRIMARY KEY  (ID)
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS ModSite;
CREATE TABLE IF NOT EXISTS ModSite(
	ModSiteId INT NOT NULL auto_increment,

	HostName varchar(1024),
	BaseDomain varchar(1024) COMMENT 'e.g. http://yacoby.silgrad.com',
	ModUrlPrefix varchar(1024) COMMENT 'e.g. /MW/Mods/',

	ByteLimit INT unsigned NOT NULL default '0',
	BytesUsed INT unsigned NOT NULL default '0',
	BytesLastUpdated INT unsigned NOT NULL default '0' COMMENT 'Time',
	NextUpdate INT NOT NULL default '0',

	Enabled TINYINT NOT NULL default '1',

	PRIMARY KEY (ModSiteId),
	KEY (HostName) -- Don't know if this is needed. Only cron job tends to use HostName
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS ModCategory;
CREATE TABLE IF NOT EXISTS ModCategory(
	CategoryId INT NOT NULL auto_increment,
	CategoryName varchar(256) NOT NULL,
	PRIMARY KEY(CategoryId),
	KEY CategoryName (CategoryName)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- This ensures that the ModLocations default category points to something
INSERT INTO ModCategory (CategoryId, CategoryName) VALUES (0, 'Unknown');
-- --------------------------------------------------------
DROP TABLE IF EXISTS `Mod`;
CREATE TABLE IF NOT EXISTS `Mod` (
	ModId int(11) unsigned NOT NULL,
	`Name` varchar(1024) NOT NULL,
	Author varchar(1024) NOT NULL,
	GameId INT UNSIGNED NOT NULL default '0',
	PRIMARY KEY  (ModId)
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS `Game`;
CREATE TABLE IF NOT EXISTS `Game` (
	GameId int(11) unsigned NOT NULL,
    ShortName char(2) NOT NULL,
    `Name` varchar(64) NOT NULL,
	PRIMARY KEY  (GameId),
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS `GameMap`;
CREATE TABLE IF NOT EXISTS `GameMap` (
	Id int(11) unsigned NOT NULL,
	ModId int(11) unsigned NOT NULL,
	GameId int(11) unsigned NOT NULL,
	PRIMARY KEY  (Id),
    FOREIGN KEY (ModId) REFERENCES `Mod`(ModId),
    FOREIGN KEY (GameId) REFERENCES `Game`(GameId)
) ENGINE=INNODB DEFAULT CHARSET=latin1;
-- --------------------------------------------------------
DROP TABLE IF EXISTS ModLocation;
CREATE TABLE IF NOT EXISTS ModLocation (
	ModId int(11) unsigned NOT NULL,
	UrlSuffix varchar(512) NOT NULL COMMENT 'The last part of the URL. The main bit is held in ModSite.BaseDomain and ModSite.ModUrlPrefix',
	Version varchar(128) NOT NULL,
	IntVersion INT NOT NULL DEFAULT '0',
	Description text NOT NULL,
	ModSiteId INT NOT NULL,
	CategoryId INT NOT NULL DEFAULT '0',
	PRIMARY KEY  (`ModID`,`UrlSuffix`),
	KEY ModId (ModId),
    FOREIGN KEY (CategoryId) REFERENCES `ModCategory`(CategoryId),
    FOREIGN KEY (ModId) REFERENCES `Mod`(ModId) ON DELETE CASCADE,
    FOREIGN KEY (ModSiteId) REFERENCES `ModSite`(ModSiteId) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS Page;
CREATE TABLE IF NOT EXISTS Page (
	ModSiteId INT NOT NULL,
	UrlPath varchar(512) NOT NULL COMMENT 'The url excluding the domain (found in ModSite.BaseDomain)',
	LastVisited int unsigned NOT NULL default '0' COMMENT 'Time',
	Revisit int unsigned NOT NULL default '0' COMMENT 'TimeFlagged',
	PRIMARY KEY  (UrlPath),
    FOREIGN KEY (ModSiteId) REFERENCES `ModSite`(ModSiteId) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS RecentSearch;
CREATE TABLE IF NOT EXISTS RecentSearch (
	Ip varchar(64) NOT NULL,
	`Time` int NOT NULL,
	`General` text NOT NULL default '',
	`Name` text NOT NULL default '',
	`Author` text NOT NULL default '',
	`Description` text NOT NULL default '',
	PRIMARY KEY (`Ip`),
	KEY (`Time`)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS SearchIndex;
CREATE TABLE IF NOT EXISTS SearchIndex(
	IndexId INT NOT NULL auto_increment,
	Game INT NOT NULL,
	Mode varchar(1024) NOT NULL,
	PRIMARY KEY (IndexId)
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
DROP TABLE IF EXISTS SearchIndexVersion;
CREATE TABLE IF NOT EXISTS SearchIndexVersion(
	IndexId INT NOT NULL,
	IndexVersion INT NOT NULL,
	PRIMARY KEY (IndexId, IndexVersion),
    FOREIGN KEY (IndexId) REFERENCES `SearchIndex`(IndexId) ON DELETE CASCADE
) ENGINE=INNODB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------
-- DROP TABLE IF EXISTS SearchIndexChanges;
-- CREATE TABLE IF NOT EXISTS SearchIndexChanges(
--	UpdateId INT NOT NULL,
--	IndexId INT NOT NULL,
--	IndexVersion INT NOT NULL,
--	`Action` ENUM('ADD', 'REMOVE') NOT NULL,
--	ModId INT COMMENT 'NULL if the mod doesn\'t exist',
--	PRIMARY KEY (UpdateId),
--    FOREIGN KEY (IndexId) REFERENCES `SearchIndex`(IndexId) ON DELETE CASCADE,
--    FOREIGN KEY (IndexVersion) REFERENCES `SearchIndexVersion`(IndexVersion) ON DELETE CASCADE,
--    FOREIGN KEY (ModId) REFERENCES `Mod`(ModId) ON DELETE SET NULL
-- ) ENGINE=INNODB DEFAULT CHARSET=latin1;
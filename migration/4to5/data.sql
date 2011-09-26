-- This is written for mysql, it doesn't totally conver the data but it puts
-- it into a format that can be exported and then reinserted (this is fine
-- as I am moving to pgsql)

-- -----------------------------------------------------------------------------
-- Game has moved from many to many to one to many
ALTER TABLE modification
  ADD COLUMN game_id int NOT NULL;

UPDATE modification m
LEFT JOIN game_mods g ON
    m.id = g.modification_id
SET
    m.game_id = g.game_id;

-- ----------------------------------------------------------------------------

-- This is now obsolete
DROP TABLE `game_mods`;

-- These can be recreated as the data is not imporatnt
DROP TABLE `history_banned`;
DROP TABLE `error_log`;
DROP TABLE `search_history`;
DROP TABLE `aditional_mods`;

-- ----------------------------------------------------------------------------
-- Create a mod source table from the sites table
CREATE TABLE mod_source LIKE site;

INSERT mod_source SELECT * FROM site;

ALTER TABLE  `mod_source` ADD  `url_prefix` VARCHAR( 512 ) NOT NULL ,
  ADD  `search` TINYINT NOT NULL ,
  ADD  `scrape` TINYINT NOT NULL;

UPDATE `mod_source` SET `url_prefix` = CONCAT(`base_url`,`mod_url_prefix`);

ALTER TABLE `mod_source`
  DROP `host`,
  DROP `base_url`,
  DROP `mod_url_prefix`,
  DROP `byte_limit`,
  DROP `bytes_used`,
  DROP `bytes_last_updated`,
  DROP `next_update`,
  DROP `enabled`;

-- ----------------------------------------------------------------------------
-- convert the site table, keeping the data not in the mod source table

RENAME TABLE  `site` TO  `byte_limited_source` ;

ALTER TABLE  `byte_limited_source` ADD  `mod_source_id` INT NOT NULL;

UPDATE `byte_limited_source` SET `mod_source_id` = `id`;

ALTER TABLE  `byte_limited_source`
  CHANGE  `mod_url_prefix`  `url_prefix` TEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL;

ALTER TABLE  `byte_limited_source` DROP  `enabled`;

-- ----------------------------------------------------------------------------
-- convert the page table

ALTER TABLE  `page`
  ADD  `id` INT NOT NULL;

UPDATE `page` SET `id`=`site_id`;
ALTER TABLE  `page` DROP  `id`
ALTER TABLE  `page` DROP PRIMARY KEY
ALTER TABLE  `page` ADD  `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST


ALTER TABLE  `page`
  CHANGE  `site_id`  `byte_limited_source_id` BIGINT( 20 ) NOT NULL DEFAULT  '0';

-- ----------------------------------------------------------------------------
-- Convert location table

ALTER TABLE  `location`
  CHANGE  `mod_url_suffix` `url_suffix` VARCHAR( 255 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL DEFAULT  '',
  CHANGE  `site_id`  `mod_source_id` BIGINT( 20 ) NULL DEFAULT NULL;


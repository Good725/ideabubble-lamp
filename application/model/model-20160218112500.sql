/*
ts:2016-02-18 11:25:00
*/

CREATE TABLE IF NOT EXISTS `engine_countries`
(
  `id`  INT AUTO_INCREMENT PRIMARY KEY,
  `name`  VARCHAR(127) NOT NULL,
  created DATETIME NOT NULL,
  created_by INT NOT NULL,
  updated DATETIME NOT NULL,
  updated_by INT NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = InnoDB
CHARSET = UTF8;

INSERT IGNORE INTO `engine_countries`
SELECT 1,'Republic of Ireland',CURRENT_TIMESTAMP,users.id,CURRENT_TIMESTAMP,users.id,0,1
FROM `users` WHERE users.email= 'super@ideabubble.ie';

INSERT IGNORE INTO `engine_countries`
SELECT 2,'Northern Ireland',CURRENT_TIMESTAMP,users.id,CURRENT_TIMESTAMP,users.id,0,1
FROM `users` WHERE users.email= 'super@ideabubble.ie';

INSERT IGNORE INTO `engine_countries`
SELECT 3,'Great Britain',users.id,users.id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1
FROM `users` WHERE users.email= 'super@ideabubble.ie';

INSERT IGNORE INTO `engine_countries`
SELECT 4,'Spain',users.id,users.id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1
FROM `users` WHERE users.email= 'super@ideabubble.ie';

INSERT IGNORE INTO `engine_countries`
SELECT 5,'France',users.id,users.id,CURRENT_TIMESTAMP,CURRENT_TIMESTAMP,0,1
FROM `users` WHERE users.email= 'super@ideabubble.ie';

ALTER IGNORE TABLE `engine_counties` ADD COLUMN `country_id` INT NOT NULL DEFAULT 0 AFTER `region`;

UPDATE `engine_counties` SET `country_id` = `region`+1;
/*
ts:2016-03-23 12:33:00
*/

CREATE TABLE `plugin_propman_ipn_logs`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `get` MEDIUMTEXT,
  `post` MEDIUMTEXT,
  `cookies` TEXT,
  `time` DATETIME,
  `ip` VARCHAR(16)
)
ENGINE = INNODB
CHARSET = UTF8;

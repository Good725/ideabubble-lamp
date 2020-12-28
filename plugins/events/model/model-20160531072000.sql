/*
ts:2016-05-31 07:20:00
*/

CREATE TABLE `plugin_events_organizers`
(
  contact_id  INT PRIMARY KEY,
  url VARCHAR(255) NOT NULL,
  twitter VARCHAR(100),
  linkedin  VARCHAR(100),
  facebook  VARCHAR(100),
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (`url`)
)
ENGINE=INNODB
CHARSET=UTF8;

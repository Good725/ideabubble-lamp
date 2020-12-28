/*
ts:2019-09-17 07:50:00
*/

CREATE TABLE engine_automations_actions_triggers
(
  `action`  VARCHAR(100) NOT NULL,
  `trigger` VARCHAR(100) NOT NULL
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_remote_sync
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  type  VARCHAR(50) NOT NULL,
  remote_id VARCHAR(100) NOT NULL,
  cms_id  VARCHAR(100) NOT NULL,
  synced DATETIME NOT NULL,

  KEY (remote_id),
  KEY (cms_id)
)
ENGINE = INNODB
CHARSET = UTF8;

/*
ts:2016-02-10 22:44:00
*/

CREATE TABLE engine_activity_locks
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `plugin`  VARCHAR(50) NOT NULL,
  `activity`  VARCHAR(200) NOT NULL,
  locked_by INT NOT NULL,
  locked DATETIME NOT NULL,
  `session` VARCHAR(200) NOT NULL,

  UNIQUE KEY (`plugin`,`activity`)
)
ENGINE = InnoDB
CHARSET = UTF8;

INSERT INTO `settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  values
  ('engine_activity_lock_timeout', 'Activity Lock Timeout(seconds)', 'engine', '300', '300', '300', '300', '300', 'both', '', 'text', 'Engine', 0, '');

UPDATE `settings` SET `linked_plugin_name` = NULL  WHERE `variable` = 'engine_activity_lock_timeout';

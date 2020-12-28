/*
ts:2017-01-08 17:05:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('chat', 'Chat', 0, 0);
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (0, 'chat', 'Chat', 'Chat', 0);

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'chat_log', 'Chat View Logs', 'Chat View Logs', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'chat'));

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'), (SELECT `id` FROM `engine_resources` WHERE `alias` = 'chat'));


CREATE TABLE plugin_chat_rooms
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  name  VARCHAR(100),
  is_public TINYINT NOT NULL DEFAULT 1,
  created_by  INT,
  created DATETIME,
  closed DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_chat_rooms_join_users
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  user_id INT NOT NULL,
  joined DATETIME,

  KEY (room_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_chat_rooms_leave_users
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  user_id INT NOT NULL,
  leaved DATETIME,

  KEY (room_id),
  KEY (user_id)
)
  ENGINE = INNODB
  CHARSET = UTF8;

CREATE TABLE plugin_chat_rooms_has_messages
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  room_id INT NOT NULL,
  user_id INT NOT NULL,
  message TEXT,
  created  DATETIME,

  KEY (room_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_chat_rooms_has_messages_has_read_by
(
  message_id  INT NOT NULL,
  user_id INT NOT NULL,

  KEY (message_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_chat_online_users
(
  user_id INT PRIMARY KEY,
  last_actioned DATETIME
)
ENGINE = MEMORY
CHARSET = UTF8;

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('chat', 'chat_offline_after_minutes', 'Offline After Minutes', '5', '5', '5',  '5',  '5',  'both', '', 'text', 'Chat', 0, '');

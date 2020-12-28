/*
ts:2019-05-31 07:55:00
*/

ALTER TABLE `plugin_chat_rooms_join_users` RENAME `plugin_chat_rooms_has_users`;
ALTER TABLE `plugin_chat_rooms_has_users` ADD COLUMN `left` DATETIME;
ALTER TABLE plugin_chat_rooms_has_users ADD COLUMN invited_by INT;
ALTER TABLE plugin_chat_rooms_has_users ADD COLUMN invited DATETIME;
DROP TABLE plugin_chat_rooms_leave_users;

CREATE TABLE plugin_chat_roles_can_invite
(
  role_id INT,
  can_invite_role_id INT,

  PRIMARY KEY (role_id, can_invite_role_id)
)
ENGINE = INNODB
CHARSET = UTF8;


INSERT IGNORE INTO plugin_chat_roles_can_invite
  (role_id, can_invite_role_id)
  (select roles.id, invite_roles.id from engine_project_role roles, engine_project_role invite_roles where roles.role in ('Administrator', 'Teacher', 'Manager'));

INSERT IGNORE INTO plugin_chat_roles_can_invite
  (role_id, can_invite_role_id)
  (select roles.id, invite_roles.id from engine_project_role roles, engine_project_role invite_roles where roles.role in ('Student', 'Parent/Guardian', 'Mature Student') AND invite_roles.role in ('Teacher'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'chat_create_room', 'Chat Create Room', 'Chat Create Room', (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'chat'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT roles.id, resources.id FROM engine_project_role roles JOIN engine_resources resources WHERE roles.role in ('Administrator', 'Teacher', 'Manager') AND resources.alias = 'chat_create_room');

ALTER TABLE plugin_chat_rooms_has_users ADD COLUMN `mute` TINYINT NOT NULL DEFAULT 0;

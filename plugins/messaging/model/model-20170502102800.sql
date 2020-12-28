/*
ts:2017-05-02 10:28:00
*/

CREATE TABLE plugin_messaging_signatures
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100) NOT NULL,
  content TEXT,
  format  ENUM('HTML', 'TEXT'),
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE plugin_messaging_notification_templates ADD COLUMN signature_id INT;

ALTER TABLE engine_users ADD COLUMN default_messaging_signature TEXT;

/* Permissions */
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_see_under_developed_features', 'Messaging / See underdeveloped features', `id` FROM `engine_resources` WHERE alias = 'messaging';

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_send_alerts', 'Messaging / Send Alerts', `id` FROM `engine_resources` where alias = 'messaging';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id`
  FROM   `engine_project_role`
  JOIN   `engine_resources`
  WHERE  `engine_project_role`.`role` IN ('Super User', 'Administrator', 'External User')
  AND    `engine_resources`.`alias` = 'messaging_send_alerts';

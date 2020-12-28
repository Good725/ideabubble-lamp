/*
ts:2020-02-26 07:33:00
*/

ALTER TABLE engine_automations_actions_triggers ADD COLUMN id INT AUTO_INCREMENT PRIMARY KEY;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN name VARCHAR(100);
ALTER TABLE engine_automations_actions_triggers ADD COLUMN run_after_automation_id INT;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN created_by INT;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN created_date DATETIME;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN updated_by INT;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN updated_date DATETIME;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN deleted TINYINT NOT NULL DEFAULT 0;
ALTER TABLE engine_automations_actions_triggers ADD COLUMN conditions_mode ENUM('AND', 'OR');
ALTER TABLE engine_automations_actions_triggers MODIFY COLUMN `action`  VARCHAR(100) NULL AFTER `trigger`;

CREATE TABLE engine_automations_actions_triggers_has_conditions
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  action_trigger_id INT NOT NULL,
  field VARCHAR(100),

  KEY (action_trigger_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_automations_actions_triggers_has_conditions_has_values
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  condition_id INT NOT NULL,
  val VARCHAR(100),

  KEY (condition_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_automations_actions_triggers_has_intervals
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  action_trigger_id INT NOT NULL,
  interval_type ENUM('month', 'week', 'day', 'hour', 'minute'),
  interval_amount INT,
  execute ENUM('before', 'after'),

  KEY (action_trigger_id)
)
ENGINE=INNODB
CHARSET=UTF8;

UPDATE engine_automations_actions_triggers SET `name` = CONCAT(`trigger`, '-', `action`) WHERE `name` IS NULL;
ALTER TABLE engine_automations_actions_triggers_has_conditions ADD COLUMN `operator` ENUM('=', '<>', '>', '>=', '<', '<=');

ALTER TABLE engine_automations_actions_triggers RENAME `engine_automations`;

CREATE TABLE engine_automations_has_sequences
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  automation_id INT NOT NULL,
  position INT NOT NULL DEFAULT 0,
  wait_type ENUM('minute', 'hour', 'day', 'month'),
  wait INT,
  run_type ENUM('action', 'automation', 'message') NOT NULL,
  action VARCHAR(100),
  run_after_automation_id INT,
  message_template_id INT,
  message_driver ENUM('email', 'sms', 'dashboard'),
  message_subject VARCHAR(100),
  message_body MEDIUMTEXT,

  KEY (automation_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO engine_automations_has_sequences
  (automation_id,run_type,action)
  (select id, 'action', action from engine_automations);

ALTER TABLE engine_automations DROP COLUMN `action`;
ALTER TABLE engine_automations DROP COLUMN `run_after_automation_id`;

ALTER TABLE engine_automations_actions_triggers_has_conditions RENAME engine_automations_has_conditions;
ALTER TABLE engine_automations_actions_triggers_has_conditions_has_values RENAME engine_automations_has_conditions_has_values;
ALTER TABLE engine_automations_actions_triggers_has_intervals RENAME engine_automations_has_intervals;

ALTER TABLE engine_automations_has_conditions CHANGE COLUMN action_trigger_id automation_id  INT NOT NULL;
ALTER TABLE engine_automations_has_intervals CHANGE COLUMN action_trigger_id automation_id  INT NOT NULL;
ALTER TABLE engine_automations_has_intervals ADD COLUMN ref VARCHAR(100);
ALTER TABLE engine_automations_has_intervals DROP COLUMN ref;

ALTER TABLE engine_cron_tasks ADD COLUMN extra_parameters TEXT;
ALTER TABLE engine_cron_tasks ADD COLUMN internal_only TINYINT NOT NULL DEFAULT 0;

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `note`, `type`, `group`)
  VALUES
  ('php_binary_path', 'PHP Path', '/user/bin/php', '/user/bin/php', '/user/bin/php', '/user/bin/php', 'Path to php exacutable binary', 'text', 'Engine');

UPDATE engine_settings SET value_live='/usr/bin/php',value_stage='/usr/bin/php',value_test='/usr/bin/php' WHERE variable='php_binary_path';

CREATE TABLE engine_automations_has_sequences_has_intervals
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  interval_type ENUM('month', 'week', 'day', 'hour', 'minute'),
  interval_amount INT,
  execute ENUM('before', 'after'),
  crontask_id INT,

  KEY (sequence_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_automations_has_sequences_has_conditions
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  field VARCHAR(100),
  `operator` ENUM('=', '<>', '>', '>=', '<', '<=', 'in'),

  KEY (sequence_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_automations_has_sequences_has_conditions_has_values
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_condition_id INT NOT NULL,
  val VARCHAR(100),

  KEY (sequence_condition_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE engine_automations_has_sequences_has_message_recipients
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  recipient VARCHAR(100),
  x_details VARCHAR(50),

  KEY (sequence_id)
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE engine_automations ADD COLUMN crontask_id INT;
ALTER TABLE engine_automations_has_sequences MODIFY COLUMN run_type ENUM('action', 'automation', 'message', 'create_todo') NOT NULL;
ALTER TABLE engine_automations ADD COLUMN published TINYINT NOT NULL DEFAULT 0;

ALTER TABLE engine_automations_has_sequences_has_intervals ADD COLUMN is_periodic TINYINT NOT NULL DEFAULT 1;
ALTER TABLE engine_automations_has_sequences_has_intervals ADD COLUMN execute_once_at_datetime DATETIME;

CREATE TABLE engine_automations_has_sequences_has_attachments
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  file_id INT NOT NULL,
  process_docx  TINYINT NOT NULL DEFAULT 0,
  convert_docx_to_pdf TINYINT NOT NULL DEFAULT 0,

  KEY (sequence_id),
  KEY (file_id)
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE engine_automations_has_sequences ADD COLUMN todo_title VARCHAR(100);
ALTER TABLE engine_automations_has_sequences ADD COLUMN todo_assignee ENUM('Trainer', 'Attendee', 'Contact');

CREATE TABLE engine_automations_has_sequences_has_todo_schedules
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  schedule_id INT NOT NULL,

  KEY (sequence_id),
  KEY (schedule_id)
)
ENGINE=INNODB
CHARSET=UTF8;
CREATE TABLE engine_automations_has_sequences_has_todo_contacts
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  sequence_id INT NOT NULL,
  contact_id INT NOT NULL,

  KEY (sequence_id),
  KEY (contact_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO `engine_plugins`
  (`name`, `friendly_name`, `icon`, `flaticon`, `svg`)
  VALUES
  ('automations', 'Automations', 'notifications', 'speech', 'notification');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`)
  VALUES
  ('0', 'automations', 'Automations', 'Automations');

DELETE FROM engine_resources WHERE `alias` IN ('automations_edit', 'automations_view');
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'automations_edit', 'Automations / Edit', 'Automations / Edit', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'automations'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'automations_view', 'Automations / View', 'Automations / View', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'automations'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'automations_settings', 'Automations / Settings', 'Automations / Settings', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'automations'));

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
    (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'automations')
  );

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
    (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'automations_edit')
  );

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
    (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'automations_view')
  );

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
    (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'automations_settings')
  );


ALTER TABLE `engine_automations` RENAME `plugin_automations`;
ALTER TABLE `engine_automations_has_conditions` RENAME `plugin_automations_has_conditions`;
ALTER TABLE `engine_automations_has_conditions_has_values` RENAME `plugin_automations_has_conditions_has_values`;
ALTER TABLE `engine_automations_has_intervals` RENAME `plugin_automations_has_intervals`;
ALTER TABLE `engine_automations_has_sequences` RENAME `plugin_automations_has_sequences`;
ALTER TABLE `engine_automations_has_sequences_has_attachments` RENAME `plugin_automations_has_sequences_has_attachments`;
ALTER TABLE `engine_automations_has_sequences_has_conditions` RENAME `plugin_automations_has_sequences_has_conditions`;
ALTER TABLE `engine_automations_has_sequences_has_conditions_has_values` RENAME `plugin_automations_has_sequences_has_conditions_has_values`;
ALTER TABLE `engine_automations_has_sequences_has_intervals` RENAME `plugin_automations_has_sequences_has_intervals`;
ALTER TABLE `engine_automations_has_sequences_has_todo_contacts` RENAME `plugin_automations_has_sequences_has_todo_contacts`;
ALTER TABLE `engine_automations_has_sequences_has_todo_schedules` RENAME `plugin_automations_has_sequences_has_todo_schedules`;
ALTER TABLE `engine_automations_has_sequences_has_message_recipients` RENAME `plugin_automations_has_sequences_has_message_recipients`;

CREATE TABLE plugin_automations_log
(
  id  INT NOT NULL PRIMARY KEY,
  automation_id INT NOT NULL,
  parameters  MEDIUMTEXT,
  executed  DATETIME,

  KEY (automation_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_automations_triggers_enabled
(
  trigger_name VARCHAR(100) PRIMARY KEY
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE plugin_automations_has_sequences ADD COLUMN conditions_mode ENUM('AND', 'OR') NOT NULL DEFAULT 'AND';

ALTER TABLE `plugin_automations_log` CHANGE COLUMN `automation_id` `sequence_id`  INT NOT NULL;

ALTER TABLE `plugin_automations_has_intervals` DROP INDEX `action_trigger_id`;
ALTER TABLE `plugin_automations_has_intervals` ADD INDEX (`automation_id`);

ALTER TABLE `plugin_automations_has_conditions` ADD FOREIGN KEY (`automation_id`) REFERENCES `plugin_automations` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_conditions_has_values` ADD FOREIGN KEY (`condition_id`) REFERENCES `plugin_automations_has_conditions` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_sequences` ADD FOREIGN KEY (`automation_id`) REFERENCES `plugin_automations` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_sequences_has_conditions` ADD FOREIGN KEY (`sequence_id`) REFERENCES `plugin_automations_has_sequences` (`id`) ON DELETE CASCADE;

ALTER TABLE `plugin_automations_has_sequences_has_intervals` ADD FOREIGN KEY (`sequence_id`) REFERENCES `plugin_automations_has_sequences` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_sequences_has_todo_contacts` ADD FOREIGN KEY (`sequence_id`) REFERENCES `plugin_automations_has_sequences` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_sequences_has_todo_schedules` ADD FOREIGN KEY (`sequence_id`) REFERENCES `plugin_automations_has_sequences` (`id`) ON DELETE CASCADE;
ALTER TABLE `plugin_automations_has_sequences_has_attachments` ADD FOREIGN KEY (`sequence_id`) REFERENCES `plugin_automations_has_sequences` (`id`) ON DELETE CASCADE;
-- ALTER TABLE `plugin_automations_has_intervals` ADD FOREIGN KEY (`automation_id`) REFERENCES `plugin_automations` (`id`) ON DELETE CASCADE;


ALTER TABLE `plugin_automations_has_sequences` ADD COLUMN repeat_by_field VARCHAR(100);
ALTER TABLE `plugin_automations_has_sequences_has_intervals` DROP COLUMN `interval_type`;
ALTER TABLE `plugin_automations_has_sequences_has_intervals` DROP COLUMN `interval_amount`;
ALTER TABLE `plugin_automations_has_sequences_has_intervals` DROP COLUMN `execute`;
ALTER TABLE `plugin_automations_has_sequences_has_intervals` ADD COLUMN `frequency` TEXT;

CREATE TABLE plugin_automations_log_messages
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  log_id  INT NOT NULL,
  message_id INT NOT NULL,


  KEY (log_id),
  KEY (message_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_automations_log_todos
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  log_id  INT NOT NULL,
  todo_id INT NOT NULL,


  KEY (log_id),
  KEY (todo_id)
)
ENGINE=INNODB
CHARSET=UTF8;

ALTER TABLE `plugin_automations_has_sequences_has_conditions` MODIFY COLUMN `operator`  enum('=','<>','>','>=','<','<=','in','onbefore','onafter','tobefore','fromsince ');
ALTER TABLE `plugin_automations_log` MODIFY COLUMN `id`  INT NOT NULL AUTO_INCREMENT;

ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN interval_amount INT;
ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN interval_type ENUM('month', 'week', 'day', 'hour', 'minute');
ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN interval_operator ENUM('<', '<=', '=', '>=', '>');
ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN `execute` ENUM('before', 'after');
ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN interval_field VARCHAR(100);
ALTER TABLE plugin_automations_has_sequences_has_intervals ADD COLUMN allow_duplicate_message TINYINT NOT NULL DEFAULT 1;

ALTER TABLE plugin_automations_has_sequences_has_message_recipients ADD COLUMN recipient_type VARCHAR(100);

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  ('1', 'automations_test', 'Automations / Test', 'Automations / Test', (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'automations'));

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  VALUES
  (
    (SELECT `id` FROM `engine_project_role` WHERE `role`  = 'Administrator'),
    (SELECT `id` FROM `engine_resources`    WHERE `alias` = 'automations_test')
  );

ALTER TABLE `plugin_automations_has_sequences` ADD COLUMN message_from VARCHAR(100);

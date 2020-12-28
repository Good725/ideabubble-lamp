/*
ts:2016-05-16 11:31:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`)	VALUES ('homework', 'Homework', 1);

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'homework', 'Homework', 'Homework');
SELECT id INTO @homework_resource_id FROM `engine_resources` o WHERE o.`alias` = 'homework';
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'homework_index', 'Homework / Index', 'Homework List', @homework_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'homework_edit', 'Homework / Edit', 'homework Create / Update', @homework_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'homework_delete', 'Homework / Delete', 'Homework Delete', @homework_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'homework_edit_limited', 'Homework / Edit : limited', 'Homework Create/Update limited access based on permission', @homework_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'homework_index_limited', 'Homework / Index : limited', 'Homework List limited access based on permission', @homework_resource_id);
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (2, 'homework_view_limited', 'Homework / View : limited', 'Homework View limited access based on permission', @homework_resource_id);

CREATE TABLE `plugin_homework_homeworks`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  course_schedule_event_id INT,
  title VARCHAR(200),
  description TEXT,
  created DATETIME,
  created_by INT,
  updated DATETIME,
  updated_by INT,
  published TINYINT(1) DEFAULT 0,
  deleted TINYINT(1) DEFAULT 0,

  KEY (course_schedule_event_id)
)
  ENGINE = InnoDB
  CHARSET = UTF8;

CREATE TABLE `plugin_homework_has_files`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  homework_id INT,
  file_id INT,

  KEY (homework_id),
  KEY (file_id)
)
  ENGINE = InnoDB
  CHARSET = UTF8;

INSERT INTO `plugin_files_file` (`type`, `name`, `parent_id`, `deleted`) VALUES (0, 'homeworks', 1, 0);

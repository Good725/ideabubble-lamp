/*
ts:2018-04-11 12:01:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('exams', 'Exams', '1', '0');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'exams', 'Exams', 'Exams');
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  (SELECT 1, 'exams_list', 'Exams / List', 'Exams / List', id FROM `engine_resources` o WHERE o.`alias` = 'exams');
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  (SELECT 1, 'exams_edit', 'Exams / Edit', 'Exams / Edit', id FROM `engine_resources` o WHERE o.`alias` = 'exams');
INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  (SELECT 1, 'exams_view_results', 'Exams / View Results', 'Exams / View Results', id FROM `engine_resources` o WHERE o.`alias` = 'exams');
INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
  (SELECT 1, 'exams_list_limited', 'Exams / List Limited', 'Exams / List Limited', id FROM `engine_resources` o WHERE o.`alias` = 'exams');
INSERT INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
  (SELECT 1, 'exams_edit_limited', 'Exams / Edit Limited', 'Exams / Edit Limited', id FROM `engine_resources` o WHERE o.`alias` = 'exams');
INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role = 'Administrator' AND resources.alias like 'exams%');

INSERT INTO `engine_role_permissions`
  (`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role IN('Student', 'Parent/Guardian') AND resources.alias = 'exams_view_results');
INSERT INTO `engine_role_permissions`
(`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role IN('Teacher') AND resources.alias IN ('exams_list_limited', 'exams_edit_limited'));
INSERT INTO `engine_role_permissions`
(`role_id`, `resource_id`)
  (SELECT roles.id, resources.id FROM engine_project_role roles, engine_resources resources WHERE roles.role IN('Teacher') AND resources.alias IN ('courses_limited_access'));

CREATE TABLE plugin_exams_exams
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `type`  ENUM('Class Test', 'Term Assesment', 'State Exam'),
  `mode`  ENUM('Theory', 'Practical', 'Aural', 'Oral'),
  title VARCHAR(100),
  datetime DATETIME,
  location_id INT,
  published TINYINT NOT NULL DEFAULT 1,
  created_by  INT,
  created DATETIME,
  updated_by  INT,
  updated DATETIME,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE plugin_exams_exams ADD COLUMN owned_by INT;
ALTER TABLE plugin_exams_exams ADD COLUMN summary TEXT;
ALTER TABLE plugin_exams_exams ADD COLUMN grading_type ENUM('%', '%+Grade', '%Custom');

CREATE TABLE plugin_exams_exams_has_courses
(
  exam_id INT NOT NULL,
  course_id INT NOT NULL,

  KEY (exam_id),
  KEY (course_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_has_schedules
(
  exam_id INT NOT NULL,
  schedule_id INT NOT NULL,

  KEY (exam_id),
  KEY (schedule_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_has_topics
(
  exam_id INT NOT NULL,
  topic_id INT NOT NULL,

  KEY (exam_id),
  KEY (topic_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_has_results
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  exam_id INT NOT NULL,
  schedule_id INT NOT NULL,
  student_id  INT NOT NULL,
  result  VARCHAR(10),
  grade VARCHAR(3),
  points  DECIMAL(10, 2),
  comment TEXT,

  KEY (exam_id),
  KEY (schedule_id),
  KEY (student_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_has_permissions
(
  exam_id INT NOT NULL,
  role_id INT,

  KEY (exam_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `plugin_messaging_notification_templates` (`name`, `driver`, `type_id`, `subject`, `message`, `create_via_code`, `usable_parameters_in_template`, `linked_plugin_name`) VALUES ('exam-result-email', 'EMAIL', '1', 'Exam Result', 'Hello,\r\nExam: $exam $type $mode\r\nStudent: $student\r\nCourse: $course\r\nSchedule: $schedule\r\nResult: $result\r\nGrade: $grade\r\nPoints: $points\r\nComment: $comment\r\n', 'Exams', 'exam,$type,$mode,$student,$course,$schedule,$result,$grade,$points,$comment\r\n', 'exams');

CREATE TABLE plugin_exams_grades
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  grade VARCHAR(10),
  percent_min DECIMAL(10, 2),
  percent_max DECIMAL(10, 2),
  points_h  INT,
  points_o  INT
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('A', 85.00, 100.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('B', 70.00, 85.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('C', 55.00, 70.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('D', 40.00, 55.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('E', 25.00, 40.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('F', 10.00, 25.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('NG', 0.00, 10.00, NULL, NULL);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H1/O1', 90.00, 100.00, 100, 56);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H2/O2', 80.00, 90.00, 88, 46);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H3/O3', 70.00, 80.00, 77, 37);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H4/O4', 60.00, 70.00, 66, 28);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H5/O5', 50.00, 60.00, 56, 20);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H6/O6', 40.00, 50.00, 46, 12);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H7/O7', 30.00, 40.00, 33, 0);
INSERT INTO `plugin_exams_grades` (`grade`, `percent_min`, `percent_max`, `points_h`, `points_o`) VALUES ('H8/O8', 0.00, 30.00, 0, 0);

ALTER TABLE plugin_exams_grades ADD COLUMN `deleted` TINYINT NOT NULL DEFAULT 0;

-- Spelling correction
ALTER TABLE `plugin_exams_exams` MODIFY COLUMN `type` ENUM('Class Test', 'Term Assessment', 'State Exam');

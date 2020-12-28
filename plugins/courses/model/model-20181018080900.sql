/*
ts:2018-10-18 08:09:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_credits', 'Courses Credits', 'Courses Credits', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));


CREATE TABLE plugin_courses_credits
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  academicyear_id INT,
  course_id INT,
  subject_id  INT,
  type  ENUM('Practical', 'Theory'),
  credit  INT,
  hours INT,
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (course_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_courses_credits_has_schedules
(
  credit_id  INT NOT NULL,
  schedule_id INT NOT NULL,

  KEY (credit_id),
  KEY (schedule_id)
)
ENGINE = INNODB
CHARSET = UTF8;

/*
ts:2018-05-21 08:00:00
*/

ALTER TABLE plugin_exams_exams ADD COLUMN datetime_end DATETIME;
ALTER TABLE `plugin_exams_exams` MODIFY COLUMN `grading_type` ENUM('%','%+Grade','%Custom','%+Grade+Points','Grade+Points');
CREATE TABLE plugin_exams_exams_has_subjects
(
  exam_id INT NOT NULL,
  subject_id  INT NOT NULL,
  KEY (exam_id),
  KEY (subject_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_has_academicyears
(
  exam_id INT NOT NULL,
  academicyear_id  INT NOT NULL,
  KEY (exam_id),
  KEY (academicyear_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_exams_exams_is_favorite
(
  exam_id INT NOT NULL,
  user_id  INT NOT NULL,
  KEY (exam_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

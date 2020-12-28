/*
ts:2018-01-01 15:15:00
*/

CREATE TABLE plugin_courses_courses_has_years
(
  course_id INT NOT NULL,
  year_id INT NOT NULL,
  KEY (course_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_courses_courses_has_years
	(course_id, year_id)
	(SELECT id, year_id FROM plugin_courses_courses where year_id IS NOT NULL);

/*
ts: 2018-03-05 10:38:00
*/

ALTER TABLE plugin_courses_subjects ADD COLUMN cycle  SET('Junior', 'Senior');
ALTER TABLE plugin_courses_courses ADD COLUMN cycle  SET('Junior', 'Senior');

ALTER TABLE `plugin_courses_subjects` MODIFY COLUMN `cycle`  SET('Junior','Senior','Transition');
ALTER TABLE `plugin_courses_courses` MODIFY COLUMN `cycle`  SET('Junior','Senior','Transition');


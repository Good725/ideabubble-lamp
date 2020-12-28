/*
ts:2018-08-14 16:47:00
*/

ALTER TABLE `plugin_courses_rollcall` MODIFY COLUMN `status`  SET ('Absent','Present','Late','Early Departures','Unpaid','Paid','Temporary Absence');
ALTER TABLE `plugin_courses_rollcall` ADD COLUMN arrived DATETIME;
ALTER TABLE `plugin_courses_rollcall` ADD COLUMN `left` DATETIME;
ALTER TABLE `plugin_courses_rollcall` ADD COLUMN absence_left DATETIME;
ALTER TABLE `plugin_courses_rollcall` ADD COLUMN absence_returned DATETIME;

ALTER TABLE `plugin_ib_educate_booking_items` MODIFY COLUMN `timeslot_status`  SET ('Present','Late','Early Departures','Paid','Temporary Absence');
ALTER TABLE `plugin_ib_educate_booking_items` ADD COLUMN arrived DATETIME;
ALTER TABLE `plugin_ib_educate_booking_items` ADD COLUMN `left` DATETIME;
ALTER TABLE `plugin_ib_educate_booking_items` ADD COLUMN absence_left DATETIME;
ALTER TABLE `plugin_ib_educate_booking_items` ADD COLUMN absence_returned DATETIME;

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_rollcall', 'Courses / Roll Call', 'Courses / Roll Call', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'courses_rollcall');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_rollcall_limited', 'Courses / Roll Call Limited', 'Courses / Roll Call Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Teacher') AND e.alias = 'courses_rollcall_limited');


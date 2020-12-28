/*
ts:2017-03-07 18:39:17
*/

UPDATE `engine_settings` SET `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1' WHERE `variable`='view_website';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
  WHERE `engine_project_role`.`role` IN ('Administrator', 'Super User') AND `engine_resources`.`alias` = 'view_website_frontend';


UPDATE IGNORE `engine_settings` SET
  `value_live`='/admin/bookings',
  `value_stage`='/admin/bookings',
  `value_test`='/admin/bookings',
  `value_dev`='/admin/bookings'
WHERE `variable`='cms_heading_button_link';

UPDATE IGNORE `engine_settings` SET
  `value_live`='Create Course',
  `value_stage`='Create Course',
  `value_test`='Create Course',
  `value_dev`='Create Course'
WHERE `variable`='cms_heading_button_text';
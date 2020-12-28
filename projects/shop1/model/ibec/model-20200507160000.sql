/*
ts:2020-05-07 16:00:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'ibec',
  `value_test`  = 'ibec',
  `value_stage` = 'ibec',
  `value_live`  = 'ibec'
WHERE
  `variable` = 'cms_skin';

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Find',
  `value_test`  = 'Find',
  `value_stage` = 'Find',
  `value_live`  = 'Find'
WHERE
  `variable` = 'cms_heading_button_text_2';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
  WHERE `engine_project_role`.`role` IN ('Student') AND `engine_resources`.`alias` = 'view_website_frontend';

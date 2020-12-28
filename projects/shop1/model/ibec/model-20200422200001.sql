/*
ts:2020-04-22 20:00:01
*/
DELIMITER ;;


UPDATE `engine_settings`
SET
  `value_dev`   = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'accreditation-application' LIMIT 1),
  `value_test`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'accreditation-application' LIMIT 1),
  `value_stage` = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'accreditation-application' LIMIT 1),
  `value_live`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'accreditation-application' LIMIT 1)
WHERE
  `variable` = 'accreditation_application_page';;


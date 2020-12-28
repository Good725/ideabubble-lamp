/*
ts:2019-07-01 18:00:00
*/
INSERT INTO `plugin_courses_study_modes` (`study_mode`, `summary`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'Online',
  'Distant learning. Modules are taught online.',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);

INSERT INTO `plugin_courses_location_types` (`type`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'Online',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);

INSERT INTO `plugin_courses_locations` (`name`, `location_type_id`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`) VALUES (
  'Online',
  (SELECT IFNULL(`id`, '') FROM `plugin_courses_location_types` WHERE `type` = 'Online' AND `delete` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '0'
);
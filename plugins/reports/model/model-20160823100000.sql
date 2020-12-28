/*
ts:2016-08-23 10:00:00
*/

INSERT IGNORE INTO `plugin_reports_chart_types` (`name`, `stub`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES (
  'Total',
  'total',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

ALTER TABLE `plugin_reports_sparklines` ADD COLUMN `dashboard_link_id` INT(11) NULL DEFAULT NULL  AFTER `width` ;

/*
ts:2016-03-30 17:15:00
*/
INSERT IGNORE INTO `plugin_reports_chart_types` (`name`, `stub`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES (
  'Comparison Total',
  'comparison_total',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

UPDATE IGNORE `plugin_reports_sparklines`
SET `chart_type_id` = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'comparison_total')
WHERE `title` = 'Sales';

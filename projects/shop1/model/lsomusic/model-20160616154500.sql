/*
ts:2016-06-16 15:45:00
*/
INSERT IGNORE  INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Useful Links',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'raw_html'),
  '',
  '',
  '<a href=\"#\">Link 1</a><br />\n<a href=\"#\">Link 2</a><br />\n<a href=\"#\">Link 3</a><br />\n<a href=\"#\">Link 4</a>',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `summary`, `sql`, `widget_sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Useful Links',
  '',
  '',
  '',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Useful Links' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT INTO `plugin_dashboards` (`title`, `columns`, `date_filter`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Example',
  '3',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards`              WHERE `title` = 'Example'      ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports`         WHERE `name`  = 'Useful Links' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub`  = 'widget'),
  '1',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

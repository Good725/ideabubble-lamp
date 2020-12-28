/*
ts:2016-03-09 15:10:00
*/

-- "Top-Selling Days" widget
INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Top-Selling Days',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'bar_chart' LIMIT 1),
  'Date',
  'Money',
  '',
  '',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0');

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `summary`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Top-Selling Days',
  'Days of the week with the most income',
  'SELECT    SUM(payment_amount) AS `Money`,    DATE_FORMAT(purchase_time, \'%a\') AS `Date`,    paid  FROM    `plugin_payments_log`  WHERE    `purchase_time` IS NOT NULL  AND  `purchase_time` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}  GROUP BY    WEEKDAY (`purchase_time`)',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Top-Selling Days' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

-- "Top-Selling Months" widget
INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `html`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Top-Selling Months',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'bar_chart' LIMIT 1),
  'Date',
  'Money',
  '',
  '',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0');

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `summary`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Top-Selling Months',
  'Most with the most income',
  'SELECT    SUM(payment_amount) AS `Money`,    DATE_FORMAT(purchase_time, \'%b\') AS `Date`,    paid  FROM    `plugin_payments_log`  WHERE    `purchase_time` IS NOT NULL  AND  `purchase_time` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}  GROUP BY    MONTH (`purchase_time`)',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Top-Selling Months' ORDER BY `id` DESC LIMIT 1),
  'sql'
);

-- Sales dashboard
INSERT IGNORE INTO `plugin_dashboards` (`title`, `description`, `columns`, `date_filter`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Sales',
  '',
  '3',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Sales' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Top-Selling Months'),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget'),
  '1',
  '1',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Sales' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Top-Selling Days'),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget'),
  '2',
  '2',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

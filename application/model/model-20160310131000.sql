/*
ts:2016-03-10 13:10:00
*/

-- Create a "Total Sales" sparkline and add to the "Sales" dashboard

-- Create the report
INSERT IGNORE INTO `plugin_reports_reports` (`name`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `report_type`) VALUES
(
  'Total Sales',
  'SELECT  	ROUND(SUM(`payment_amount`), 2) AS `Money`, 	DATE_FORMAT(`purchase_time`, \'%b\') AS `Date`, 	(SELECT ROUND(SUM(`payment_amount`), 2) FROM `plugin_payments_log`) AS `Total` FROM 	`plugin_payments_log` WHERE 	`purchase_time` IS NOT NULL GROUP BY MONTH(`purchase_time`)',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  'sql'
);

-- Create the sparkline
INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `x_axis`, `y_axis`, `total_field`, `total_type_id`, `text_color`, `background_color`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  'Sales',
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Sales' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
  'Money',
  'Date',
  'Money',
  (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'count' LIMIT 1),
  'rgb(255, 255, 255)',
  'rgb(158, 195, 230)',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Add the sparkline to the dashboard
INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Sales' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Sales' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget'),
  '3',
  '3',
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Add date range variables to the report
UPDATE IGNORE `plugin_reports_reports` SET `sql` = 'SELECT  	ROUND(SUM(`payment_amount`), 2) AS `Money`, 	DATE_FORMAT(`purchase_time`, ''%Y %b'') AS `Date`, 	(SELECT ROUND(SUM(`payment_amount`), 2) FROM `plugin_payments_log`) AS `Total` FROM 	`plugin_payments_log` WHERE 	`purchase_time` IS NOT NULL AND  `purchase_time` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}   GROUP BY MONTH(`purchase_time`)' WHERE `name` = 'Total Sales' AND `delete` = 0;

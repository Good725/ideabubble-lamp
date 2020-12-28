/*
ts:2016-06-30 16:00:00
*/

/* Create the dashboard */
INSERT IGNORE INTO `plugin_dashboards` (`title`, `columns`, `date_filter`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  'Events Dashboard',
  '3',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

/* Create the widgets */
INSERT IGNORE INTO `plugin_reports_widgets`
(`name`, `type`, `x_axis`, `y_axis`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`) VALUES
(
  'Sales by Month',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'bar_chart'),
  'Date',
  'Money',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
),
(
  'Sales by Day',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'bar_chart'),
  'Date',
  'Money',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
),
(
  'Sales by Event',
  (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'bar_chart'),
  'Event',
  'Money',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

/* Create the reports */
INSERT IGNORE INTO `plugin_reports_reports`
(`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Sales by Month',
  'SELECT\n\t
    ROUND(SUM(`amount`), 2) AS `Money`,\n\t
    DATE_FORMAT(`created`, \'%Y %b\') AS `Date`\n
FROM\n\t
    `plugin_events_orders_payments`\n
WHERE\n\t
    `created` IS NOT NULL\n
AND\n\t
    `created` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Sales by Month' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT IGNORE INTO `plugin_reports_reports`
(`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Sales by Day',
  'SELECT
\n\tROUND(SUM(`amount`), 2) AS `Money`,
\n\tDATE_FORMAT(`created`, \'%a\') AS `Date`
\nFROM
\n\t`plugin_events_orders_payments`
\nWHERE
\n\t`created` IS NOT NULL
\nAND
\n\t`created` IS NOT NULL
\nAND
\n\t`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}
\nGROUP BY DAYOFWEEK(`created`)',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Sales by Day' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  'sql'
);

INSERT IGNORE INTO `plugin_reports_reports`
(`name`, `sql`, `dashboard`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `widget_id`, `report_type`) VALUES
(
  'Sales by Event',
  'SELECT ROUND(`payment`.`amount`, 2) AS `Money`, `event`.`name` AS `Event`
  \nFROM	`plugin_events_orders_payments`         `payment`
  \nJOIN	`plugin_events_orders`                  `order`       ON `payment`    .`order_id`       = `order`      .`id`
  \nJOIN	`plugin_events_orders_items`            `order_item`  ON `order_item` .`order_id`       = `order`      .`id`
  \nJOIN	`plugin_events_events_has_ticket_types` `ticket_type` ON `order_item` .`ticket_type_id` = `ticket_type`.`id`
  \nJOIN	`plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`
  \nWHERE   `payment`.`created` IS NOT NULL AND `payment`.`created` BETWEEN {!DASHBOARD-FROM!} and {!DASHBOARD-TO!}
  \nGROUP BY `ticket_type`.`event_id`',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  (SELECT `id` FROM `plugin_reports_widgets` WHERE `name` = 'Sales by Event' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  'sql'
);


INSERT IGNORE INTO `plugin_reports_reports`
(`name`, `sql`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `delete`, `report_type`) VALUES
(
  'Total Revenue',
  'SELECT
  \n\tROUND(SUM(`amount`), 2) AS `Money`,
  \n\tDATE_FORMAT(`created`, \'%Y %b\') AS `Date`,
  \n\t(SELECT ROUND(SUM(`amount`), 2) FROM `plugin_events_orders_payments`) AS `Total`
  \nFROM
  \n\t`plugin_events_orders_payments`
  \nWHERE
  \n\t`created` IS NOT NULL
  \nAND
  \n\t`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0',
  'sql'
);

/* Create the sparkline */
INSERT IGNORE INTO `plugin_reports_sparklines`
(`title`, `report_id`, `chart_type_id`, `x_axis`, `y_axis`, `total_field`, `total_type_id`, `text_color`, `background_color`, `created_by`, `modified_by`, `date_created`, `publish`, `deleted`) VALUES
(
  'Total Revenue',
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Revenue' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'comparison_total' LIMIT 1),
  'Money',
  'Date',
  'Money',
  (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'count' LIMIT 1),
  '#fff',
  '#31ceb4',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

/* Add the widgets and sparklines to the dashboard */

INSERT IGNORE  INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Sales by Month' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0),
  '1',
  '1',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE  INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Sales by Day' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0),
  '2',
  '2',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE  INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Sales by Event' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' AND `deleted` = 0),
  '3',
  '3',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

INSERT IGNORE  INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
(
  (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Events Dashboard' ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Total Revenue' AND `delete` = 0 ORDER BY `id` DESC LIMIT 1),
  (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' AND `deleted` = 0),
  '2',
  '4',
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '0'
);


UPDATE IGNORE `plugin_reports_reports` SET `dashboard` = 0 WHERE `name` IN ('Sales by Month', 'Sales by Day', 'Sales by Event');

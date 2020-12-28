/*
ts:2016-08-10 11:30:00
*/

UPDATE `plugin_reports_reports`
SET `sql` = "SELECT
\n      CONCAT('<span class=\"calendar-event-name\">', `event`.`name`, '</span> <span class=\"calendar-event-qty\">',  `item`.`quantity`, (case when `item`.`quantity` = 1 THEN ' ticket' ELSE ' tickets' END), '</span>') AS `Title`,
\n	`date`.`starts` AS `iso_date`,
\n	DATE_FORMAT(`date`.`starts`, '%e %M %Y') AS `Date`,
\n	`event`.`url` AS `Link`
\nFROM `plugin_events_orders` `order`
\nJOIN `plugin_events_orders_items`            `item`        ON `item`       .`order_id`       = `order`      .`id`
\nJOIN `plugin_events_events_has_ticket_types` `ticket_type` ON `item`       .`ticket_type_id` = `ticket_type`.`id`
\nJOIN `plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`
\nJOIN `plugin_events_events_dates`            `date`        ON `date`       .`event_id`       = `event`      .`id`
\nWHERE `buyer_id` = @user_id"
WHERE `name` = 'Orders' AND `summary` IS NULL;

UPDATE `plugin_reports_widgets`
SET `type` = (SELECT `id` FROM `plugin_reports_widget_types` WHERE `stub` = 'calendar')
WHERE `name` = 'Orders' AND `x_axis` = 'Title' AND `y_axis` = 'Date'
;
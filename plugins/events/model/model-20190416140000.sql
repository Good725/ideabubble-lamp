/*
ts:2019-04-16 14:00:00
*/

UPDATE
  `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `sql` = "SELECT
\n      CONCAT('<span class=\"calendar-event-name\">', `event`.`name`, '</span> <span class=\"calendar-event-qty\">',  `item`.`quantity`, (case when `item`.`quantity` = 1 THEN ' ticket' ELSE ' tickets' END), '</span>') AS `Title`,
\n    `date`.`starts` AS `iso_date`,
\n    DATE_FORMAT(`date`.`starts`, '%e %M %Y') AS `Date`,
\n    '/admin/events/mytickets' AS `Link`,
\n    'Go to my tickets' AS `Read more text`
\nFROM `plugin_events_orders` `order`
\nJOIN `plugin_events_orders_items`            `item`        ON `item`       .`order_id`       = `order`      .`id`
\nJOIN `plugin_events_events_has_ticket_types` `ticket_type` ON `item`       .`ticket_type_id` = `ticket_type`.`id`
\nJOIN `plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`
\nJOIN `plugin_events_events_dates`            `date`        ON `date`       .`event_id`       = `event`      .`id`
\nWHERE event.deleted = 0 AND `order`.deleted = 0 AND `order`.`status` = 'PAID' AND `buyer_id` = \@user_id
\nORDER BY `order`.created"
WHERE
  `name` = 'Orders' AND `sql` LIKE '%plugin_events%'
;
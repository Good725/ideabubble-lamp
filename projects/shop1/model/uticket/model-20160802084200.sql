/*
ts:2016-08-02 08:42:00
*/

UPDATE plugin_reports_reports
  SET `sql` = 'SELECT\n    DATE_FORMAT(`date`.`starts`, \'<div class=\"text-center text-uppercase\">%d<br />%b</div>\') AS \'Date\',\n    CONCAT(\'<a href=\"/event/\', `event`.`url`, \'\" target=\"_blank\" style=\"text-decoration: underline;color: blue;\">\',`event`.`name`, \'</a>\') AS \'Event\',\n    `item`.`quantity` AS \'Tickets\'\nFROM `plugin_events_orders` `order`\nJOIN `plugin_events_orders_items`            `item`        ON `item`       .`order_id`       = `order`      .`id`\nJOIN `plugin_events_events_has_ticket_types` `ticket_type` ON `item`       .`ticket_type_id` = `ticket_type`.`id`\nJOIN `plugin_events_events`                  `event`       ON `ticket_type`.`event_id`       = `event`      .`id`\nJOIN `plugin_events_events_dates`            `date`        ON `date`       .`event_id`       = `event`      .`id`\nWHERE `buyer_id` = @user_id'
  WHERE `name` = 'Orders' AND `sql` LIKE '%plugin_events_events%' AND `delete`=0;

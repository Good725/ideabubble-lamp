/*
ts:2017-01-26 12:45:00
*/

UPDATE plugin_reports_sparklines SET total_field = 'Money' WHERE title='Total Sales';
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `widget_sql`, `publish`, `delete`, `report_type`, `dashboard`) VALUES ('My Live Events', 'SELECT COUNT(DISTINCT e.id) AS qty FROM plugin_events_events e INNER JOIN plugin_events_events_dates d ON e.id = d.event_id WHERE e.deleted = 0 AND d.`ends` >= NOW() AND e.owned_by = @user_id', 'SELECT COUNT(DISTINCT e.id) AS qty FROM plugin_events_events e INNER JOIN plugin_events_events_dates d ON e.id = d.event_id WHERE e.deleted = 0 AND d.`ends` >= NOW() AND e.owned_by = @user_id', '1', '0', 'sql', 1);

UPDATE plugin_reports_sparklines
  SET
    report_id = (select id from plugin_reports_reports where name = 'My Live Events' limit 1),
    total_field = 'qty',
    total_type_id = 6
  WHERE title = 'Total Live Events' AND chart_type_id = 7;

UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n  IFNULL(COALESCE(`order`.total - `order`.commission_total - `order`.vat_total, 0), 0) AS `Money`, \r\n  `order`.`created` AS \'Date\', \r\n  IFNULL(REPLACE(REPLACE(REPLACE(`peop`.`currency`, \'EUR\', \'€\'), \'USD\', \'$\'), \'GBP\', \'£\'), \'€\') as `currency` \r\nFROM \r\n  `plugin_events_orders_payments` `peop` \r\nLEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id` \r\nINNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id` \r\nWHERE `peop`.`status` = \'PAID\' \r\nAND  `account`.`owner_id` = @user_id \r\nAND `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND DATE_ADD({!DASHBOARD-TO!} , INTERVAL 1 DAY)\r\nAND `order`.`deleted` = 0' WHERE (`name`='Total Revenue');

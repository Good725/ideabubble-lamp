/*
ts:2016-08-05 15:02:00
*/

UPDATE plugin_reports_reports
  SET `sql` = 'SELECT `e`.`name` AS `Title`, `d`.`starts` AS `iso_date`, DATE_FORMAT(`d`.`starts`, \'%d %b\') AS `Date`, CONCAT(\'/event/\', `e`.`url`) AS `Link`  \n	FROM plugin_events_orders o \n		INNER JOIN plugin_events_orders_items i ON o.id = i.order_id \n		INNER JOIN plugin_events_orders_items_has_dates hd ON i.id = hd.order_item_id \n		INNER JOIN plugin_events_events_dates d ON hd.date_id = d.id \n		INNER JOIN plugin_events_events e ON d.event_id = e.id \n	WHERE e.deleted = 0 AND o.deleted = 0 AND o.`status` = \'PAID\' AND \n		d.`starts` >= NOW() AND o.buyer_id = @user_id',
    date_modified = NOW()
  WHERE `name` = 'Upcoming Events';

UPDATE plugin_reports_reports
  SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"text-center\"><span style=\"font-size: 2em;\">\', \n		DATEDIFF(`d`.`starts`, CURRENT_TIMESTAMP), \n		\' DAYS</span><br />till<br /><a href=\"/event/\', \n		`e`.`url`, \n		\'\" style=\"text-decoration: underline;color: blue;\">\', \n		`e`.`name`, \n		\'</a></div>\' ) AS \' \' FROM plugin_events_orders o INNER JOIN plugin_events_orders_items i ON o.id = i.order_id INNER JOIN plugin_events_orders_items_has_dates hd ON i.id = hd.order_item_id INNER JOIN plugin_events_events_dates d ON hd.date_id = d.id INNER JOIN plugin_events_events e ON d.event_id = e.id WHERE e.deleted = 0 AND o.deleted = 0 AND o.`status` = \'PAID\' AND d.starts >= NOW() AND o.buyer_id = @user_id ORDER BY `d`.`starts` LIMIT 1',
    date_modified = NOW()
  WHERE `name` = 'Next Event';

UPDATE plugin_reports_reports
  SET `sql` = 'SELECT \n	CONCAT( \n		\'<div class=\"next-event-widget\">\n<div class=\"text-center\"><div class=\"day-count\">\', \n		DATEDIFF(`d`.`starts`, CURRENT_TIMESTAMP), \n		\' DAYS</div> till <a href=\"/event/\', \n		`e`.`url`, \n		\'\">\', \n		`e`.`name`, \n		\'</a></div></div>\' ) AS \' \' FROM plugin_events_orders o INNER JOIN plugin_events_orders_items i ON o.id = i.order_id INNER JOIN plugin_events_orders_items_has_dates hd ON i.id = hd.order_item_id INNER JOIN plugin_events_events_dates d ON hd.date_id = d.id INNER JOIN plugin_events_events e ON d.event_id = e.id WHERE e.deleted = 0 AND o.deleted = 0 AND o.`status` = \'PAID\' AND d.starts >= NOW() AND o.buyer_id = @user_id ORDER BY `d`.`starts` LIMIT 1',
    date_modified = NOW()
  WHERE `name` = 'Next Event';

UPDATE plugin_reports_reports
  SET `sql` = 'SELECT `e`.`name` AS `Title`, `d`.`starts` AS `iso_date`, DATE_FORMAT(`d`.`starts`, \'%e %M %Y\') AS `Date`, CONCAT(\'/event/\', `e`.`url`) AS `Link`  \n	FROM plugin_events_orders o \n		INNER JOIN plugin_events_orders_items i ON o.id = i.order_id \n		INNER JOIN plugin_events_orders_items_has_dates hd ON i.id = hd.order_item_id \n		INNER JOIN plugin_events_events_dates d ON hd.date_id = d.id \n		INNER JOIN plugin_events_events e ON d.event_id = e.id \n	WHERE e.deleted = 0 AND o.deleted = 0 AND o.`status` = \'PAID\' AND \n		d.`starts` >= NOW() AND o.buyer_id = @user_id',
    date_modified = NOW()
  WHERE `name` = 'Upcoming Events';
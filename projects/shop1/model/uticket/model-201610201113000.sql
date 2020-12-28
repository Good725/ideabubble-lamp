/*
ts:2016-10-20 11:30:00
*/

DELETE FROM `engine_role_permissions`
WHERE
  `role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User' LIMIT 1)
AND
  `resource_id` IN (SELECT `id` FROM `engine_resources` WHERE `alias` IN ('contacts2_index_limited', 'contacts2_view_limited'));

UPDATE `engine_plugins_per_role`
SET
  `enabled` = 0
WHERE
	`plugin_id` = (SELECT `id` FROM `engine_plugins` WHERE `name` = 'contacts2' LIMIT 1)
AND
	`role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User' LIMIT 1);

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User' LIMIT 1),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'global_search')
);

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n  CONCAT(
\n    '<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">',
\n    `currency`,
\n    REPLACE(FORMAT(`total`, 2), \",\", \"\"),
\n    '</span><hr /><a href=\"/admin/dashboards/view_dashboard/',
\n    (
\n      SELECT
\n        IFNULL(`id`, '')
\n      FROM
\n        `plugin_dashboards`
\n      WHERE
\n        `title` = 'My Sales'
\n        AND `deleted` = 0
\n    ),
\n    '\" style=\"color: #fff;\">View Dashboard</a></div>'
\n  ) AS ` `
\nFROM
\n  (
\n    SELECT
\n      IFNULL(SUM(COALESCE(`peop`.`amount`, 0)), 0) AS `total`,
\n      REPLACE(REPLACE(REPLACE(`peop`.`currency`, 'EUR', '€'), 'USD', '$'), 'GBP', '£') as `currency`
\n    FROM
\n      `plugin_events_orders_payments` `peop`
\n      LEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id`
\n      INNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\n    WHERE
\n      `peop`.`status` = 'PAID'
\n   AND  `account`.`owner_id` = @user_id
\n	 AND `order`.`deleted` = 0
\n  ) AS `total`;"
WHERE `name` = 'Total Revenue';

UPDATE IGNORE `plugin_reports_reports`
SET `sql` = "SELECT
\n  IFNULL(COALESCE(`peop`.`amount`, 0), 0) AS `Money`,
\n  `order`.`created` AS 'Date',
\n  REPLACE(REPLACE(REPLACE(`peop`.`currency`, 'EUR', '€'), 'USD', '$'), 'GBP', '£') as `currency`
\nFROM
\n  `plugin_events_orders_payments` `peop`
\nLEFT JOIN `plugin_events_orders` `order` ON `peop`.`order_id` = `order`.`id`
\nINNER JOIN `plugin_events_accounts` `account` ON `order`.`account_id` = `account`.`id`
\nWHERE `peop`.`status` = 'PAID'
\nAND  `account`.`owner_id` = @user_id
\nAND `order`.`created` BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}
\nAND `order`.`deleted` = 0 "
WHERE `name` = 'Total Revenue';

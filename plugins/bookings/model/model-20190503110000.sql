/*
ts:2019-05-03 11:00:00
*/

/* Don't show these reports on the dashboard, unless the bookings plugin has been enabled. */
UPDATE
  `plugin_reports_reports`
SET
  `dashboard` = 0
WHERE
  `name` IN ('Attendance by Month', 'Bookings by Month')
AND (SELECT count(*)
  FROM  `engine_plugins_per_role` `ppr`
  JOIN  `engine_plugins`          `plugin` ON `ppr`.`plugin_id` = `plugin`.`id`
  JOIN  `engine_project_role`     `role`   ON `ppr`.`role_id`   = `role`.`id`
  WHERE `plugin`.`name` = 'bookings'
  AND   `role`.`role`   = 'Administrator'
  AND   `ppr`.`enabled` = '1') = 0
;
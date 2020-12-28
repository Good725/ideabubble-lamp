/*
ts:2018-11-01 08:58:00
*/

UPDATE plugin_reports_reports
  SET `sql` = REPLACE (`sql`, "`event`.`url` AS `Link`", "'/admin/events/mytickets' AS `Link`")
  WHERE `name` = 'Orders';

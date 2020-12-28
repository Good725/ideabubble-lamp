/*
ts:2016-07-26 13:51:00
*/

UPDATE plugin_reports_reports
  SET `sql` = REPLACE(`sql`, "'{!DASHBOARD-TO!}' >= e.datetime_start", "DATE_ADD('{!DASHBOARD-TO!}', INTERVAL 1 DAY) > e.datetime_start")
  where `sql` like "%'{!DASHBOARD-TO!}' >= e.datetime_start%";
  

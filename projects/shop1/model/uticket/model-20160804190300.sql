/*
ts:2016-08-04 19:03:00
*/

UPDATE plugin_reports_reports
  SET `sql` = REPLACE (`sql`,"BETWEEN '{!DASHBOARD-FROM!}' AND '{!DASHBOARD-TO!}'", "BETWEEN '{!DASHBOARD-FROM!}' AND DATE_ADD('{!DASHBOARD-TO!}', INTERVAL 1 DAY)")
  WHERE `name` in ('Admin Total Revenue', 'Admin Total Events', 'Admin Total Tickets', 'Admin Total Profit', 'Admin Booking Fee Total');

UPDATE plugin_reports_reports
  SET `sql` = REPLACE (`sql`,"BETWEEN {!DASHBOARD-FROM!} AND {!DASHBOARD-TO!}", "BETWEEN '{!DASHBOARD-FROM!}' AND DATE_ADD('{!DASHBOARD-TO!}', INTERVAL 1 DAY)")
  WHERE `name` in ('Admin Total Revenue', 'Admin Total Events', 'Admin Total Tickets', 'Admin Total Profit', 'Admin Booking Fee Total');

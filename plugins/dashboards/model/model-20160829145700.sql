/*
ts:2016-08-29 14:58:00
*/

update
  plugin_dashboards_gadgets
set
  deleted = 1
where
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') and gadget_id = (SELECT id FROM plugin_reports_reports WHERE name = 'Website Traffic');

UPDATE
  plugin_dashboards_gadgets
SET
  plugin_dashboards_gadgets.`column` = 3, plugin_dashboards_gadgets.`order` = 4
WHERE
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') AND gadget_id = (SELECT id FROM plugin_reports_reports WHERE NAME = 'Admin Total Events');

UPDATE
  plugin_dashboards_gadgets
SET
  plugin_dashboards_gadgets.`column` = 1, plugin_dashboards_gadgets.`order` = 1
WHERE
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') AND gadget_id = (SELECT id FROM plugin_reports_reports WHERE NAME = 'Admin Total Tickets');

UPDATE
  plugin_dashboards_gadgets
SET
  plugin_dashboards_gadgets.`column` = 1, plugin_dashboards_gadgets.`order` = 2
WHERE
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') AND gadget_id = (SELECT id FROM plugin_reports_reports WHERE NAME = 'Admin Total Profit');

UPDATE
  plugin_dashboards_gadgets
SET
  plugin_dashboards_gadgets.`column` = 3, plugin_dashboards_gadgets.`order` = 5
WHERE
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') AND gadget_id = (SELECT id FROM plugin_reports_reports WHERE NAME = 'Admin Booking Fee Total');

UPDATE
  plugin_dashboards_gadgets
SET
  plugin_dashboards_gadgets.`column` = 2, plugin_dashboards_gadgets.`order` = 5
WHERE
  dashboard_id = (SELECT id FROM plugin_dashboards WHERE title = 'Admin') AND gadget_id = (SELECT id FROM plugin_reports_reports WHERE NAME = 'Admin Total Revenue');
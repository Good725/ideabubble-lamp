/*
ts:2019-02-04 11:24:00
*/


INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `x_axis`, `y_axis`, `total_field`, `total_type_id`)
(select 'Outstanding', id, '7', 'Category', 'Outstanding', 'Outstanding', '6' from plugin_reports_reports where name='Outstanding By Category');

INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `html`, `extra_text`, `fill_color`) VALUES ('Outstanding By Category', '2', 'Category', 'Outstanding', '#00c7ef', '<h2>Bookings</h2>\r\n\r\n<p><a href=\"/admin/bookings\">View all bookings</a></p>', '#00c7ef');

UPDATE plugin_reports_reports SET widget_id=(select id from plugin_reports_widgets where name='Outstanding By Category') WHERE name='Outstanding By Category';

INSERT INTO `plugin_dashboards_gadgets`
  (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`)
  (select plugin_dashboards.id, plugin_reports_reports.id, 1, 0, 1 from plugin_dashboards, plugin_reports_reports where title='Welcome' and name = 'Outstanding By Category');

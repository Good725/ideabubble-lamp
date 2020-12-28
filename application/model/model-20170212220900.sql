/*
ts:2017-02-12 22:09:00
*/

/*add widget for total sales if not exist already*/
INSERT INTO `plugin_reports_widgets`
  (`name`, `type`, `x_axis`, `y_axis`, `publish`, `delete`)
  (select 'Total Sales', '2', 'Date', 'Money', '1', '0' from plugin_reports_reports r left join plugin_reports_widgets w on r.widget_id = w.id where r.`name` = 'Total Sales' and w.id is null);

UPDATE plugin_reports_reports
  SET widget_id = (select id from plugin_reports_widgets where name = 'Total Sales')
  WHERE name = 'Total Sales';

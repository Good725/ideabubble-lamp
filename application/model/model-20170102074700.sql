/*
ts:2017-01-02 07:47:00
*/

CREATE TABLE engine_errorlog
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `type`  ENUM('PHP', 'SQL', 'HTTP') NOT NULL,
  file  VARCHAR(100),
  line  INT,
  host  VARCHAR(100),
  url VARCHAR(200),
  referer VARCHAR(200),
  post  MEDIUMTEXT,
  `get` MEDIUMTEXT,
  cookie MEDIUMTEXT,
  server MEDIUMTEXT,
  session MEDIUMTEXT,
  env MEDIUMTEXT,
  trace MEDIUMTEXT,
  dt  DATETIME,
  ip  VARCHAR(100),
  browser VARCHAR(100),
  details MEDIUMTEXT
)
ENGINE = MYISAM
CHARSET = UTF8;

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'Database Errors',
		`type` = 10,
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'PHP Errors',
		`type` = 10,
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_widgets`
	SET
		`name` = 'HTTP Errors',
		`type` = 10,
		`publish` = 1,
		`delete` = 0;

INSERT INTO `plugin_reports_reports`
  (`name`, `sql`, `widget_sql`, `category`, `sub_category`, `dashboard`, `publish`, `delete`, `report_type`, widget_id, action_button, action_button_label, action_event)
  VALUES
  ('Database Errors', 'SELECT \r\n        file, line, dt, ip, url, host, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'SQL\'\r\n    ORDER BY `dt` DESC', 'SELECT \r\n        dt AS `Date`, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'SQL\'\r\n    ORDER BY `dt` DESC', '0', '0', '0', '1', '0', 'sql', (select id from plugin_reports_widgets where name = 'Database Errors' limit 1), 1, 'Clear Errors', '$.post(\n\'/admin/settings/clear_errorlog\',\n{type:\'SQL\'},\nfunction(response){\nalert(\'cleared\');\n}\n);\n');


INSERT INTO `plugin_reports_reports`
  (`name`, `sql`, `widget_sql`, `category`, `sub_category`, `dashboard`, `publish`, `delete`, `report_type`, widget_id, action_button, action_button_label, action_event)
  VALUES
  ('PHP Errors', 'SELECT \r\n        file, line, dt, ip, url, host, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'PHP\'\r\n    ORDER BY `dt` DESC', 'SELECT \r\n        dt AS `Date`, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'PHP\'\r\n    ORDER BY `dt` DESC', '0', '0', '0', '1', '0', 'sql', (select id from plugin_reports_widgets where name = 'PHP Errors' limit 1), 1, 'Clear Errors', '$.post(\n\'/admin/settings/clear_errorlog\',\n{type:\'PHP\'},\nfunction(response){\nalert(\'cleared\');\n}\n);\n');

INSERT INTO `plugin_reports_reports`
  (`name`, `sql`, `widget_sql`, `category`, `sub_category`, `dashboard`, `publish`, `delete`, `report_type`, widget_id, action_button, action_button_label, action_event)
  VALUES
  ('HTTP Errors', 'SELECT \r\n        dt, ip, url, host, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'HTTP\'\r\n    ORDER BY `dt` DESC', 'SELECT \r\n        dt AS `Date`, url, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'HTTP\'\r\n    ORDER BY `dt` DESC', '0', '0', '0', '1', '0', 'sql', (select id from plugin_reports_widgets where name = 'HTTP Errors' limit 1), 1, 'Clear Errors', '$.post(\n\'/admin/settings/clear_errorlog\',\n{type:\'HTTP\'},\nfunction(response){\nalert(\'cleared\');\n}\n);\n');

INSERT INTO plugin_dashboards (title, columns, date_filter, publish, deleted) VALUES ('System', 3, 0, 1, 0);

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (select id from plugin_dashboards where `title` = 'System'), id FROM engine_project_role WHERE `role` IN ('Administrator', 'Super User'));

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'System'), (select id from plugin_reports_reports where `name` = 'Database Errors' limit 1), 1, 1, null, 1, 0);

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'System'), (select id from plugin_reports_reports where `name` = 'PHP Errors' limit 1), 1, 2, null, 1, 0);

INSERT INTO plugin_dashboards_gadgets
  (dashboard_id, gadget_id, type_id, `column`, `order`, `publish`, `deleted`)
  VALUES
  ((select id from plugin_dashboards where `title` = 'System'), (select id from plugin_reports_reports where `name` = 'HTTP Errors' limit 1), 1, 3, null, 1, 0);


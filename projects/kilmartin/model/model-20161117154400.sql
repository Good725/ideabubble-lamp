/*
ts:2016-11-17 15:44:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Manager', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (select id from plugin_dashboards where `title` = 'Manager'), id FROM engine_project_role WHERE `role` IN ('Administrator', 'Manager'));

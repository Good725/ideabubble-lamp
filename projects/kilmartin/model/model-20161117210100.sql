/*
ts:2016-11-17 21:01:00
*/

INSERT INTO plugin_dashboards
  (title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
  VALUES
  ('Supervisor', '', 3, 1, NOW(), NOW(), 1, 1, 1, 0);

INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (SELECT (select id from plugin_dashboards where `title` = 'Supervisor'), id FROM engine_project_role WHERE `role` IN ('Administrator'));

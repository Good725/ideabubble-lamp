/*
ts:2019-07-11 09:05:00
*/

CREATE TABLE plugin_dashboards_render_cache
(
  dashboard_id  INT NOT NULL,
  user_id INT NOT NULL,
  html MEDIUMTEXT,
  rendered  DATETIME,

  PRIMARY KEY (dashboard_id, user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('dashboards_render_cache', 'Dashboard Render Cache', 'dashboards', '1', '1', '1', '1', '1', 'Dashboard Render Cache', 'toggle_button', 'Dashboards', 'Model_Settings,on_or_off');

INSERT INTO `engine_settings`
  (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
  VALUES
  ('dashboards_render_cache_duration', 'Dashboard Render Cache Duration', 'dashboards', '86400', '86400', '86400', '86400', '86400', 'Dashboard Render Cache Duration', 'text', 'Dashboards', '');

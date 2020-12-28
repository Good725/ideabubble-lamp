/*
ts:2016-09-04 22:42:00
*/

#Insert Traffic sparkline
INSERT INTO `plugin_reports_sparklines`
 (`title`, `report_id`, `chart_type_id`, `type_id`, `x_axis`, `y_axis`, `total_field`, `total_type_id`, `text_color`, `background_color`,
  `width`, `dashboard_link_id`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`)
  SELECT
    'Traffic',
    (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic' ORDER BY `id` DESC LIMIT 1),
    (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' ORDER BY `id` DESC LIMIT 1),
    NULL,
    '',
    '',
    '',
    (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'sum' ORDER BY `id` DESC LIMIT 1),
    'rgb(255, 255, 255)',
    'rgb(56,231,202)',
    0,
    (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Admin' ORDER BY `id` DESC LIMIT 1),
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    '1',
    '0'
  FROM `plugin_reports_sparklines`
  WHERE
    NOT EXISTS (SELECT * FROM `plugin_reports_sparklines`
    WHERE
      `title` = 'Traffic'
      AND `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic' ORDER BY `id` DESC LIMIT 1)
      AND `chart_type_id` = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' ORDER BY `id` DESC LIMIT 1)
      AND `type_id` IS NULL
      AND `x_axis` = ''
      AND `y_axis` = ''
      AND `total_field` = ''
      AND `total_type_id` = (SELECT `id` FROM `plugin_reports_total_types` WHERE `stub` = 'sum' ORDER BY `id` DESC LIMIT 1)
      AND `text_color` = 'rgb(255, 255, 255)'
      AND `background_color` = 'rgb(56,231,202)'
      AND `width` = 0
      AND `dashboard_link_id` = (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Admin' ORDER BY `id` DESC LIMIT 1))
LIMIT 1;

# Move Top web Page widget to Top Web Pages report
UPDATE `plugin_reports_sparklines`
SET `report_id` = (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Top Web Pages' ORDER BY `id` DESC LIMIT 1)
WHERE `title` = 'Top Web Page';

# Delete all reports on dashboard
DELETE FROM plugin_dashboards_gadgets
  WHERE
    dashboard_id = (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Traffic' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1);

# Insert Traffic report
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
  (
    (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Traffic' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
    (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Website Traffic' ORDER BY `id` DESC LIMIT 1),
    (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' AND `deleted` = 0 LIMIT 1),
    '0',
    '1',
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    '1',
    '0'
  );

UPDATE IGNORE `engine_settings`
SET
  `value_live` ='0.23',
  `value_stage`='0.23',
  `value_test` ='0.23',
  `value_dev`  ='0.23'
WHERE `variable`='vat_rate';

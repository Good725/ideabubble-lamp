/*
ts:2016-08-29 15:39:00
*/

INSERT IGNORE INTO `plugin_dashboards_gadgets`
  (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  (
    (SELECT id FROM plugin_dashboards WHERE title = 'My Orders'),
    (SELECT id FROM plugin_reports_reports WHERE name = 'Total Orders'),
    2,
    0,
    1,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    1,
    0);